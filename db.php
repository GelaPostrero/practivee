<?php

$host='localhost';
$dbname='eventreg';
$username='root';
$password='';

$conn=new mysqli($host, $username, $password, $dbname);

if($conn->connect_error){
    die('Database connection failed: ' . $conn -> connect_error);
}

?>

