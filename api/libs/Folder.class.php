<?php

require_once "Database.class.php";
require_once "Shares.class.php";
require_once "Notes.class.php";
require_once realpath(dirname(__FILE__)) . '/../../../../vendor/autoload.php';

use Carbon\Carbon;

class Folder extends Share
{
    public $db;
    public $data;
    public $id;
    public function __construct($id = null)
    {
        $this->db = Database::getConnection();
        parent::__construct($id, "folder");
        if ($id != null) {
            $this->id = $id;
            $this->refresh();
        }
        /**
         * removed else part
         * else {
         *   throw new Exception("Invalid Share Type....");
         *   }
         */
    }


    public function createdAt()
    {
        if ($this->data and isset($this->data['created_at'])) {
            $CreatedTime = new Carbon($this->data['created_at'], date_default_timezone_get());
            return $CreatedTime->diffForHumans();
        } else {
            throw new Exception("Created at Not Found");
        }
    }

    public function createNew($foldername = "New Folder")
    {
        if ($_SESSION['username'] and strlen($foldername) <= 60) {
            $username = $_SESSION['username'];
            $query = "INSERT INTO `API_Notes_Folder` (`folder_name`, `owner`)
                  VALUES ('$foldername', '$username')";
            $result = $this->db->query($query);
            if ($result) {
                $this->id = mysqli_insert_id($this->db);
                $this->refresh();
                return $this->id;
            } else {
                throw new Exception("Error creating folder");
            }
        } else {
            throw new Exception("You must be logged in to create a folder and check the Folder name size, should not exid 60 charecters");
        }
    }

    public function getName()
    {
        if ($this->data and isset($this->data['folder_name'])) {
            return $this->data['folder_name'];
        } else {
            throw new Exception("Not Found");
        }
    }

    public function rename($foldername)
    {
        if ($this->id) {
            $query = "UPDATE `API_Notes_Folder` SET `folder_name` = '$foldername'
                  WHERE `id` = '$this->id'";
            $result = $this->db->query($query);
            $this->refresh();
            return $result;
        } else {
            throw new Exception("Note not Found");
        }
    }

    public function refresh()
    {
        if ($this->id!=null) {
            $query = "SELECT * FROM `API_Notes_Folder` WHERE `id` = '$this->id'";
            $result = $this->db->query($query);
            if ($result) {
                if ($result->num_rows > 0) {
                    $this->data = $result->fetch_assoc();
                    if ($this->data['owner'] != $_SESSION['username']) {
                        throw new Exception("Not Authorized or Resource Not found....");
                    }
                    $this->id = $this->data['id'];
                } else {
                    throw new Exception("Folder Not Found, Please check the the thing once again...");
                }
            } else {
                throw new Exception("Database Error");
            }
        }
    }

    public function getallFolders()
    {
        $query = "SELECT * FROM `API_Notes_Folder` WHERE `owner` = '$_SESSION[username]'";
        $result = $this->db->query($query);
        if ($result) {
            $AllFoldersdata = $result->fetch_all(MYSQLI_ASSOC);
            for ($i=0;$i<count($AllFoldersdata);$i++) {
                $date = $AllFoldersdata[$i]['created_at'];
                $humanReadable = new Carbon($date, date_default_timezone_get());
                $AllFoldersdata[$i]['created'] = $humanReadable->diffForHumans();

                $a = new Folder($AllFoldersdata[$i]['id']);
                $AllFoldersdata[$i]['total_notes'] = $a->countallNotes();
            }
            return $AllFoldersdata;
        } else {
            throw new Exception("Database Error");
        }
    }
    public static function sayhello()
    {
        return "hello";
    }

    public function getId()
    {
        return $this->id;
    }

    public function getallNotes()
    {
        //Also get the notes which are shared with the user-- to be implemented
        $query = "SELECT * FROM `API_Notes` WHERE `folder_id` = '$this->id'";
        $result = $this->db->query($query);
        if ($result) {
            $AllNotesdata = $result->fetch_all(MYSQLI_ASSOC);
            return $AllNotesdata;
        } else {
            throw new Exception("Database Error");
        }
    }

    public function countallNotes()
    {
        $query = "SELECT COUNT(*) FROM `API_Notes` WHERE `folder_id` = '$this->id'";
        $result = $this->db->query($query);
        if ($result) {
            $Notesdata = $result->fetch_assoc();
            return $Notesdata['COUNT(*)'];
        } else {
            throw new Exception("Database Error");
        }
    }

    public function getOwner()
    {
        if ($this->data and isset($this->data['owner'])) {
            return $this->data['owner'];
        } else {
            throw new Exception("Owner Not Found");
        }
    }

    public function getNotes()
    {
    }

    //------DELETE ALL THE Notes and then the induvidual folder containing it-----------------------------------
    public function delete()
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            if ($this->id) {
                $notes = $this->getallNotes();
                foreach ($notes as $note) {
                    $a = new Notes($note['id']);
                    $a->delete();
                }

                $query = "DELETE FROM `API_Notes_Folder` WHERE `id` = '$this->id'";
                $result = $this->db->query($query);
                //$this->refresh(); //atmost nod needed but needed
                return $result;
            } else {
                throw new Exception("Folder not Loaded");
            }
        } else {
            throw new exception("UnAuthorized Access");
        }
    }
    //----------------------------------------------------------------------------------------------------------
}
