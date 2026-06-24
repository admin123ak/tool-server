<?php

$servername = "localhost";
$username = "vipteams_rohit";
$password = "vipteams_rohit";
$dbname = "vipteams_rohit";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if(!$conn) {

die(" PROBLEM WITH CONNECTION : " . mysqli_connect_error());

}
  
?>