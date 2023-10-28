<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
class Signup
{
    private $username;
    private $password;
    private $email;
    private $db;
    private $token;
    private $id;
    private $users_table;

    public function __construct($username, $password, $email)
    {
        $this->db = Database::getConnection();
        //--------------------------------------------------
        $this->username = $username;
        $this->email = $email;
        $this->password = $this->gen_pass_hash($password);
        $this->token = $this->gen_token();
        $this->users_table = Database::getCurrentDB()[0];
        //--------------------------------------------------
        if ($this->userExists()) {
            throw new Exception("Can't signup, username already exists");
        } else {
            $this->signup();
        }
    }

    public function userExists()
    {
        $db = $this->db = Database::getConnection();
        //--------------------------------------------------
        $username = $this->username;
        //--------------------------------------------------
        $query = "SELECT * FROM $this->users_table WHERE `username` = '$username'";
        $result = $db->query($query);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function signup()
    {
        $email = $this->email;
        $token = $this->token;
        $users_table = $this->users_table;
        $query = "INSERT INTO `$users_table` (`username`, `password`, `email_address`, `active`, `token`)
                  VALUES ('$this->username', '$this->password', '$this->email', '0', '$this->token')";
        $result = $this->db->query($query);
        if ($result) {
            $this->id = mysqli_insert_id($this->db);
            $result = $this->sendverificationEmail($email, $token);
        } else {
            throw new Exception("Unable to signup");
        }
    }

    public function gen_token()
    {
        $token = bin2hex(random_bytes(32));
        return $token;
    }

    public function InsertID()
    {
        return $this->id;
    }


    public function gen_pass_hash($password)
    {
        $cost_amount = [
            'cost' => 10
        ];
        //-----------------------
        $hash = password_hash($password, PASSWORD_BCRYPT, $cost_amount);
        return $hash;
    }
    private function sendVerificationEmail($email_account, $token)
    {
        if (!isset($email_account) || !isset($token)) {
            throw new Exception("Email or Token not set");
        }

        $SecureAPIKey = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/../env.json");
        $SecureAPIKey = json_decode($SecureAPIKey, true);
        $SecureAPIKey = $SecureAPIKey['token'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://sendemailapi.praveenms.site/api/sendmail/mail',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query(array(
                'request_method' => 'verification',
                'torecieve' => $email_account,
                'username' => $this->username,
                'subject' => 'Verify Your Email !',
                'link' => "https://vpn.praveenms.tech/api/verify?token=$token"
            )),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $SecureAPIKey
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $query = "DELETE FROM `$this->users_table` WHERE `username` = '$this->username'";
            $result = $this->db->query($query);
            if ($result) {
                throw new Exception("Please try again later. cURL Error: " . curl_error($curl));
            } else {
                throw new Exception("Please try again later. cURL Error: " . curl_error($curl));
            }
        }

        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpStatus == 200) {
            return true;
        } else {
            $query = "DELETE FROM `$this->users_table` WHERE `username` = '$this->username'";
            $result = $this->db->query($query);
            if ($result) {
                throw new Exception("Unable to send verification email and deleted the user, Please try again later. httpsstatus: $httpStatus");
            } else {
                throw new Exception("Unable to send verification email, Please try again later");
            }
        }
    }


    public static function verifyEmail($token)
    {
        $db = Database::getConnection();
        $users_table = Database::getCurrentDB()[0];
        $query = "SELECT * FROM `$users_table` WHERE `token` = '$token'";
        $result = $db->query($query);
        if ($result->num_rows > 0) {
            if ($result->fetch_assoc()['active'] == 1) {
                throw new Exception("Email Already Verified");
            } else {
                $sql = "UPDATE `$users_table` SET `active` = '1' WHERE `token` = '$token'";
                $final_result = $db->query($sql);
                if ($final_result) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
}
