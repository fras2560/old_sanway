<?php
	session_start();
	require_once( "team_login.php" );
	if($_SESSION['tlogged_in'] != true){
    	login();
    	header("Location: team.php");
    }
    else{
    	header("Location: team.php");
    }
?>