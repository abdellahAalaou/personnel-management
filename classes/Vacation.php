<?php
require_once __DIR__ . '/../config/database.php';

class Vacation {
    private $conn;
    private $table_name = "conge";

    public $N__CGE;
    public $ID_EMP;
    public $DATE_DEBUT_CGE;
    public $DATE_FIN_CGE;
    

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
        (ID_EMP, DATE_DEBUT_CGE, DATE_FIN_CGE, ETAT)
        VALUES
        (:id_emp, :date_debut, :date_fin, 'en attente')";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_emp", $data['id_emp']);
        $stmt->bindParam(":date_debut", $data['date_debut']);
        $stmt->bindParam(":date_fin", $data['date_fin']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function read($id) {
        $query = "SELECT c.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " c
                 JOIN employee e ON c.ID_EMP = e.ID_EMP
                 WHERE c.N__CGE = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . "
                SET DATE_DEBUT_CGE = :date_debut,
                    DATE_FIN_CGE = :date_fin
                WHERE N__CGE = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":date_debut", $data['date_debut']);
        $stmt->bindParam(":date_fin", $data['date_fin']);
        $stmt->bindParam(":id", $data['id']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE N__CGE = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByEmployee($id_emp) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ID_EMP = :id_emp";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_emp", $id_emp);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT c.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " c
                 JOIN employee e ON c.ID_EMP = e.ID_EMP";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkConflict($id_emp, $date_debut, $date_fin) {
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE ID_EMP = :id_emp 
                 AND (
                     (DATE_DEBUT_CGE BETWEEN :date_debut AND :date_fin)
                     OR (DATE_FIN_CGE BETWEEN :date_debut AND :date_fin)
                     OR (:date_debut BETWEEN DATE_DEBUT_CGE AND DATE_FIN_CGE)
                 )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_emp", $id_emp);
        $stmt->bindParam(":date_debut", $date_debut);
        $stmt->bindParam(":date_fin", $date_fin);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function updateStatus($id, $status) {
        // Start a transaction to keep things safe
        $this->conn->beginTransaction();
    
        // Update the vacation request status
        $query = "UPDATE " . $this->table_name . " SET ETAT = :status WHERE N__CGE = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
    
        if (!$stmt->execute()) {
            $this->conn->rollBack();
            return false;
        }
    
        // If status is 'acceptée', deduct vacation days from employee
        if ($status === 'acceptée') {
            // Get the vacation record info to calculate days and employee ID
            $vacation = $this->read($id);
            if (!$vacation) {
                $this->conn->rollBack();
                return false;
            }
    
            // Deduct vacation days
            $success = $this->deductVacationDays($vacation['ID_EMP'], $vacation['DATE_DEBUT_CGE'], $vacation['DATE_FIN_CGE']);
    
            if (!$success) {
                $this->conn->rollBack();
                return false;
            }
        }
    
        // Commit the transaction if all went well
        $this->conn->commit();
        return true;
    }
    
    // Add this private helper method to your class:
    private function deductVacationDays($id_emp, $start_date, $end_date) {
        // Calculate number of vacation days
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        $days = floor(($end - $start) / (60 * 60 * 24)) + 1;
    
        // Get current remaining vacation days
        $query = "SELECT NOMBRE_JOURS_CONGE FROM employee WHERE ID_EMP = :id_emp";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_emp', $id_emp);
        $stmt->execute();
        $currentDays = $stmt->fetchColumn();
    
        // Calculate new remaining days (no negative)
        $newDays = max(0, $currentDays - $days);
    
        // Update employee table with new remaining days
        $updateQuery = "UPDATE employee SET NOMBRE_JOURS_CONGE = :newDays WHERE ID_EMP = :id_emp";
        $stmtUpdate = $this->conn->prepare($updateQuery);
        $stmtUpdate->bindParam(':newDays', $newDays);
        $stmtUpdate->bindParam(':id_emp', $id_emp);
    
        return $stmtUpdate->execute();
    }
    
    public function getPending() {
            $query = "SELECT * FROM " . $this->table_name . " WHERE ETAT = 'en attente'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getTotalUsedDays($id_emp) {
        $query = "SELECT DATE_DEBUT_CGE, DATE_FIN_CGE FROM conge WHERE ID_EMP = :id AND ETAT = 'acceptée'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_emp);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $total_days = 0;
        foreach ($rows as $row) {
            $start = strtotime($row['DATE_DEBUT_CGE']);
            $end = strtotime($row['DATE_FIN_CGE']);
            $days = floor(($end - $start) / (60*60*24)) + 1; 
            $total_days += $days;
        }
        return $total_days;
    }


 
    
    
}