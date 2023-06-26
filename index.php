<?php
phpinfo();
//-------------------------------------------------------------------------------------------
// $config = file_get_contents("../env.json");
// $config = json_decode($config, true);
// $sendgrid_api_key = $config['sendgrid_api_key'];
// $email = new \SendGrid\Mail\Mail();
// $email->setFrom("mspraveenkumar77@gmail.com", "Example User");
// $email->setSubject("Sending with SendGrid is Fun");
// $email->addTo("mspreetha12@gmail.com", "Example User");
// $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
// $email->addContent(
//     "text/html",
//     "<strong>and easy to do anywhere, even with PHP</strong>"
// );
// $sendgrid = new \SendGrid($sendgrid_api_key);
// try {
//     $response = $sendgrid->send($email);
//     print $response->statusCode() . "\n";
//     print_r($response->headers());
//     print $response->body() . "\n";
// } catch (Exception $e) {
//     echo 'Caught exception: '. $e->getMessage() ."\n";
// }
