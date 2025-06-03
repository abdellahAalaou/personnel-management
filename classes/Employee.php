<?php
require_once __DIR__ . '/../config/database.php';

class Employee {
    private $conn;
    private $table_name = "employee";

    public $ID_EMP;
    public $N__SERVICE;
    public $NOM_EMP;
    public $PRENOM_EMP;
    public $DATE_EMP;
    public $TEL_EMP;
    public $EMAIL_EMP;
    public $ADRESSE_EMP;
    public $DATEEMBAUCH_EMP;
    public $NOMBRE_D_ENFANT;
    public $NOM_UTILISATEUR;
    public $MOT_DE_PASSE;
    public $ROLES;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function authenticate($username, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE NOM_UTILISATEUR = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['MOT_DE_PASSE'])) {
                return $row;
            }
        }
        return false;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                (N__SERVICE, NOM_EMP, PRENOM_EMP, DATE_EMP, TEL_EMP, EMAIL_EMP, 
                ADRESSE_EMP, DATEEMBAUCH_EMP, NOMBRE_D_ENFANT, NOM_UTILISATEUR, 
                MOT_DE_PASSE, ROLES)
                VALUES
                (:service_id, :nom, :prenom, :date_emp, :tel, :email, :adresse, 
                :date_embauche, :nbr_enfants, :username, :password, :role)";

        $stmt = $this->conn->prepare($query);

        $password_hash = $data['password'];

        $stmt->bindParam(":service_id", $data['service_id']);
        $stmt->bindParam(":nom", $data['nom']);
        $stmt->bindParam(":prenom", $data['prenom']);
        $stmt->bindParam(":date_emp", $data['date_emp']);
        $stmt->bindParam(":tel", $data['tel']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":adresse", $data['adresse']);
        $stmt->bindParam(":date_embauche", $data['date_embauche']);
        $stmt->bindParam(":nbr_enfants", $data['nbr_enfants']);
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":role", $data['role']);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function read($id) {
        $query = "SELECT e.*, s.NOM_SERVICE 
                 FROM " . $this->table_name . " e
                 LEFT JOIN service s ON e.N__SERVICE = s.N__SERVICE
                 WHERE e.ID_EMP = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($data) {
        $query = "UPDATE " . $this->table_name . "
                SET N__SERVICE = :service_id,
                    NOM_EMP = :nom,
                    PRENOM_EMP = :prenom,
                    DATE_EMP = :date_emp,
                    TEL_EMP = :tel,
                    EMAIL_EMP = :email,
                    ADRESSE_EMP = :adresse,
                    DATEEMBAUCH_EMP = :date_embauche,
                    NOMBRE_D_ENFANT = :nbr_enfants,
                    NOM_UTILISATEUR = :username,
                    NOMBRE_JOURS_CONGE = :jours_conge
                WHERE ID_EMP = :id";
    
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(":service_id", $data['service_id']);
        $stmt->bindParam(":nom", $data['nom']);
        $stmt->bindParam(":prenom", $data['prenom']);
        $stmt->bindParam(":date_emp", $data['date_emp']);
        $stmt->bindParam(":tel", $data['tel']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":adresse", $data['adresse']);
        $stmt->bindParam(":date_embauche", $data['date_embauche']);
        $stmt->bindParam(":nbr_enfants", $data['nbr_enfants']);
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":jours_conge", $data['jours_conge']); 
        $stmt->bindParam(":id", $data['id']);
    
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE ID_EMP = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getAll() {
        $query = "SELECT e.*, s.NOM_SERVICE 
                 FROM " . $this->table_name . " e
                 LEFT JOIN service s ON e.N__SERVICE = s.N__SERVICE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAbsences($id) {
        $query = "SELECT * FROM absence WHERE ID_EMP = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVacations($id) {
        $query = "SELECT * FROM conge WHERE ID_EMP = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContract($id) {
        $query = "SELECT * FROM contrat WHERE ID_EMP = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDiplomas($id) {
        $query = "SELECT d.*, td.NOM_TYPE_DPL, ed.DATE_OBTENTION_DIPLOME 
                 FROM employee_diplome ed
                 JOIN diplome d ON ed.ID_DPL = d.ID_DPL
                 JOIN type_diplome td ON d.ID_TYPE_DPL = td.ID_TYPE_DPL
                 WHERE ed.ID_EMP = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSalaries($id) {
        $query = "SELECT * FROM salaire WHERE ID_EMP = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
      try {
        $stmt = $this->conn->prepare("
            SELECT e.*, s.NOM_SERVICE 
            FROM EMPLOYEE e
            LEFT JOIN SERVICE s ON e.N__SERVICE = s.N__SERVICE
            WHERE e.ID_EMP = :id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in Employee::getById: " . $e->getMessage());
        return null;
    }
    }

}
