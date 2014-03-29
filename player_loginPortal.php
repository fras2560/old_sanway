<?php
	session_start();
	require_once( "player_login.php" );
	if($_SESSION['logged_in'] != true){
    	login();
    	header("Location: player.php");
    }
    else{
    	header("Location: player.php");
    }
?>