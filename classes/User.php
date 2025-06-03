<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $conn;
    private $table_name = "employee";

    public $ID_EMP;
    public $NOM_UTILISATEUR;
    public $MOT_DE_PASSE;
    public $ROLES;

    public function __construct() {
        try {
            $database = new Database();
            $this->conn = $database->getConnection();
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function register($data) {
        try {
            if($this->isUsernameExists($data['username'])) {
                error_log("Username already exists: " . $data['username']);
                return false;
            }

            $query = "INSERT INTO " . $this->table_name . "
                    (NOM_UTILISATEUR, MOT_DE_PASSE, ROLES)
                    VALUES
                    (:username, :password, :role)";

            $stmt = $this->conn->prepare($query);

            $password_hash = $data['password'];
            
            error_log("Registering user: " . $data['username']);
            error_log("Password hash: " . $password_hash);
            error_log("Role: " . $data['role']);

            $stmt->bindParam(":username", $data['username']);
            $stmt->bindParam(":password", $password_hash);
            $stmt->bindParam(":role", $data['role']);

            $result = $stmt->execute();
            
            error_log("Registration result: " . ($result ? "success" : "failed"));
            
            return $result;
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE NOM_UTILISATEUR = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();

            error_log("Login attempt for username: " . $username);
            error_log("SQL Query: " . $query);

            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($password == $row['MOT_DE_PASSE']) {
                    return $row;
                }
            } else {
                error_log("No user found with username: " . $username);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function isUsernameExists($username) {
        try {
            $query = "SELECT ID_EMP FROM " . $this->table_name . " WHERE NOM_UTILISATEUR = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Username check error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id, $new_password) {
        try {
            $query = "UPDATE " . $this->table_name . "
                    SET MOT_DE_PASSE = :password
                    WHERE ID_EMP = :id";

            $stmt = $this->conn->prepare($query);

            $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            return false;
        }
    }

    public function updateRole($id, $role) {
        try {
            $query = "UPDATE " . $this->table_name . "
                    SET ROLES = :role
                    WHERE ID_EMP = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":role", $role);
            $stmt->bindParam(":id", $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Role update error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE ID_EMP = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }


    public function getAll() {
        $query = "SELECT e.*, s.NOM_SERVICE
                  FROM " . $this->table_name . " e
                  LEFT JOIN service s ON e.N__SERVICE = s.N__SERVICE";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}


?> 