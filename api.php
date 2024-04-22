<?php
date_default_timezone_set("Asia/Bangkok");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE"); // Method
header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding,Authorization");
header('Content-Type: application/json'); // JSON

require_once __DIR__ . '/vendor/autoload.php'; // Path to Autoload.php file
require_once("./config/connect.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$pass_app = $_ENV['PASS_APP_GMAIL']; // อ่านจาก Environment Server (.env) หรือจะเขียน Logic เข้ารหัส ถอดรหัสเองก็ได้


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Clent Method => POST ,BODY =>  {router : "sendmail",email:"test@gmail.com"} 

    if ($req->router == 'sendmail') {
        $mail = new PHPMailer(true); // สร้าง Instance Class PHPMailer เวลาจะเข้าถึง Method ก็เรียกผ่าน $mail

        $email = $req->email; //จะส่งถึงใคร (รับจาก Client)
        $desc = $req->desc; // กรณีอยากรับค่าจาก Client

        try {
            //Server settings
            $mail->CharSet = "utf-8"; // Utf-8 Format
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'mail.sncformer.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //เปิด SMTP authentication
            $mail->Username   = 'aukawut@sncformer.com';                //SMTP Email
            $mail->Password   = $pass_app;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port -> ใช้ Port 587 ถ้ามีการตั้งค่า `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $mail->setFrom('aukawut2542@gmail.com', 'IT MIS');
            $mail->addAddress($email, $email);     //Add address ผู้รับ //addAddress(address, name)
            //Content
            $mail->isHTML(true); // ในกรณรต้องการเขียน Html -> True ;
            $mail->addEmbeddedImage('./assets/images/ExampleMail.png', 'my-image', './assets/images/ExampleMail.png');
            //Set email format to HTML
            $mail->Subject = 'แบบสำรวจการใช้งานเครื่อง Printer';
            $mail->Body = "<h2>กรุณาเข้าตรวจสอบการใช้งาน <b>เครื่อง Printer</b> <a href='https://it-services.sncformer.com/printerchecklist/'>คลิกที่นี่</a> </h2>"
                . "<h2>เลือกเมนู - <u>สำรวจการใช้งาน Printer</u> - </h2>" . "<br />" . "<img alt='PHPMailer' src='cid:my-image'>" . "<br />" . $desc;
            $mail->send();

            echo json_encode(["err" => false, "msg" => "Mail sended!"]);
        } catch (Exception $e) {
            echo json_encode(["err" => true, "msg" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    }
} else {
    echo json_encode(["err" => true, "msg" => "Route not provide!"]);
}
