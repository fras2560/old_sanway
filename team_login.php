<?php

// Login function
function login()
{
	session_start();				//Need to start session
	if (isset($_POST['password']) || isset($_POST['username'])){
		$_SESSION['empty_user'] = FALSE;
		$_SESSION['empty_pass'] = FALSE;
		$_SESSION['wrong_login'] = FALSE;
		
		if(empty($_POST['username']))	// No username so clearly not valid
		{
			$_SESSION['empty_user'] = TRUE;
		}
		if(empty($_POST['password']))
		{
			$_SESSION['empty_pass'] = TRUE;;
		}
		$user=$_POST['username'];
		$pass=$_POST['password'];
		require_once( 'connectionClass.php' );
		$g1 = new Connection( 'settings.ini' );
		$conn = $g1->getConnection();
		$sql = "SELECT * FROM Team WHERE team_name='$user' AND password='$pass'";
		$stmt = $conn->prepare( $sql );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$data = $stmt->fetchAll();
			
		if(	$_SESSION['empty_user'] == FALSE && $_SESSION['empty_pass'] == FALSE){
			if( count($data) != 1)
			{
				$_SESSION['wrong_login'] = TRUE;
				header("Location: teamLogin.php");
			}
			else{
				$_SESSION['tlogged_in'] = true;
				$_SESSION['team_id']= $data[0]['team_id'];
				$_SESSION['team_name']=$user;
			}
		}
		else{
			header("Location: teamLogin.php");
		}
	}
	else{
		header("Location: teamLogin.php");
	}
}

function login_check(){
	if($_SESSION['logged_in'] == true){
		return true;
	}
	else{
		return false;
	}
}
function get_id(){
	print($data['team_id']);
	return $_SESSION['team_id'];
}

?>