<?php

// include("../verify.php");

//------------------including the notes framework-------------------------------------------------------------------------------
require_once(realpath(dirname(__FILE__)) . "/Folder.class.php");
//------------------End ofincluding the notes framework-------------------------------------------------------------------------------
/**
 */
//------------------including Php mail framework-------------------------------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once realpath(dirname(__FILE__)) . '/../../../../vendor/phpmailer/phpmailer/src/Exception.php';
require_once realpath(dirname(__FILE__)) . '/../../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once realpath(dirname(__FILE__)) . '/../../../../vendor/phpmailer/phpmailer/src/SMTP.php';
require_once realpath(dirname(__FILE__)) . '/../../../../vendor/autoload.php';
//------------------End of including Php mail framework-------------------------------------------------------------------------------
class Signup
{
    private $username;
    private $password;
    private $email;
    private $db;
    public $id;
    public $token;

    public function __construct($username, $password, $email)
    {
        $this->db = Database::getConnection();
        //--------------------------------------------------
        $this->username = $username;
        $this->email = $email;
        $this->password = $this->gen_pass_hash($password);
        $this->token = $this->gen_token();
        //--------------------------------------------------
        if ($this->userExists()) {
            throw new Exception("Can't signup, user already exists");
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
        $query = "SELECT * FROM `API` WHERE `username` = '$username'";
        $result = $db->query($query);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function signup()
    {
        $query = "INSERT INTO `API` (`username`, `password`, `email_address`, `active`, `token`)
                  VALUES ('$this->username', '$this->password', '$this->email', '0', '$this->token')";

        $email = $this->email;
        $token = $this->token;
        $result = $this->db->query($query);
        if ($result) {
            $this->sendverificationEmail($email, $token);
            $this->id = mysqli_insert_id($this->db);
            //------------------creating the default folder-------------------------------------------------------------------------------
            $folder = new Folder();
            session_start();
            $_SESSION['username'] = $this->username;
            $folder->createNew("Default Folder");
            //------------------End of creating the default folder-------------------------------------------------------------------------------
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


    private function sendverificationEmail($email, $token)
    {
        $config = file_get_contents(realpath(dirname(__FILE__)) . "/../../../../config_files/api.json");
        $config = json_decode($config, true);
        $from_mail = $config["mail_ID"];
        $from_mail_key = $config["mail_key"];
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->Username = $from_mail;
            $mail->Password = $from_mail_key;

            $mail->setFrom($from_mail, "M.S.Praveen Kumar");
            $mail->addAddress($email, 'Test Name');
            $mail->addReplyTo($from_mail, "M.S.Praveen Kumar");

            $mail->IsHTML(true);
            $mail->Subject = "Verify Your Email";
            //$mail->Body = 'You can see your Auth Token Here. Token : ' . $token;
            $mail->Body = "<h2>Click<a href='http://apis.selfmade.one/api/verify?token=$token'> here </a>to verify you Email..</h2>";
            $mail->Body = "<strong>Click<a href='http://apis.selfmade.one/api/verify?token=$token'> here </a>to verify you Email..</strong>";
            $mail->SMTPDebug = false;
            $mail->send();
            //echo "Email message sent.";
        } catch (Exception $e) {
            echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
        }
    }



    public static function verifyEmail($token)
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM `API` WHERE `token` = '$token'";
        $result = $db->query($query);
        if ($result->num_rows > 0) {
            if ($result->fetch_assoc()['active'] == 1) {
                throw new Exception("Email Already Verified");
            } else {
                $sql = "UPDATE `API` SET `active` = '1' WHERE `token` = '$token'";
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
