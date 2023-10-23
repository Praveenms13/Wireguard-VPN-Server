<?php

require_once "Database.class.php";
class User
{
    private $db;
    private $user;
    private $username;
    private $users_table;

    public function __construct($username)
    {
        $this->db = Database::getConnection();
        $this->users_table = Database::getCurrentDB()[0];
        $this->username = $username;
        $query = "SELECT * FROM `$this->users_table` WHERE `username` = '$this->username' OR `email_address` = '$this->username'";
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
