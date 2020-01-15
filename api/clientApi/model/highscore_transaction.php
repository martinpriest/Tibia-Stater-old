<?php

class HighscoreTransaction {
    private $conn;
    private $table_name = "highscore_transaction";

    private $id;
    private $idTime;
    private $idWorld;
    private $idPlayer;
    private $idVocation;
    private $idHighscoreCategory;
    private $rankPosition;
    private $rankValue;

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

    // idVocation
    public function getIdVocation(): ?int {
        return $this->idVocation;
    }
    public function setIdVocation(int $idVocation): self {
        if (is_numeric($idVocation)) {
            $this->idVocation = $idVocation;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidVocationlowy format danych")));
        }
    }

    // idHighscoreCategory
    public function getIdHighscoreCategory(): ?int {
        return $this->idHighscoreCategory;
    }
    public function setIdHighscoreCategory(int $idHighscoreCategory): self {
        if (is_numeric($idHighscoreCategory)) {
            $this->idHighscoreCategory = $idHighscoreCategory;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawlevellowy format danych")));
        }
    }

    // rankPosition
    public function getRankPosition(): ?int {
        return $this->rankPosition;
    }
    public function setRankPosition(int $rankPosition): self {
        if (is_numeric($rankPosition)) {
            $this->rankPosition = $rankPosition;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawrankPositionlowy format danych")));
        }
    }

    // rankValue
    public function getRankValue(): ?int {
        return $this->rankValue;
    }
    public function setRankValue(int $rankValue): self {
        if (is_numeric($rankValue)) {
            $this->rankValue = $rankValue;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawrankValuelowy format danych")));
        }
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET idTime = :idTime,
                    idWorld = :idWorld,
                    idPlayer = :idPlayer,
                    idVocation = :idVocation,
                    idHighscoreCategory = :idHighscoreCategory,
                    rankPosition = :rankPosition,
                    rankValue = :rankValue";
                    
        $stmt = $this->conn->prepare($query);

        $this->idTime = htmlspecialchars(strip_tags($this->idTime));
        $this->idWorld = htmlspecialchars(strip_tags($this->idWorld));
        $this->idPlayer = htmlspecialchars(strip_tags($this->idPlayer));
        $this->idVocation = htmlspecialchars(strip_tags($this->idVocation));
        $this->idHighscoreCategory = htmlspecialchars(strip_tags($this->idHighscoreCategory));
        $this->rankPosition = htmlspecialchars(strip_tags($this->rankPosition));
        $this->rankValue = htmlspecialchars(strip_tags($this->rankValue));

        $stmt->bindParam(':idTime', $this->idTime);
        $stmt->bindParam(':idWorld', $this->idWorld);
        $stmt->bindParam(':idPlayer', $this->idPlayer);
        $stmt->bindParam(':idVocation', $this->idVocation);
        $stmt->bindParam(':idHighscoreCategory', $this->idHighscoreCategory);
        $stmt->bindParam(':rankPosition', $this->rankPosition);
        $stmt->bindParam(':rankValue', $this->rankValue);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // read by idPlayer

    // read by idWorld

    // read by 
}