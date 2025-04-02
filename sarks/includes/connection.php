<?php

	$server="sarks_mysql";
	$user="root";
	$pass="root";
	$db="sarksdb";
	
	// connect to mysql
	
	$con = mysqli_connect($server, $user, $pass) or die("Sorry, can't connect to the mysql.");
	
	// select the db
	
	mysqli_select_db($con,$db) or die("Sorry, can't select the database.");

?>
