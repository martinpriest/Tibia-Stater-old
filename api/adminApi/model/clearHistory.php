<?php

class ClearHistory {
    // PROPETERIES
    private $table_name = "clear_history";
    private $conn;

    private $id;
    private $idUser;
    private $recordDeleted;

    // CONSTRUCTOR
    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
        else {
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }

    // GETTERS AND SETTERS
    public function getId() : ?int {
        return $this->id;
    }
    public function setId(int $id): self {
        if(is_numeric($id)) {
            $this->id = $id;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }

    public function getIdUser() : ?int {
        return $this->idUser;
    }
    public function setIdUser(int $idUser) : self {
        if(is_numeric($idUser)) {
            $this->idUser = $idUser;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }

    public function getRecordDeleted() : ?int {
        return $this->recordDeleted;
    }
    public function setRecordDeleted(int $recordDeleted) : self {
        if(is_numeric($recordDeleted)) {
            $this->recordDeleted = $recordDeleted;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }

    // DATABASE OPERATION
    public function create() {
        $query = "INSERT INTO {$this->table_name} SET 
                idUser = :idUser,
                recordDeleted = :recordDeleted";
        // przygotuj zapytanie
        $stmt = $this->conn->prepare($query);
        // sanityzacja zmiennych obiektu
        $this->idUser=htmlspecialchars(strip_tags($this->idUser));
        $this->recordDeleted=htmlspecialchars(strip_tags($this->recordDeleted));
        //wstawianie zmiennych obiketu do zapytania
        $stmt->bindParam(':idUser', $this->idUser, PDO::PARAM_INT);
        $stmt->bindParam(':recordDeleted', $this->recordDeleted, PDO::PARAM_INT);
        //jesli zapytanie sie wykona poprawnie zwroc true
        if($stmt->execute()) {
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // DODAC METODY read DO PRZEGLADANIA
}