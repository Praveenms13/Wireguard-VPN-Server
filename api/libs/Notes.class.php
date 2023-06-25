<?php

require_once "Shares.class.php";
require_once "Database.class.php";
require_once "Folder.class.php";
require_once realpath(dirname(__FILE__)) . '/../../../../vendor/autoload.php';

use Carbon\Carbon;

class Notes extends Share
{
    public $id;
    public function __construct($id = null)
    {
        $this->db = Database::getConnection();
        parent::__construct($id, "note");
        if ($id!=null) {
            $this->id = $id;
            $this->refresh();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function createNew($folder_id, $title, $body, $owner = null)
    {
        $a = new Folder($folder_id);
        if ($a->getOwner() == $_SESSION['username']) {
            if (isset($_SESSION['username']) and strlen($title) <= 60 and is_numeric($folder_id) /*and strlen($body) <= 1000*/) {
                $owner = $_SESSION['username'];
                $query = "INSERT INTO `API_Notes` (`folder_id`, `title`, `body`, `owner`)
                      VALUES ('$folder_id', '$title', '$body', '$owner')";
                $result = $this->db->query($query);
                if ($result) {
                    $this->id = $this->db->insert_id;
                    $this->refresh();
                    return $this->id;
                } else {
                    throw new Exception("Error Creating Note, Try Again");
                }
            } else {
                throw new Exception("Cannot Create Note, Invalid Input Data");
            }
        } else {
            throw new Exception("Unauthorized");
        }
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
    public function updatedAt()
    {
        if ($this->data and isset($this->data['updated_at'])) {
            $UpdatedTime = new Carbon($this->data['updated_at'], date_default_timezone_get());
            return $UpdatedTime->diffForHumans();
        } else {
            throw new Exception("Updated at Not Found");
        }
    }

    //--------------------------------------------------------------------------------------------------------------------------------------------
    //setter not needed for this two func()
    public function getOwner()
    {
        if ($this->data and isset($this->data['owner'])) {
            return $this->data['owner'];
        } else {
            throw new Exception("Owner Not Found");
        }
    }


    public function getFolder_id()
    {
        if ($this->data and isset($this->data['folder_id'])) {
            return $this->data['folder_id'];
        }
    }
    //--------------------------------------------------------------------------------------------------------------------------------------------

    public function getTitle()
    {
        if ($this->data and isset($this->data['title'])) {
            return $this->data['title'];
        }
    }

    public function getBody()
    {
        if ($this->data and isset($this->data['body'])) {
            return $this->data['body'];
        }
    }

    public function setBody($body)
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            if ($this->id) {
                $query = "UPDATE `API_Notes` SET `body` = '$body', `updated_at` = NOW()
                      WHERE `id` = '$this->id'";
                $result = $this->db->query($query);
                $this->refresh(); //atmost nod needed but needed
                return $result;
            } else {
                throw new Exception("Note not Loaded");
            }
        } else {
            throw new exception("UnAuthorized Access");
        }
    }

    public function setTitle($title)
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            if ($this->id) {
                $query = "UPDATE `API_Notes` SET `title` = '$title', `updated_at` = NOW()
                      WHERE `id` = '$this->id'";
                $result = $this->db->query($query);
                $this->refresh(); //atmost nod needed but needed
                return $result;
            } else {
                throw new Exception("Note not Loaded");
            }
        } else {
            throw new exception("UnAuthorized Access");
        }
    }



    public function refresh()
    {
        if ($this->id!=null) {
            $query = "SELECT * FROM `API_Notes` WHERE `id` = '$this->id'";
            $result = $this->db->query($query);
            if ($result) {
                if ($result->num_rows > 0) {
                    $this->data = $result->fetch_assoc();
                    if ($this->data['owner'] != $_SESSION['username']) {
                        throw new Exception("Not Authorized or Resource Not found....");
                    }
                    $this->id = $this->data['id'];
                } else {
                    throw new Exception("Notes Not Found");
                }
            } else {
                throw new Exception("Database Error");
            }
        }
    }

    public function delete()
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            if ($this->id) {
                $query = "DELETE FROM `API_Notes` WHERE `id` = '$this->id'";
                $result = $this->db->query($query);
                return $result;
            } else {
                throw new Exception("Note not Loaded");
            }
        } else {
            throw new exception("UnAuthorized Access;)");
        }
    }

    public static function getallNotes($per_page = 10, $page = 1)
    {
    }
}
