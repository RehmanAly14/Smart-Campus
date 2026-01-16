<?php
class Database
{
    private $host = "sql100.infinityfree.com";
    private $db_name = "if0_40906498_smart_campus";
    private $username = "if0_40906498";
    private $password = "lE7qRYLLHS9";

    public function getConnection()
    {
        try {
            $conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Database connection failed");
        }
    }
}
