<?php
require_once "../mysqli_connect.php";

$messageid = time();
$username = filter_var($_GET['username'], FILTER_SANITIZE_NUMBER_INT);
$message = filter_var($_GET['message'], FILTER_SANITIZE_STRING);

$query = "INSERT INTO log (messageid, username, message) VALUES ($messageid, $username, '$message');";
$response = mysqli_query($dbc, $query);

mysqli_close($dbc);
?>