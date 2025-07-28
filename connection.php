<?php
//this is my server address
$server="localhost";
//this is mmy MySQL username
$user = "root";
//this is mmy MySQL password
$password = "root";
// this is my database name
$database = "shopdatabase";
//this set up a connection table in PHP
$connection = new mysqli($server,$user,$password,$database);
#here i am trying to connect to my database if the connection fails print and error as well as the connection message
if ($connection->connect_error){
	die("Connection Failed".$connection->connect_error);
}



?>