<?php

class PlayerTransaction {
    private $conn;
    private $table_name = "players_transaction";

    private $id;
    private $idTime;
    private $idWorld;
    private $idPlayer;
    private $idResidence;
    private $level;
    private $achievmentPoint;
    private $timeOnline;

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

    // idTime
    public function getIdTime(): ?int {
        return $this->idTime;
    }
    public function setIdTime(int $idTime): self {
        if (is_numeric($idTime)) {
            $this->idTime = $idTime;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidTimelowy format danych")));
        }
    }

    // idWorld
    public function getIdWorld(): ?int {
        return $this->idWorld;
    }
    public function setIdWorld(int $idWorld): self {
        if (is_numeric($idWorld)) {
            $this->idWorld = $idWorld;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidWorldlowy format danych")));
        }
    }

    // idPlayer
    public function getIdPlayer(): ?int {
        return $this->idPlayer;
    }
    public function setIdPlayer(int $idPlayer): self {
        if (is_numeric($idPlayer)) {
            $this->idPlayer = $idPlayer;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidPlayerlowy format danych")));
        }
    }

    // idResidence
    public function getIdResidence(): ?int {
        return $this->idResidence;
    }
    public function setIdResidence(int $idResidence): self {
        if (is_numeric($idResidence)) {
            $this->idResidence = $idResidence;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidResidencelowy format danych")));
        }
    }

    // level
    public function getLevel(): ?int {
        return $this->level;
    }
    public function setLevel(int $level): self {
        if (is_numeric($level)) {
            $this->level = $level;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawlevellowy format danych")));
        }
    }

    // achievmentPoint
    public function getAchievmentPoint(): ?int {
        return $this->achievmentPoint;
    }
    public function setAchievmentPoint(int $achievmentPoint): self {
        if (is_numeric($achievmentPoint)) {
            $this->achievmentPoint = $achievmentPoint;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawachievmentPointlowy format danych")));
        }
    }

    // timeOnline
    public function getTimeOnline(): ?int {
        return $this->timeOnline;
    }
    public function setTimeOnline(int $timeOnline): self {
        if (is_numeric($timeOnline)) {
            $this->timeOnline = $timeOnline;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawtimeOnlinelowy format danych")));
        }
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET idTime = :idTime,
                    idWorld = :idWorld,
                    idPlayer = :idPlayer,
                    idResidence = :idResidence,
                    level = :level,
                    achievmentPoint = :achievmentPoint,
                    timeOnline = :timeOnline";
                    
        $stmt = $this->conn->prepare($query);

        $this->idTime = htmlspecialchars(strip_tags($this->idTime));
        $this->idWorld = htmlspecialchars(strip_tags($this->idWorld));
        $this->idPlayer = htmlspecialchars(strip_tags($this->idPlayer));
        $this->idResidence = htmlspecialchars(strip_tags($this->idResidence));
        $this->level = htmlspecialchars(strip_tags($this->level));
        $this->achievmentPoint = htmlspecialchars(strip_tags($this->achievmentPoint));
        $this->timeOnline = htmlspecialchars(strip_tags($this->timeOnline));

        $stmt->bindParam(':idTime', $this->idTime);
        $stmt->bindParam(':idWorld', $this->idWorld);
        $stmt->bindParam(':idPlayer', $this->idPlayer);
        $stmt->bindParam(':idResidence', $this->idResidence);
        $stmt->bindParam(':level', $this->level);
        $stmt->bindParam(':achievmentPoint', $this->achievmentPoint);
        $stmt->bindParam(':timeOnline', $this->timeOnline);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }

    // read by idPlayer
    public function readAllPlayersByWorld() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE id IN (
                    SELECT MAX(id)
                    FROM {$this->table_name}
                    WHERE idWorld = :idWorld
                    GROUP BY idPlayer);";
        $stmt = $this->conn->prepare($query);

        $this->idWorld = htmlspecialchars(strip_tags($this->idWorld));
        $stmt->bindParam(':idWorld', $this->idWorld);

        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }

    // read by idWorld

    // read by 
}