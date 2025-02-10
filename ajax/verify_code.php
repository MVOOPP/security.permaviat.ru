<?php
session_start();

if ($_POST['code'] == $_SESSION['auth_code']) {
    unset($_SESSION['auth_code']);
    echo "success";
} else {
    echo "error";
}
?>