<?php
session_start();
include("../settings/connect_datebase.php");

$login = trim($_POST['login']);
$password = $_POST['password'];

// Проверяем пользователя
$query = $mysqli->prepare("SELECT id, password, email FROM users WHERE login=?");
$query->bind_param("s", $login);
$query->execute();
$query->bind_result($id, $hashed_password, $email);
$query->fetch();
$query->close();

if (!$id || !password_verify($password, $hashed_password)) {
    echo "error";
    exit;
}


session_destroy();
session_start();
$_SESSION['user'] = $id;


$authCode = rand(100000, 999999);
$_SESSION['auth_code'] = $authCode;


$subject = "Код подтверждения входа";
$message = "Ваш код: " . $authCode;
$headers = "From: no-reply@site.com\r\n";
mail($email, $subject, $message, $headers);

echo "success";
?>