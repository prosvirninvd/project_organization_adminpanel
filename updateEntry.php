<?php
$arr = $_POST['update'];
$keyarr = array_keys($arr);
$valuearr = array_values($arr); 
$pk_name = $keyarr[0];
$pk_value = $valuearr[0];
$table_name = $_POST['table-name'];
require_once 'config.php';
$connect = mysqli_connect(host,user,password,database);
if (!$connect) {
    exit();
}
mysqli_set_charset($connect,'utf8');
for ($i=1; $i < count($keyarr); $i++) { 
    $param[$i - 1] = "`$keyarr[$i]` = '$valuearr[$i]'";
}
$joined_param_value = implode(", ", $param);
$query_update = mysqli_query($connect, "UPDATE `$table_name` SET $joined_param_value WHERE `$pk_name` = '$pk_value';");
$query_select = mysqli_query($connect, "SELECT * FROM `$table_name` WHERE `$pk_name` = '$pk_value';");
$row = json_encode(mysqli_fetch_assoc($query_select));
echo $row;
mysqli_close($connect);
?>