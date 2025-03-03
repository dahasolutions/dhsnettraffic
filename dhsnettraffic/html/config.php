<?php
// Ket noi de quan ly tai khoan
$servername			=    "MYSQL_HOST";
$username			=    "MYSQL_USER";
$password			=    "MYSQL_PASSWORD";
$database			=    'MYSQL_DB';

// Ket noi den host name mysql
$conn_web = new mysqli($servername, $username, $password, $database);
mysqli_set_charset($conn_web,"utf8");
if ($conn_web->connect_error) {
	die ('<center style="font-size: 20px;color: red;">The system is under maintenance, please come back later !!</center>');
}

?>
