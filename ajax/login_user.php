<?php
session_start();
include("../settings/connect_datebase.php");


require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require __DIR__ . '/../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$N_DAYS = 30; 

$login = trim($_POST['login']);
$password = $_POST['password'];

$query = $mysqli->prepare("SELECT id, password, password_changed_at FROM users WHERE login=?");
$query->bind_param("s", $login);
$query->execute();
$query->bind_result($id, $hashed_password, $password_changed_at);
$query->fetch();
$query->close();

if (!$id || !password_verify($password, $hashed_password)) {
    echo "error";
    exit;
}


$today = new DateTime();
$passwordDate = new DateTime($password_changed_at);
$interval = $today->diff($passwordDate)->days;

if ($interval >= $N_DAYS) {
    $_SESSION['user'] = $id;
    echo "expired";
    exit;
}


session_destroy();
session_start();
$_SESSION['user'] = $id;


$authCode = rand(100000, 999999);
$_SESSION['auth_code'] = $authCode;


session_write_close();


$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.yandex.ru';
    $mail->SMTPAuth = true;
    $mail->Username = 'anders@yandex.ru';
    $mail->Password = 'cnnwssisrgaxiiis'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('anders@yandex.ru', 'Система безопасности');
    $mail->addAddress('anders@yandex.ru');

    $mail->isHTML(true);
    $mail->Subject = 'Код подтверждения входа';
    $mail->Body = "<h2>Ваш код подтверждения: <b>$authCode</b></h2>";

    $mail->send();
    echo "success";
} catch (Exception $e) {
    echo "error: " . $mail->ErrorInfo;
}
?>