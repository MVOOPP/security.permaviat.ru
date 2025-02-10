<?php
session_start();
include("./settings/connect_datebase.php");

// Если пользователь не авторизован, перенаправляем на страницу входа
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Если в сессии нет session_token, тоже перенаправляем
if (!isset($_SESSION['session_token'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user'];
$current_session_token = $_SESSION['session_token'];

// Получаем session_token, роль и логин пользователя из базы
$stmt = $mysqli->prepare("SELECT session_token, roll, login FROM users WHERE id = ?");
if (!$stmt) {
    // В случае ошибки подготовки запроса — выходим
    header("Location: login.php");
    exit();
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($db_session_token, $user_roll, $user_login);
if (!$stmt->fetch()) {
    // Если пользователя не нашли — выходим
    $stmt->close();
    header("Location: login.php");
    exit();
}
$stmt->close();

// Если текущий session_token не совпадает с тем, что в базе, значит пользователь вошёл в другом браузере
if ($current_session_token !== $db_session_token) {
    session_destroy();
    header("Location: login.php?error=duplicate_login");
    exit();
}

// Если роль пользователя равна 1 (админ) — перенаправляем (можно заменить на редирект в админку)
if ($user_roll == 1) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE HTML>
<html>
	<head> 
		<meta charset="utf-8">
		<title>Личный кабинет</title>
		
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="style.css">
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
	</head>
	<body>
		<div class="top-menu">
			<a href="#" class="singin"><img src="img/ic-login.png" alt="icon"/></a>
			<a href="#"><img src="img/logo1.png" alt="logo"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"></div>
		<div class="main">
			<div class="content">
				<input type="button" class="button" value="Выйти" onclick="logout()"/>
				<div class="name" style="padding-bottom: 0px;">Личный кабинет</div>
				<div class="description">
					Добро пожаловать: <?php echo htmlspecialchars($user_login); ?><br>
					Ваш идентификатор: <?php echo htmlspecialchars($user_id); ?>
				</div>
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href="#">Конфиденциальность</a>
					<a href="#">Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			function logout() {
				$.ajax({
					url: 'ajax/logout.php',
					type: 'POST',
					data: null,
					cache: false,
					dataType: 'html',
					processData: false,
					contentType: false, 
					success: function (_data) {
						location.reload();
					},
					error: function(){
						console.log('Системная ошибка!');
					}
				});
			}
		</script>
	</body>
</html>
