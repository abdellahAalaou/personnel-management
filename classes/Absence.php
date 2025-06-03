<?php
require_once __DIR__ . '/../config/database.php';

class Absence {
    private $conn;
    private $table_name = "absence";

    public $N__ABS;
    public $ID_EMP;
    public $DUREE_ABS;
    public $DATE;
    public $MOTIF;
    public $JUSTIF;

    public function __construct() {
        try {
            $database = new Database();
            $this->conn = $database->getConnection();
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                    (ID_EMP, DUREE_ABS, DATE, MOTIF, JUSTIF)
                    VALUES
                    (:id_emp, :duree_abs, :date, :motif, :justif)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id_emp", $this->ID_EMP);
            $stmt->bindParam(":duree_abs", $this->DUREE_ABS);
            $stmt->bindParam(":date", $this->DATE);
            $stmt->bindParam(":motif", $this->MOTIF);
            $stmt->bindParam(":justif", $this->JUSTIF);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Absence creation error: " . $e->getMessage());
            return false;
        }
    }

    public function getByEmployee($id_emp) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE ID_EMP = :id_emp";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id_emp", $id_emp);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get absences error: " . $e->getMessage());
            return false;
        }
    }

    public function update() {
        try {
            $query = "UPDATE " . $this->table_name . "
                    SET DUREE_ABS = :duree_abs,
                        DATE = :date,
                        MOTIF = :motif,
                        JUSTIF = :justif
                    WHERE N__ABS = :n_abs";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":duree_abs", $this->DUREE_ABS);
            $stmt->bindParam(":date", $this->DATE);
            $stmt->bindParam(":motif", $this->MOTIF);
            $stmt->bindParam(":justif", $this->JUSTIF);
            $stmt->bindParam(":n_abs", $this->N__ABS);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Absence update error: " . $e->getMessage());
            return false;
        }
    }

    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE N__ABS = :n_abs";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":n_abs", $this->N__ABS);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Absence deletion error: " . $e->getMessage());
            return false;
        }
    }

    
    public function read($id) {
        $query = "SELECT a.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " a
                 JOIN employee e ON a.ID_EMP = e.ID_EMP
                 WHERE a.N__ABS = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAll() {
        $query = "SELECT a.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " a
                 JOIN employee e ON a.ID_EMP = e.ID_EMP";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 