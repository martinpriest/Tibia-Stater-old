<?php

class PlayerSexHistory {
    private $conn;
    private $table_name = "player_sex_history";

    private $id;
    private $idTime;
    private $idPlayer;
    private $sex;

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

    // sex
    public function getSex(): ?int {
        return $this->sex;
    }
    public function setSex(int $sex): self {
        if (is_numeric($sex)) {
            $this->sex = $sex;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "NieprawidFormerWorldlowy format danych")));
        }
    }

    // create
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET idTime = :idTime,
                    idPlayer = :idPlayer,
                    sex = :sex";

        $stmt = $this->conn->prepare($query);

        $this->idTime = htmlspecialchars(strip_tags($this->idTime));
        $this->idPlayer = htmlspecialchars(strip_tags($this->idPlayer));
        $this->sex = htmlspecialchars(strip_tags($this->sex));

        $stmt->bindParam(':idTime', $this->idTime);
        $stmt->bindParam(':idPlayer', $this->idPlayer);
        $stmt->bindParam(':sex', $this->sex);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }

    // readByIdPlayer
    public function readByIdPlayer() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE idPlayer = :idPlayer";

        $this->idPlayer = htmlspecialchars(strip_tags($this->idPlayer));
        $stmt->bindParam(':idPlayer', $this->idPlayer);

        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }

}