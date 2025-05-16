<?php
$con = mysqli_connect("db", "user", "pass", "my_db");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>