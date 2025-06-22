<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8'); 

$servername = "127.0.0.1"; 
$username = "root";        
$password = "";            
$dbname = "sopitas_baza";   

$dbc = mysqli_connect($servername, $username, $password, $dbname);

mysqli_set_charset($dbc, "utf8");

if (!$dbc) {
    die("Povezivanje na bazu podataka nije uspjelo: " . mysqli_connect_error());
}

if (!defined('UPLPATH')) {
    define('UPLPATH', 'img/'); 
}

?>