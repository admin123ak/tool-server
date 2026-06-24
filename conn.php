<?php

$servername = "localhost";
$username = "sxladoro_sannso";
$password = "sxladoro_sannso";
$dbname = "sxladoro_sannso";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if(!$conn) {

die(" PROBLEM WITH CONNECTION : " . mysqli_connect_error());

}
  
?>