<?php

require_once "Database.class.php";
class User
{
    private $db;
    private $user;

    public function __construct($username)
    {
        $this->db = Database::getConnection();
        $this->username = $username;
        $query = "SELECT * FROM `API` WHERE `username` = '$this->username' OR `email_address` = '$this->username'";
        $result = $this->db->query($query);
        if ($result->num_rows > 0) {
            $this->user = $result->fetch_assoc();
        } else {
            throw new Exception("User not found......");
        }
    }

    public function getId()
    {
        return $this->user['id'];
    }

    public function getName()
    {
        return $this->user['username'];
    }

    public function getEmail()
    {
        return $this->user['email_address'];
    }

    public function getPasswordHash()
    {
        return $this->user['password'];
    }
}
