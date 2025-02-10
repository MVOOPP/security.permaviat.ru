<?php 
session_start();
include("./settings/connect_datebase.php");


if (isset($_SESSION['user']) && $_SESSION['user'] != -1) {
    header("Location: user.php");
    exit();
}
?>
<html>
<head> 
    <meta charset="utf-8">
    <title>Авторизация</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="top-menu">
    <a href="#"><img src="img/logo1.png"/></a>
    <div class="name">
        <a href="index.php">
            <div class="subname">БЕЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
            Пермский авиационный техникум им. А. Д. Швецова
        </a>
    </div>
</div>
<div class="space"></div>
<div class="main">
    <div class="content">
        <div class="login">
            <div class="name">Авторизация</div>

            <div class="sub-name">Логин:</div>
            <input name="_login" type="text" placeholder=""/>

            <div class="sub-name">Пароль:</div>
            <input name="_password" type="password" placeholder=""/>

            <a href="regin.php">Регистрация</a>
            <br><a href="recovery.php">Забыли пароль?</a>
            <input type="button" class="button" value="Войти" onclick="LogIn()"/>
            <img src="img/loading.gif" class="loading" style="display: none;"/>

            <div id="codeVerification" style="display: none;">
                <div class="sub-name">Введите код из почты:</div>
                <input name="_code" type="text" placeholder="6-значный код"/>
                <input type="button" class="button" value="Подтвердить" onclick="VerifyCode()"/>
            </div>
        </div>
    </div>
</div>

<script>
    function LogIn() {
        let _login = document.getElementsByName("_login")[0].value;
        let _password = document.getElementsByName("_password")[0].value;
        let loading = document.getElementsByClassName("loading")[0];

        loading.style.display = "block";

        $.ajax({
            url: 'ajax/login_user.php',
            type: 'POST',
            data: { login: _login, password: _password },
            success: function (response) {
                console.log("Ответ сервера (login_user.php):", response);
                loading.style.display = "none";

                if (response === "error") {
                    alert("Неверные данные!");
                } else if (response === "expired") {
                    alert("Ваш пароль устарел. Пожалуйста, смените его.");
                    window.location.href = "change_password.php";
                } else {
                    alert("Код отправлен на почту!");
                    document.getElementById("codeVerification").style.display = "block";
                    localStorage.setItem("authSession", response);
                }
            },
            error: function (xhr) {
                console.error("Ошибка сервера:", xhr.responseText);
                loading.style.display = "none";
                alert("Ошибка сервера!");
            }
        });
    }

    function VerifyCode() {
        let _code = document.getElementsByName("_code")[0].value;

        $.ajax({
            url: 'ajax/verify_code.php',
            type: 'POST',
            data: { code: _code },
            success: function (response) {
                console.log("Ответ сервера (verify_code.php):", response);

                if (response.trim() === "success") {
                    alert("Авторизация успешна!");
                    window.location.href = "user.php";
                } else {
                    alert("Неверный код!");
                }
            },
            error: function (xhr) {
                console.error("Ошибка сервера:", xhr.responseText);
                alert("Ошибка при проверке кода!");
            }
        });
    }
</script>

</body>
</html>