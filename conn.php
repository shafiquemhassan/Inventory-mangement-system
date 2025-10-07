<?php

$conn = new mysqli('localhost','root','','ims');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>