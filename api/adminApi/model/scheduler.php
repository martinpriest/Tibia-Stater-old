<?php

class ExtractHistory {
    // PROPETERIES
    private $table_name = "scheduler";
    private $conn;

    private $id;
    private $idWorld;
    private $onlinePlayers;
    private $highscores;
    private $guilds;
    private $done;
    private $time; // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! !

    // CONSTRUCTOR
    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
        else {
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
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
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
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
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    public function getOnlinePlayers() : ?bool {
        return $this->onlinePlayers;
    }
    public function setOnlinePlayers(bool $onlinePlayers) : self {
        if(is_bool($onlinePlayers)) {
            $this->onlinePlayers = $onlinePlayers;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    public function getHighscores() : ?bool {
        return $this->highscores;
    }
    public function setHighscores(bool $highscores) : self {
        if(is_bool($highscores)) {
            $this->highscores = $highscores;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    public function getGuilds() : ?bool {
        return $this->guilds;
    }
    public function setGuilds(bool $guilds) : self {
        if(is_bool($guilds)) {
            $this->guilds = $guilds;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    public function getDone() : ?bool {
        return $this->done;
    }
    public function setDone(bool $done) : self {
        if(is_bool($done)) {
            $this->done = $done;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    public function getTime() {
        return $this->time;
    }
    public function setTime(string $time) : self {
        if(is_string($time)) {
            $this->time = date('H:i:s', strtotime($time));
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // Create new schedule task
    public function create() {
        $query = "INSERT INTO {$this->table_name} SET 
                idUser = :idUser,
                idWorld = :idWorld,
                fileDownloaded = :fileDownloaded,
                executionTime = :executionTime,
                onlinePlayers = :onlinePlayers,
                highscores = :highscores,
                guilds = :guilds,
                time = :time";
        // przygotuj zapytanie
        $stmt = $this->conn->prepare($query);
        // sanityzacja zmiennych obiektu
        $this->idUser=htmlspecialchars(strip_tags($this->idUser));
        $this->idWorld=htmlspecialchars(strip_tags($this->idWorld));
        $this->fileDownloaded=htmlspecialchars(strip_tags($this->fileDownloaded));
        $this->executionTime=htmlspecialchars(strip_tags($this->executionTime));
        $this->onlinePlayers=htmlspecialchars(strip_tags($this->onlinePlayers));
        $this->highscores=htmlspecialchars(strip_tags($this->highscores));
        $this->guilds=htmlspecialchars(strip_tags($this->guilds));
        //wstawianie zmiennych obiketu do zapytania
        $stmt->bindParam(':idUser', $this->idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idWorld', $this->idWorld, PDO::PARAM_INT);
        $stmt->bindParam(':fileDownloaded', $this->fileDownloaded, PDO::PARAM_INT);
        $stmt->bindParam(':executionTime', $this->executionTime);
        $stmt->bindParam(':onlinePlayers', $this->onlinePlayers, PDO::PARAM_BOOL);
        $stmt->bindParam(':highscores', $this->highscores, PDO::PARAM_BOOL);
        $stmt->bindParam(':guilds', $this->guilds, PDO::PARAM_BOOL);
        $stmt->bindParam(':time', $this->time);
        //jesli zapytanie sie wykona poprawnie zwroc true
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }
    // Read all schedule task for load view
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
    // Read schedule task by id for updating
    public function readById() :bool {
        $query = "SELECT * FROM {$this->table_name}
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam("id", $this->id);
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->$idWorld = $row['idWorld'];
            $this->$onlinePlayers = $row['onlinePlayers'];
            $this->$highscores = $row['highscores'];
            $this->$guilds = $row['guilds'];
            $this->$done = $row['done'];
            $this->$time = $row['time'];
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }
    // Update schedule task by id
    public function update() :bool {
        $query = "UPDATE {$this->table_name}
                SET 
                    idUser = :idUser,
                    idWorld = :idWorld,
                    fileDownloaded = :fileDownloaded,
                    executionTime = :executionTime,
                    onlinePlayers = :onlinePlayers,
                    highscores = :highscores,
                    guilds = :guilds,
                    time = :time
                WHERE id = :id";
        // przygotuj zapytanie
        $stmt = $this->conn->prepare($query);
        // sanityzacja zmiennych obiektu
        $this->idUser=htmlspecialchars(strip_tags($this->idUser));
        $this->idWorld=htmlspecialchars(strip_tags($this->idWorld));
        $this->fileDownloaded=htmlspecialchars(strip_tags($this->fileDownloaded));
        $this->executionTime=htmlspecialchars(strip_tags($this->executionTime));
        $this->onlinePlayers=htmlspecialchars(strip_tags($this->onlinePlayers));
        $this->highscores=htmlspecialchars(strip_tags($this->highscores));
        $this->guilds=htmlspecialchars(strip_tags($this->guilds));
        //wstawianie zmiennych obiketu do zapytania
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':idUser', $this->idUser, PDO::PARAM_INT);
        $stmt->bindParam(':idWorld', $this->idWorld, PDO::PARAM_INT);
        $stmt->bindParam(':fileDownloaded', $this->fileDownloaded, PDO::PARAM_INT);
        $stmt->bindParam(':executionTime', $this->executionTime);
        $stmt->bindParam(':onlinePlayers', $this->onlinePlayers, PDO::PARAM_BOOL);
        $stmt->bindParam(':highscores', $this->highscores, PDO::PARAM_BOOL);
        $stmt->bindParam(':guilds', $this->guilds, PDO::PARAM_BOOL);
        $stmt->bindParam(':time', $this->time);
        //jesli zapytanie sie wykona poprawnie zwroc true
        if($stmt->execute()) {
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }
    // Delete schedule task by id
    public function delete() {
        $query = "DELETE FROM {$this->table_name}
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam('id', $this->id);
        if($stmt->execute()) {
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }
}