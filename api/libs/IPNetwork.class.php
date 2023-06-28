<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/libs/Database.class.php";

//TODO : Update the class to support IPv6...
//TODO : To Change with REGEX to validate CIDR IF Possible...
//TODO : Check if nmap is installed...
//TODO :To Filter Nmap Commands to Prevent Command Injection...
//TODO : Creating a file with www-data user to avoid permission issues should be fix in future...
class IPNetwork
{
    private $cidr;
    private $db;
    private $collection;
    private $network;
    public function __construct($cidr = null)
    {
        if ($cidr == null) {
            throw new Exception("CIDR is null");
        }
        $this->db = new Database();
        $this->collection = $this->db->getMongoClient('VPN');
        $this->cidr = $cidr;
        $this->network = $this->getNetwork();
    }

    public function getNetwork()
    {
        if ($this->network == null) {
            $result = $this->collection->Networks->findone(['cidr' => $this->cidr]);
            return $this->db->getArray($result);
        } else {
            return $this->network;
        }
    }

    public function getNetworkFilePath()
    {
        $file_name = str_replace('.', '_', $this->cidr);
        $file_name = str_replace('/', '_', $file_name) . ".txt";
        $ip_file = $_SERVER['DOCUMENT_ROOT'] . "api/networks/" . $file_name;
        return $ip_file;
    }

    public function constructNetworkFile()
    {
        $ip_file = $this->getNetworkFilePath();
        $cmd = 'nmap -sL -n ' . $this->cidr . ' | awk \'/Nmap scan report/{print $NF}\' > ' . $ip_file;
        return system($cmd);
    }

    public function syncNetworkFile()
    {
        if (file_exists($this->getNetworkFilePath())) {
            $datas = file_get_contents($this->getNetworkFilePath());
            $datas = explode("\n", $datas);
            $datas = array_slice($datas, 2, count($datas) - 4);
            $documents = array();
            $id = 0;
            foreach ($datas as $data) {
                $val = [
                    '_id' => $id++,
                    'cidr' => $this->cidr,
                    'ip' => $data,
                    'allocated' => false,
                    'owner' => '',
                    'created_time' => time(),
                    'allocated_time' => '',
                    'public_key' => '',
                    'private_key' => '',
                    'allowed_ips' => '',
                    'endpoint' => '',
                    'persistent_keepalive' => '',
                    'status' => 'offline',
                    'last_seen' => '',
                    'last_handshake' => '',
                ];
                array_push($documents, $val);
            }try {
                $result = $this->collection->IP_Addrs->insertMany($documents);
            } catch (Exception $e) {
                print("Network Already Synced");
            }
            return $result;
        } else {
            $this->constructNetworkFile();
            return $this->syncNetworkFile();
        }
    }

    public function getnextIP()
    {

    }

    public function allocateIP($ip, $email, $public_key, $private_key, $allowed_ips, $endpoint, $persistent_keepalive, $last_seen, $last_handshake)
    {

    }

    public function generateIPfromCIDR()
    {

    }

    public function getIP()
    {

    }

    public function getUser()
    {

    }
}
