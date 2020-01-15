<?php

class WorldLocation {
    private $conn;
    private $table_name = "world_locations";
    
    private $id;
    private $name;

    // konstruktor
    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
    }

    // id
    public function getId(): ?int {
        return $this->id;
    }
    public function setId(int $id): self {
        if (is_numeric($id)) {
            $this->id = $id;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    // Name
    public function getName(): ?string {
        return $this->name;
    }
    public function setName(string $name): self {
        if (empty($name)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Wprowadź name!")));
        } else if (is_numeric($name)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Name nie może być liczbą!")));
        } else if (strlen($name) < 4 || strlen($name) > 64) {
            http_response_code(400);
            exit(json_encode(array("message" => "Name musi mieć od 4 do 32 znaków!")));
        } else {
            $this->name = $name;
            return $this;
        }
    }
    
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET name = :name";
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->idDevice));
        $stmt->bindParam(':name', $this->name);
        if($stmt->execute()) {
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }
    public function readAll() {
        $query = "SELECT * FROM {$this->table_name}";
        $stmt = $this->conn->prepare($query);

        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }

    // public function readById() {

    // }

    // public function readByName() {

    // }

    


}