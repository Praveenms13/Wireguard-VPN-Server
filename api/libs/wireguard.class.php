<?php

require_once "Database.class.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use Carbon\Carbon;
use EllipticCurve\PublicKey;

class wireguard
{
    private $db;
    private $device;
    public function __construct($device)
    {
        $this->db = Database::getConnection();
        $this->device = $device;
    }

    public function addPeer($publicKey, $ip)
    {
        $publicKey = str_replace(' ', '', trim($publicKey));
        $ip = str_replace(' ', '', trim($ip));
        // TODO TO Make this with Regex
        $cmd = "sudo wg set {$this->device} peer {$publicKey} allowed-ips {$ip}";
        $result = 0;
        system($cmd, $result);
        return $result == 0 ? true : false;
    }

    public function removePeer($publicKey)
    {
        $publicKey = str_replace(' ', '', trim($publicKey));
        // TODO TO Make this with Regex
        $cmd = "sudo wg set {$this->device} peer {$publicKey} remove";
        $result = 0;
        system($cmd, $result);
        return $result == 0 ? true : false;
    }

    public function getPeer($publicKey)
    {
        $publicKey = str_replace(' ', '', trim($publicKey));
        // TODO TO Make this with Regex
        $cmd = "sudo wg show {$this->device} | grep -A10 {$publicKey}";
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
                //TODO Done below line with ChatGPT
                $peer[$datas[0]] = $datas[1];
                //array_push($peer, $value);
            }
        }
        return $peer;
    }

    public function getPeers()
    {
        // TODO TO Make this with Regex
        $cmd = "sudo wg show {$this->device}";
        $output = trim(shell_exec($cmd));
        $output = explode("\n", $output);
        $interfaceOnly = array_slice($output, 0, 4);
        $peersOnly = array_slice($output, 5);
        $interfaces = array();
        $peers = array();
        $peerCount = -1;
        foreach($interfaceOnly as $value) {
            $value = trim($value);
            $data = explode(':', $value);
            $interfaces[trim($data[0])] = trim($data[1]);
        }

        foreach($peersOnly as $value) {
            $value = trim($value);
            if (strlen($value) > 0) {
                if (stringStart($value, 'peer')) {
                    $peerCount++;
                }
                $data = explode(':', $value);
                $peers[$peerCount][trim($data[0])] = trim($data[1]);
            }
        }
        return [
            'interface' => $interfaces,
            'peers' => $peers
        ];
    }
}
