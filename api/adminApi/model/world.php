<?php
class World {
    // Propeteries
    private $conn;
    private $table_name = "worlds";

    private $id;
    private $name;
    private $location;
    private $lastOperation;

    private $onlinePlayersReady;
    private $highscoresReady;
    private $guildsReady;

    // Constructors
    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
    }
    // GETTERS AND SETTERS
    // World ID
    public function getId(): ?int {
        return $this->id;
    }
    public function setId(int $id): self {
        if(is_numeric($id)) {
            $this->id = $id;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }
    // World name
    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        if (empty($name)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Wprowadź name!")));
        } else if (is_numeric($name)) {
            http_response_code(400);
            exit(json_encode(array("message" => "name nie może być liczbą!")));
        } else if (strlen($name) < 3 || strlen($name) > 32) {
            http_response_code(400);
            exit(json_encode(array("message" => "name musi mieć od 3 do 32 znaków!")));
        } else {
            $this->name = $name;
            return $this;
        }
    }
    // Location
    public function getLocation(): ?string {
        return $this->location;
    }

    public function setLocation(string $location): self {
        if (empty($location)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Wprowadź name!")));
        } else if (is_numeric($location)) {
            http_response_code(400);
            exit(json_encode(array("message" => "name nie może być liczbą!")));
        } else if (strlen($location) < 3 || strlen($location) > 32) {
            http_response_code(400);
            exit(json_encode(array("message" => "name musi mieć od 6 do 32 znaków!")));
        } else {
            $this->location = $location;
            return $this;
        }
    }
    // Last operation
    public function getLastOperation(): ?int {
        return $this->lastOperation;
    }
    public function setLastOperation(int $lastOperation): self {
        if(is_numeric($lastOperation)) {
            $this->lastOperation = $lastOperation;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    public function getOnlinePlayersReady(): ?int {
        return $this->onlinePlayersReady;
    }
    public function setOnlinePlayersReady(int $onlinePlayersReady): self {
        if(is_numeric($onlinePlayersReady)) {
            $this->onlinePlayersReady = $onlinePlayersReady;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    public function getHighscoresReady(): ?int {
        return $this->highscoresReady;
    }
    public function setHighscoresReady(int $highscoresReady): self {
        if(is_numeric($highscoresReady)) {
            $this->highscoresReady = $highscoresReady;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    public function getGuildsReady(): ?int {
        return $this->guildsReady;
    }
    public function setGuildsReady(int $guildsReady): self {
        if(is_numeric($guildsReady)) {
            $this->guildsReady = $guildsReady;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    // Add new world
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET name = :name,
                    location = :location,
                    lastOperation = 0";
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":location", $this->location);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    // Read all world for view
    public function read() {
        $query = "SELECT * FROM {$this->table_name}";
        $stmt = $this->conn->prepare($query);
        if($stmt->execute()) {
            return $stmt->fetchAll();
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }
    // Read world by id
    public function readById() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->name = $row['name'];
            $this->location = $row['location'];
            $this->lastOperation = $row['lastOperation'];
            $this->onlinePlayersReady = $row['onlinePlayersReady'];
            $this->highscoresReady = $row['highscoresReady'];
            $this->guildsReady = $row['guildsReady'];
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }
    // Read world by name
    public function readByName() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE name = :name";
        $stmt = $this->conn->prepare($query);
        $this->name = htmlspecialchars(strip_tags($this->name));
        $stmt->bindParam(":name", $this->name);
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->location = $row['location'];
            $this->lastOperation = $row['lastOperation'];
            $this->onlinePlayersReady = $row['onlinePlayersReady'];
            $this->highscoresReady = $row['highscoresReady'];
            $this->guildsReady = $row['guildsReady'];
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__ . ", ")));
        }
    }
    // Update world
    public function update() {
        $query = "UPDATE {$this->table_name}
                SET name = :name,
                    location = :location,
                    lastOperation = :lastOperation,
                    onlinePlayersReady = :onlinePlayersReady,
                    highscoresReady = :highscoresReady
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->lastOperation = htmlspecialchars(strip_tags($this->lastOperation));
        $this->onlinePlayersReady = htmlspecialchars(strip_tags($this->onlinePlayersReady));
        $this->highscoresReady = htmlspecialchars(strip_tags($this->highscoresReady));
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":lastOperation", $this->lastOperation);
        $stmt->bindParam(":onlinePlayersReady", $this->onlinePlayersReady);
        $stmt->bindParam(":highscoresReady", $this->highscoresReady);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}