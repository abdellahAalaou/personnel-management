<?php
require_once __DIR__ . '/../config/database.php';

class Salary {
    private $conn;
    private $table_name = "salaire";

    public $ID_SLR;
    public $ID_EMP;
    public $DATE_PAIEMENT;
    public $MONTANT;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                (ID_EMP, DATE_PAIEMENT, MONTANT)
                VALUES
                (:id_emp, :date_paiement, :montant)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_emp", $data['id_emp']);
        $stmt->bindParam(":date_paiement", $data['date_paiement']);
        $stmt->bindParam(":montant", $data['montant']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read($id) {
        $query = "SELECT s.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " s
                 JOIN employee e ON s.ID_EMP = e.ID_EMP
                 WHERE s.ID_SLR = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . "
                SET DATE_PAIEMENT = :date_paiement,
                    MONTANT = :montant
                WHERE ID_SLR = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":date_paiement", $data['date_paiement']);
        $stmt->bindParam(":montant", $data['montant']);
        $stmt->bindParam(":id", $data['id']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID_SLR = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getByEmployee($id_emp) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ID_EMP = :id_emp ORDER BY DATE_PAIEMENT DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_emp", $id_emp);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT s.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " s
                 JOIN employee e ON s.ID_EMP = e.ID_EMP
                 ORDER BY s.DATE_PAIEMENT DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalByEmployee($id_emp) {
        $query = "SELECT SUM(MONTANT) as total FROM " . $this->table_name . " WHERE ID_EMP = :id_emp";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_emp", $id_emp);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_payments,
                    SUM(MONTANT) as total_amount,
                    AVG(MONTANT) as average_salary,
                    MIN(MONTANT) as min_salary,
                    MAX(MONTANT) as max_salary
                 FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function deleteByEmployeeId($id_emp) {
        $sql = "DELETE FROM salaire WHERE ID_EMP = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_emp]);
    }
} 