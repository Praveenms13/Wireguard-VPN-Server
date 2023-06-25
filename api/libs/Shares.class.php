<?php

require_once "Database.class.php";
require_once "Folder.class.php";
require_once "Notes.class.php";
class Share
{
    public $id;
    public $type;
    public function __construct($id, $type)
    {
        if ($type == "note" or $type == "folder") {
            $this->id = $id;
            $this->type = $type;
        } else {
            throw new Exception("Invalid Share Type");
        }
    }

    public function shareWith($username)
    {
    }

    public function revoke($username)
    {
    }


    public function hasAccess($username)
    {
    }
}
