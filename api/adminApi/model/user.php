<?php

class User
{
    //POLA KLASY
    private $conn;
    private $table_name = "users";

    private $id;
    private $login;
    private $password;
    private $status;

    //KONSTRUKTOR
    public function __construct($db) {
        if(get_class($db) == "PDO") $this->conn = $db;
    }

    //GETTERS AND SETTERS
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
    // Login
    public function getLogin(): ?string {
        return $this->login;
    }

    public function setLogin(string $login): self {
        if (empty($login)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Wprowadź login!")));
        } else if (is_numeric($login)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Login nie może być liczbą!")));
        } else if (strlen($login) < 4 || strlen($login) > 32) {
            http_response_code(400);
            exit(json_encode(array("message" => "Login musi mieć od 4 do 32 znaków!")));
        } else {
            $this->login = $login;
            return $this;
        }
    }
    // Password
    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        if(empty($password)) {
            http_response_code(400);
            exit(json_encode(array("message" => "Wprowadz haslo!")));
        } else if (strlen($password) < 4 || strlen($password) > 32) {
            http_response_code(400);
            exit(json_encode(array("message" => "Hasło musi składać się z 4 do 32 znaków.")));
        } else {
            $this->password = $password;
            return $this;
        }
    }
    // Status
    public function getStatus(): ?int {
        return $this->status;
    }
    public function setStatus(int $status): self {
        if (is_numeric($status)) {
            $this->status = $status;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    //ZAPYTANIA DO BAZY DANYCH
    // Read user by login
    public function readByLogin() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE login = ?";
        $stmt = $this->conn->prepare($query);
        $this->login=htmlspecialchars(strip_tags($this->login));
        $stmt->bindParam(1, $this->login);
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->password = $row['password'];
            $this->status = $row['status'];
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Uzytkownik nie istnieje")));
        }
    }
    // Read user by id
    public function read() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(":id", $this->id);
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->login = $row['login'];
            $this->password = $row['password'];
            $this->status = $row['status'];
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }
    // Update user to active
    public function login() {
        $query = "UPDATE {$this->table_name}
                SET status = 1
                WHERE id = :id";
        $stmt = $this->conn->prepare( $query );
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()) {
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }
    // Update user to inactive
    public function logout() {
        $query = "UPDATE {$this->table_name}
                SET status = 0
                WHERE id = :id";
        $stmt = $this->conn->prepare( $query );
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        if($stmt->execute()) {
            $this->status = 0;
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Blad bazy danych")));
        }
    }
}