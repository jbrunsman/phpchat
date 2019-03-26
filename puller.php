<?php
require_once "../mysqli_connect.php";

$cookie_name = 'jchat_lastupdate';
$time = $_GET['id'];

$query = "SELECT * FROM log WHERE messageid > $time;";
$response = mysqli_query($dbc, $query);

$newtime = $time;
while($p = mysqli_fetch_assoc($response)) {
    echo $p['messageid'] . ' - <b>' . $p['username'] . '</b> : ' . $p['message'] . '<br>'; // temp styling
    $newtime = $p['messageid'];
}

mysqli_close($dbc);

setcookie($cookie_name, $newtime, time() + (86400 * 30), "/");
?>