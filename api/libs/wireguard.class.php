<?php

require_once "Database.class.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use Carbon\Carbon;

class wireguard
{
    private $db;
    private $device;
    public function __construct($device)
    {
        $this->db = Database::getConnection();
        $this->device = $device;
    }

    public function getPeers()
    {

    }

    public function getPeer($publicKey)
    {
        $cmd = "sudo wg show {$this->device}| grep -A10 {$publicKey}";
        $output = trim(shell_exec($cmd));
        $output = explode("\n", $output);
        $peer = array();
        $peerCount = 0;
        foreach ($output as $value) {
            if (!empty($value)) {
                $value = trim($value);
                if (stringStart($value, "peer:")) {
                    $peerCount++;
                    if ($peerCount > 1) {
                        break;
                    }
                }
                $datas = explode(":", $value);
                $peer[$datas[0]] = $datas[1];
                //array_push($peer, $value);
            }
        }
        return $peer;
    }
}
