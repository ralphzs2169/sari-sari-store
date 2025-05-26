<?php
class Admin
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($username, $password)
    {
        error_log("CREATE MODEL hit");
        // Debug incoming POST data
        error_log(print_r($_POST, true));

        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("CALL RegisterAdmin(:username, :password)");
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashedPassword);
        return $stmt->execute();
    }

    public function login($username)
    {
        $stmt = $this->conn->prepare("CALL GetUserByUsername(:username)");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
