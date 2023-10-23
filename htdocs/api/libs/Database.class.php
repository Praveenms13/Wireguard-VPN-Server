<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
class Database
{
    public static $connection = null;
    public $mongoClient = null;
    //-----------------MongoDB-Starts----------------------------------------
    public function __construct()
    {
        if (!extension_loaded('mysqli')) {
            throw new Exception("Mysqli extension not loaded");
        }
        if (!extension_loaded('json')) {
            throw new Exception("Json extension not loaded");
        }
        if (!extension_loaded('mongodb')) {
            throw new Exception("Mongodb extension not loaded");
        }
        $this->mongoClient = new MongoDB\Client("mongodb://127.0.0.1:27017");
        if (!$this->mongoClient) {
            http_response_code(500);
            throw new Exception("MongoDB connection failed");
        }
    }

    public function getMongoClient($db)
    {
        return $this->mongoClient->$db;
    }

    public function getArray($val)
    {
        return json_decode(json_encode($val), JSON_PRETTY_PRINT);
    }
    //-----------------MongoDB-Ends----------------------------------------
    //-----------------MySQL-Starts----------------------------------------
    public static function getConnection()
    {
        if (Database::$connection == null) {
            $config = file_get_contents(realpath(dirname(__FILE__)) . "/../../../env.json");
            $config = json_decode($config, true);
            $server = $config["server"];
            $user = $config["user"];
            $password = $config["password"];
            $dbname = $config["dbname"];
            $new_connection = new mysqli($server, $user, $password, $dbname);
            if (! $new_connection->connect_error) {
                //echo "Connection Status : Connected successfully";
                Database::$connection = $new_connection;
                return Database::$connection;
            } else {
                echo "Error: " . Database::$connection->error;
            }
        } else {
            //echo "Connection Status : Connected successfully";
            return Database::$connection;
        }
    }

    public static function getCurrentDB()
    {
        $config = file_get_contents(realpath(dirname(__FILE__)) . "/../../../env.json");
        $config = json_decode($config, true);
        $users = $config["users_table"];
        $user_session = $config["users_session_table"];
        return array($users,$user_session);
    }
    //-----------------MySQL-Ends----------------------------------------
}
