<?php

class LoadHistory {
    // PROPETERIES
    private $table_name = "load_history";
    private $conn;

    private $id;
    private $idUser;
    private $recordsInserted;
    private $recordsUpdated;
    private $executionTime;

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

    public function getRecordsInserted() : ?int {
        return $this->recordsInserted;
    }
    public function setRecordsInserted(int $recordsInserted) : self {
        if(is_numeric($recordsInserted)) {
            $this->recordsInserted = $recordsInserted;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }

    public function getRecordsUpdated() : ?int {
        return $this->recordsUpdated;
    }
    public function setRecordsUpdated(int $recordsUpdated) : self {
        if(is_numeric($recordsUpdated)) {
            $this->recordsUpdated = $recordsUpdated;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }

    public function getExecutionTime() : ?float {
        return $this->executionTime;
    }
    public function setExecutionTime(float $executionTime) : self {
        if(is_numeric($executionTime)) {
            $this->executionTime = $executionTime;
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
                recordsInserted = :recordsInserted,
                recordsUpdated = :recordsUpdated,
                executionTime = :executionTime";
        // przygotuj zapytanie
        $stmt = $this->conn->prepare($query);
        // sanityzacja zmiennych obiektu
        $this->idUser=htmlspecialchars(strip_tags($this->idUser));
        $this->recordsInserted=htmlspecialchars(strip_tags($this->recordsInserted));
        $this->recordsUpdated=htmlspecialchars(strip_tags($this->recordsUpdated));
        $this->executionTime=htmlspecialchars(strip_tags($this->executionTime));
        //wstawianie zmiennych obiketu do zapytania
        $stmt->bindParam(':idUser', $this->idUser, PDO::PARAM_INT);
        $stmt->bindParam(':recordsInserted', $this->recordsInserted, PDO::PARAM_INT);
        $stmt->bindParam(':recordsUpdated', $this->recordsUpdated, PDO::PARAM_INT);
        $stmt->bindParam(':executionTime', $this->executionTime);
        //jesli zapytanie sie wykona poprawnie zwroc true
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // DODAC METODY read DO PRZEGLADANIA
}