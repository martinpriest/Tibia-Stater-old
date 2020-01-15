<?php
class Player {
    private $conn;
    private $table_name = "players";

    private $id;
    private $idWorld;
    private $name;
    private $idVocation;
    private $title;
    private $sex;
    private $status;

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
        } else if (strlen($name) < 2 || strlen($name) > 32) {
            http_response_code(400);
            exit(json_encode(array("message" => "Name musi mieć od 4 do 32 znaków!")));
        } else {
            $this->name = $name;
            return $this;
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

    // Title
    public function getTitle(): ?string {
        return $this->title;
    }
    public function setTitle(string $title): self {
        if (empty($title)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Wprowadź title!")));
        } else if (is_numeric($title)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Title nie może być liczbą!")));
        } else if (strlen($title) < 4 || strlen($title) > 64) {
            http_response_code(400);
            exit(json_encode(array("message" => "Title musi mieć od 4 do 32 znaków!")));
        } else {
            $this->title = $title;
            return $this;
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
            exit(json_encode(array("message" => "Nieprawsexlowy format danych")));
        }
    }

    // status
    public function getStatus(): ?int {
        return $this->status;
    }
    public function setStatus(int $status): self {
        if (is_numeric($status)) {
            $this->status = $status;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawstatuslowy format danych")));
        }
    }

    // create
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET idWorld = :idWorld,
                    name = :name,
                    idVocation = :idVocation,
                    title = :title,
                    sex = :sex,
                    status = :status";
        $stmt = $this->conn->prepare($query);

        $this->idWorld = htmlspecialchars(strip_tags($this->idWorld));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->idVocation = htmlspecialchars(strip_tags($this->idVocation));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->sex = htmlspecialchars(strip_tags($this->sex));
        $this->status = htmlspecialchars(strip_tags($this->status));

        $stmt->bindParam(':idWorld', $this->idWorld);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':idVocation', $this->idVocation);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':sex', $this->sex);
        $stmt->bindParam(':status', $this->status);

        try {
            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            } else {
                http_response_code(503);
                throw new Exception ("blah");
                exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
            }
        } catch (Exception $e) {
            echo "EEE \n \n";
        }
        
    }

    // read by id
    public function readById() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        // echo "CZYTAM" . $this->id;
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->idWorld = $row['idWorld'];
            $this->name = $row['name'];
            $this->idVocation = $row['idVocation'];
            $this->title = $row['title'];
            $this->sex = $row['sex'];
            $this->status = $row['status'];

            return $this;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // czytywanie wszysttkch kiedykolwiek pobranych graczy wedlug podanego swiata
    public function readByIdWorld() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE idWorld = :idWorld";
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

    // update idWorld
    public function updateIdWorld() {
        $query = "UPDATE {$this->table_name}
                SET idWorld = :idWorld
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->idWorld = htmlspecialchars(strip_tags($this->idWorld));
       
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':idWorld', $this->idWorld);
        
        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // update sex
    public function updateSex() {
        $query = "UPDATE {$this->table_name}
                SET sex = :sex
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->sex = htmlspecialchars(strip_tags($this->sex));
       
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':sex', $this->sex);
        
        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // update name
    public function updateName() {
        $query = "UPDATE {$this->table_name}
                SET name = :name
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->name = htmlspecialchars(strip_tags($this->name));
       
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        
        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // player exist
    public function playerExists() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE name = :name
                LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $stmt->bindParam(':name', $this->name);

        if($stmt->execute()) {
            $num = $stmt->rowCount();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($num>0) {
                $this->id = $row['id'];
                return true;
            }
            else return false;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }
}