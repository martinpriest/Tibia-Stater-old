<?php

class ExtractHistory {
    // PROPETERIES
    private $table_name = "extract_history";
    private $conn;

    private $id;
    private $idUser;
    private $idWorld;
    private $fileDownloaded;
    private $executionTime;
    private $onlinePlayers;
    private $highscores;
    private $guilds;

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

    public function getIdUser() : ?int {
        return $this->idUser;
    }
    public function setIdUser(int $idUser) : self {
        if(is_numeric($idUser)) {
            $this->idUser = $idUser;
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

    public function getFileDownloaded() : ?int {
        return $this->fileDownloaded;
    }
    public function setFileDownloaded(int $fileDownloaded) : self {
        if(is_numeric($fileDownloaded)) {
            $this->fileDownloaded = $fileDownloaded;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
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

    // DATABASE OPERATION
    public function create() {
        $query = "INSERT INTO {$this->table_name} SET 
                idUser = :idUser,
                idWorld = :idWorld,
                fileDownloaded = :fileDownloaded,
                executionTime = :executionTime,
                onlinePlayers = :onlinePlayers,
                highscores = :highscores,
                guilds = :guilds";
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