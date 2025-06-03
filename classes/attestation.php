<?php
require_once __DIR__ . '/../config/database.php';

class Attestation {
    private $conn;
    private $table_name = "attestation";

    public $ID_ATTESTATION;
    public $ID_EMP;
    public $MOTIF;
    public $DATE_DEMANDE;
    public $STATUT;
    public $DATE_ATTESTATION;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (ID_EMP, MOTIF, DATE_DEMANDE, STATUT) 
                  VALUES (:id_emp, :motif, :date_demande, 'En attente')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_emp", $data['ID_EMP']);
        $stmt->bindParam(":motif", $data['MOTIF']);
        $stmt->bindParam(":date_demande", $data['DATE_DEMANDE']);
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY DATE_DEMANDE DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ID_ATTESTATION = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $date_attestation = date('Y-m-d');
        $query = "UPDATE " . $this->table_name . " 
                  SET STATUT = :status, DATE_ATTESTATION = :date_attestation 
                  WHERE ID_ATTESTATION = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":date_attestation", $date_attestation);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function getByEmployee($id_emp) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ID_EMP = :id_emp ORDER BY DATE_DEMANDE DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_emp", $id_emp);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
