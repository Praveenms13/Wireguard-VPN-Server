<?php

require_once "Database.class.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";

use Carbon\Carbon;
use EllipticCurve\PublicKey;

// TODO TO Make this with Regex
class wireguard
{
    private $db;
    public $device;
    public function __construct($device)
    {
        $this->db = Database::getConnection();
        $this->device = $device;
    }

    public function getCIDR()
    {
        $cmd = "ip addr show dev wg0 | grep -w inet | awk '{print $2}'";
        $cmd = trim(shell_exec($cmd));
        return $cmd;
    }
    public function syncNetwork()
    {
        $ipnet = new IPNetwork($this->getCIDR(), $this->device);
        return $ipnet->syncNetworkFile();
    }

    public function addPeer($publicKey, $email)
    {
        $publicKey = str_replace(' ', '', trim($publicKey));
        $email = str_replace(' ', '', trim($email));
        $ipnet = new IPNetwork($this->getCIDR(), $this->device);
        $nextIP = $ipnet->getNextIP();
        $checkCmd = "sudo wg show " . trim($this->device) . " | grep " . trim($nextIP);
        $pattern = '/allowed ips: (' . preg_quote($nextIP, '/') . ')/';
        if (preg_match($pattern, exec($checkCmd))) {
            throw new Exception("IP or Peer Already Present, cannot add more than one peer with same IP");
        } else {
            $cmd = "sudo wg set {$this->device} peer {$publicKey} allowed-ips {$nextIP}";
            $result = 0;
            system($cmd, $result);
            if ($result == 0) {
                return $ipnet->allocateIP($nextIP, $email, $publicKey);
            } else {
                throw new Exception("Error Adding Peer");
            }
        }
    }

    public function removePeer($publicKey)
    {
        $ipnet = new IPNetwork($this->getCIDR(), $this->device);
        $publicKey = str_replace(' ', '', trim($publicKey));
        $cmd = "sudo wg set {$this->device} peer {$publicKey} remove";
        system($cmd, $result);
        if ($result == 0) {
            return $ipnet->deallocateIP($publicKey);
        }
    }

    public function getPeer($publicKey)
    {
        $publicKey = str_replace(' ', '', trim($publicKey));
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
