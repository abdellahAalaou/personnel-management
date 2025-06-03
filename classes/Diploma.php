<?php
require_once __DIR__ . '/../config/database.php';

class Diploma {
    private $conn;
    private $table_name = "diplome";
    private $employee_diploma_table = "employee_diplome";
    private $type_diploma_table = "type_diplome";

    public $ID_DPL;
    public $ID_TYPE_DPL;
    public $SPECIALITE_DPL;
    public $MENTION_DPL;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                (ID_TYPE_DPL, SPECIALITE_DPL, MENTION_DPL)
                VALUES
                (:type_dpl, :specialite, :mention)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":type_dpl", $data['type_dpl']);
        $stmt->bindParam(":specialite", $data['specialite']);
        $stmt->bindParam(":mention", $data['mention']);

        if($stmt->execute()) {
            $diploma_id = $this->conn->lastInsertId();
            $query = "INSERT INTO " . $this->employee_diploma_table . "
                    (ID_DPL, ID_EMP, DATE_OBTENTION_DIPLOME)
                    VALUES
                    (:diploma_id, :emp_id, :date_obtention)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":diploma_id", $diploma_id);
            $stmt->bindParam(":emp_id", $data['emp_id']);
            $stmt->bindParam(":date_obtention", $data['date_obtention']);
            
            if($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    public function read($id) {
        $query = "SELECT d.*, td.NOM_TYPE_DPL, ed.DATE_OBTENTION_DIPLOME, ed.ID_EMP, e.NOM_EMP, e.PRENOM_EMP
                 FROM " . $this->table_name . " d
                 JOIN " . $this->type_diploma_table . " td ON d.ID_TYPE_DPL = td.ID_TYPE_DPL
                 JOIN " . $this->employee_diploma_table . " ed ON d.ID_DPL = ed.ID_DPL
                 JOIN employee e ON ed.ID_EMP = e.ID_EMP
                 WHERE d.ID_DPL = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


public function getAllTypes() {
    $query = "SELECT ID_TYPE_DPL, NOM_TYPE_DPL FROM type_diplome ORDER BY NOM_TYPE_DPL";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function createTypeDiploma($nomType) {
    $query = "INSERT INTO type_diplome (NOM_TYPE_DPL) VALUES (:nom)";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nom', $nomType, PDO::PARAM_STR);

    if ($stmt->execute()) {
        return $this->conn->lastInsertId();
    }
    return false;
}

public function getTypeDiplomaIdByName($nomType) {
    $nomType = trim($nomType);
    $query = "SELECT ID_TYPE_DPL FROM type_diplome WHERE LOWER(TRIM(NOM_TYPE_DPL)) = LOWER(:nom) LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':nom', $nomType, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['ID_TYPE_DPL'];
    }
    return false;
}



    public function update($data) {
        $query = "UPDATE " . $this->table_name . "
                SET ID_TYPE_DPL = :type_dpl,
                    SPECIALITE_DPL = :specialite,
                    MENTION_DPL = :mention
                WHERE ID_DPL = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":type_dpl", $data['type_dpl']);
        $stmt->bindParam(":specialite", $data['specialite']);
        $stmt->bindParam(":mention", $data['mention']);
        $stmt->bindParam(":id", $data['id']);

        if($stmt->execute()) {
            $query = "UPDATE " . $this->employee_diploma_table . "
                    SET DATE_OBTENTION_DIPLOME = :date_obtention
                    WHERE ID_DPL = :id AND ID_EMP = :emp_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":date_obtention", $data['date_obtention']);
            $stmt->bindParam(":id", $data['id']);
            $stmt->bindParam(":emp_id", $data['emp_id']);
            
            if($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->employee_diploma_table . " WHERE ID_DPL = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            $query = "DELETE FROM " . $this->table_name . " WHERE ID_DPL = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            
            if($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    public function getByEmployee($id_emp) {
        $query = "SELECT d.*, td.NOM_TYPE_DPL, ed.DATE_OBTENTION_DIPLOME
                 FROM " . $this->table_name . " d
                 JOIN " . $this->type_diploma_table . " td ON d.ID_TYPE_DPL = td.ID_TYPE_DPL
                 JOIN " . $this->employee_diploma_table . " ed ON d.ID_DPL = ed.ID_DPL
                 WHERE ed.ID_EMP = :id_emp";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_emp", $id_emp);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTypes() {
        $query = "SELECT * FROM " . $this->type_diploma_table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   public function getAll() {
    $query = "SELECT d.*, 
                     IFNULL(td.NOM_TYPE_DPL, d.ID_TYPE_DPL) AS TYPE_DIPLOME_AFFICHAGE,
                     ed.DATE_OBTENTION_DIPLOME, 
                     e.NOM_EMP, 
                     e.PRENOM_EMP, 
                     ed.ID_EMP
              FROM " . $this->table_name . " d
              LEFT JOIN " . $this->type_diploma_table . " td ON d.ID_TYPE_DPL = td.ID_TYPE_DPL
              JOIN " . $this->employee_diploma_table . " ed ON d.ID_DPL = ed.ID_DPL
              JOIN employee e ON ed.ID_EMP = e.ID_EMP";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
