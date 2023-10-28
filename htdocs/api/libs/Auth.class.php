<?php

require_once "Database.class.php";

class Auth  //OAuth can be used to generate new access tokens
{
    private $db;
    private $isAuthToken;
    private $Logintoken = null;
    public $username;
    public $oauth;
    public $oauth_token;
    private $Token = null;
    private $password;
    private $users_table;
    public function __construct($username, $password = null)
    {
        $this->db = Database::getConnection();
        $this->users_table = Database::getCurrentDB()[0];
        if ($password == null) {
            $this->isAuthToken = true;
            $this->Token = $username;
        } else {
            $this->isAuthToken = false;
            $this->username = $username;
            $this->password = $password;
        }


        if ($this->isAuthToken) {
            $this->oauth_token = new OAuth($this->Token);
            $this->oauth_token->authenticate();
        } elseif (!$this->isAuthToken) {
            $user = new User($this->username);
            $this->username = $user->getName();
            if (password_verify($this->password, $user->getPasswordHash())) {
                if ($this->isActive()) {
                    $this->username = $user->getName();
                    $this->Logintoken = $this->addSession();
                } else {
                    throw new Exception("User not verified, Kindly verify your email address");
                }
            } else {
                throw new Exception("Password Mismatched....");
            }
        }
    }

    private function addSession()
    {
        $this->oauth = new OAuth();
        $this->oauth->setUsername($this->username);
        $session = $this->oauth->newSession(3600);
        return $session;
    }

    public function getUsername()
    {
        if ($this->oauth_token->authenticate()) {
            return $this->oauth_token->getUsername();
        } elseif (!$this->oauth_token->authenticate()) {
            return false;
        } else {
            echo "Something Went Wrong....";
        }
    }

    public static function gen_token($len)
    {
        $bytes = openssl_random_pseudo_bytes($len, $cstrong);
        return bin2hex($bytes . random_bytes(16));
    }

    private function isActive()
    {
        $query = "SELECT * FROM `$this->users_table` WHERE `username` = '$this->username'";
        $result = $this->db->query($query);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($user['active']) {
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception("User not found");
        }
    }

    public function getToken()
    {
        return $this->Logintoken;
    }
}
