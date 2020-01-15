<?php

class Time {
    private $conn;
    private $table_name = "time";

    private $id;
    private $year;
    private $month;
    private $dayOfMonth;
    private $dayOfWeek;

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

    // year
    public function getYear(): ?int {
        return $this->year;
    }
    public function setYear(int $year): self {
        if (is_numeric($year)) {
            $this->year = $year;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    // month
    public function getMonth(): ?int {
        return $this->month;
    }
    public function setMonth(int $month): self {
        if (is_numeric($month)) {
            $this->month = $month;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    // day
    public function getDayOfMonth(): ?int {
        return $this->dayOfMonth;
    }
    public function setDayOfMonth(int $dayOfMonth): self {
        if (is_numeric($dayOfMonth)) {
            $this->dayOfMonth = $dayOfMonth;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    // dayOfWeek
    public function getDayOfWeek(): ?int {
        return $this->dayOfWeek;
    }
    public function setDayOfWeek(int $dayOfWeek): self {
        if (is_numeric($dayOfWeek)) {
            $this->dayOfWeek = $dayOfWeek;
            return $this;
        } else {
            http_response_code(400);
            exit(json_encode(array("message" => "Nieprawidlowy format danych")));
        }
    }

    // create
    public function create() {
        $query = "INSERT INTO {$this->table_name}
                SET year = :year,
                    month = :month,
                    dayOfMonth = :dayOfMonth,
                    dayOfWeek = :dayOfWeek";
        $stmt = $this->conn->prepare($query);

        $this->year = htmlspecialchars(strip_tags($this->year));
        $this->month = htmlspecialchars(strip_tags($this->month));
        $this->dayOfMonth = htmlspecialchars(strip_tags($this->dayOfMonth));
        $this->dayOfWeek = htmlspecialchars(strip_tags($this->dayOfWeek));

        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':dayOfMonth', $this->dayOfMonth);
        $stmt->bindParam(':dayOfWeek', $this->dayOfWeek);
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // read by date
    public function readByDate() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE year = {$this->year} AND
                    month = {$this->month} AND
                    dayOfMonth = {$this->dayOfMonth}";
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute()) {
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // read by id
    public function readById() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE id = {$this->id}";
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->year = $row["year"];
            $this->month = $row["month"];
            $this->dayOfMonth = $row["dayOfMonth"];
            return $stmt;
        } else {
            http_response_code(503);
            exit(json_encode(array("message" => "Error found at: file:" . __FILE__ . ",class: " . __CLASS__ . ", function: " . __METHOD__)));
        }
    }

    // czy juz istnieje taka sygnartura
    public function timeExist() {
        $query = "SELECT * FROM {$this->table_name}
                WHERE year = {$this->year} AND
                    month = {$this->month} AND
                    dayOfMonth = {$this->dayOfMonth}
                LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        
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