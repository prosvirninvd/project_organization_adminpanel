<?php
$arr = $_POST['update'];
$pk_name = array_keys($arr)[0];
$pk_value = array_values($arr)[0];
$table_name = $_POST['table-name'];
require_once 'config.php';
$connect = mysqli_connect(host,user,password,database);
if (!$connect) {
    exit();
}
mysqli_set_charset($connect,'utf8');
$query_delete = mysqli_query($connect, "DELETE FROM `$table_name` WHERE `$pk_name` = '$pk_value';");
echo '1';
mysqli_close($connect);
?>