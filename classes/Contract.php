<?php
require_once __DIR__ . '/../config/database.php';

class Contract {
    private $conn;
    private $table_name = "contrat";

    public $N__CONTRAT;
    public $ID_EMP;
    public $TYPE_CONTRAT;
    public $FONCTION;
    public $QUALIFICATION;
    public $CATEGORIE;
    public $ECHLAN;
    public $NBR_H_MOIS;
    public $NBR_H___JOURS;
    public $TYPE_DE_PAIE;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

  
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                (ID_EMP, TYPE_CONTRAT, FONCTION, QUALIFICATION, CATEGORIE, 
                ECHLAN, NBR_H_MOIS, NBR_H___JOURS, TYPE_DE_PAIE)
                VALUES
                (:id_emp, :type_contrat, :fonction, :qualification, :categorie,
                :echelon, :nbr_h_mois, :nbr_h_jours, :type_paie)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_emp", $data['id_emp']);
        $stmt->bindParam(":type_contrat", $data['type_contrat']);
        $stmt->bindParam(":fonction", $data['fonction']);
        $stmt->bindParam(":qualification", $data['qualification']);
        $stmt->bindParam(":categorie", $data['categorie']);
        $stmt->bindParam(":echelon", $data['echelon']);
        $stmt->bindParam(":nbr_h_mois", $data['nbr_h_mois']);
        $stmt->bindParam(":nbr_h_jours", $data['nbr_h_jours']);
        $stmt->bindParam(":type_paie", $data['type_paie']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function read($id) {
        $query = "SELECT c.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " c
                 JOIN employee e ON c.ID_EMP = e.ID_EMP
                 WHERE c.N__CONTRAT = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . "
                SET TYPE_CONTRAT = :type_contrat,
                    FONCTION = :fonction,
                    QUALIFICATION = :qualification,
                    CATEGORIE = :categorie,
                    ECHLAN = :echelon,
                    NBR_H_MOIS = :nbr_h_mois,
                    NBR_H___JOURS = :nbr_h_jours,
                    TYPE_DE_PAIE = :type_paie
                WHERE N__CONTRAT = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":type_contrat", $data['type_contrat']);
        $stmt->bindParam(":fonction", $data['fonction']);
        $stmt->bindParam(":qualification", $data['qualification']);
        $stmt->bindParam(":categorie", $data['categorie']);
        $stmt->bindParam(":echelon", $data['echelon']);
        $stmt->bindParam(":nbr_h_mois", $data['nbr_h_mois']);
        $stmt->bindParam(":nbr_h_jours", $data['nbr_h_jours']);
        $stmt->bindParam(":type_paie", $data['type_paie']);
        $stmt->bindParam(":id", $data['id']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE N__CONTRAT = :id";
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
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT c.*, e.NOM_EMP, e.PRENOM_EMP 
                 FROM " . $this->table_name . " c
                 JOIN employee e ON c.ID_EMP = e.ID_EMP";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 