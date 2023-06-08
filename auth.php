<?php
session_start();
require_once('config.php');

$login = htmlspecialchars(trim($_POST['input-login']));
$password = htmlspecialchars(trim($_POST['input-password']));
$connection = new mysqli(host, user, password, auth_db);
if ($connection->connect_error) {
    exit('Ошибка подключения к БД');
}
$connection->set_charset('utf8');
$stmt = $connection->prepare("select * from users where `login` = ? and `password` = md5(?);");
$stmt->bind_param('ss', $login, $password);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row) {
    echo "Вы вошли";
    $_SESSION['login-user'] = $row['login'];
}
else {
    echo "Неверный логин и (или) пароль";
}
$result->close();
$stmt->close();
$connection->close();
?>