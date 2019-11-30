<?php

class TransformHistory {
    // PROPETERIES
    private $table_name = "transform_history";
    private $conn;

    private $id;
    private $idUser;
    private $idWorld;
    private $fileParsed;
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

    public function getIdWorld() : ?int {
        return $this->idWorld;
    }
    public function setIdWorld(int $idWorld) : self {
        if(is_numeric($idWorld)) {
            $this->idWorld = $idWorld;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }

    public function getFileParsed() : ?int {
        return $this->fileParsed;
    }
    public function setFileParsed(int $fileParsed) : self {
        if(is_numeric($fileParsed)) {
            $this->fileParsed = $fileParsed;
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
                idWorld = :idWorld,
                fileParsed = :fileParsed,
                executionTime = :executionTime";
        // przygotuj zapytanie
        $stmt = $this->conn->prepare($query);
        // sanityzacja zmiennych obiektu
        $this->idUser=htmlspecialchars(strip_tags($this->idUser));
        $this->idWorld=htmlspecialchars(strip_tags($this->idWorld));
        $this->fileParsed=htmlspecialchars(strip_tags($this->fileParsed));
        $this->executionTime=htmlspecialchars(strip_tags($this->executionTime));
        //wstawianie zmiennych obiketu do zapytania
        $stmt->bindParam(':idUser', $this->idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idWorld', $this->idWorld, PDO::PARAM_INT);
        $stmt->bindParam(':fileParsed', $this->fileParsed, PDO::PARAM_INT);
        $stmt->bindParam(':executionTime', $this->executionTime);
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