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
    private $device;
    public function __construct($cidr = null, $device = null)
    {
        if ($cidr == null) {
            throw new Exception("CIDR is null");
        }
        if ($device == null) {
            throw new Exception("Device is null");
        }
        $this->db = new Database();
        $this->collection = $this->db->getMongoClient('VPN');
        $this->cidr = $cidr;
        $this->device = $device;
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
                    'wgdevice' => $this->device,
                    'allocated' => false,
                    'owner' => '',
                    'created_time' => time(),
                    'allocated_time' => '',
                    'public_key' => '',
                    'private_key' => ''
                ];
                array_push($documents, $val);
            }try {
                $result = $this->collection->Networks->insertMany($documents);
                if ($result) {
                    $a = "Network Synced Successfully, " . $result->getInsertedCount() . " IP Addresses Inserted." . " Created Time : " . date("Y-m-d H:i:s", $documents[0]['created_time']);
                    $keys = [
                        'public_key' => 'text',
                        'private_key' => 'text'
                    ];

                    $options = [
                        'unique' => true
                    ];
                    $unique = $this->collection->Networks->createIndex($keys, $options);
                    if ($unique) {
                        $a = $a . ". Unique Index Created Successfully";
                        return $a;
                    } else {
                        $a = $a . ". Alert!! Unique Index Creation Failed";
                        throw new Exception($a);
                    }
                } else {
                    throw new Exception("Network Sync Failed");
                }
            } catch (Exception $e) {
                throw new Exception("Network Already Synced");
            }
            return $result;
        } else {
            $this->constructNetworkFile();
            return $this->syncNetworkFile();
        }
    }

    public function getnextIP()
    {
        $result = $this->collection->Networks->findone(['wgdevice' => $this->device, 'allocated' => false], ["sort" => ['_id' => 1]]);
        return $result['ip'];
    }

    public function allocateIP($ip, $email, $public_key)
    {
        $result = $this->collection->Networks->updateOne(['ip' => $ip, 'wgdevice' => $this->device], ['$set' => ['allocated' => true, 'owner' => $email, 'public_key' => $public_key, 'allocated_time' => time()]]);
        return $ip;
    }
}
