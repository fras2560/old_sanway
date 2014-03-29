<?php 

// Login function
function login()
{
	session_start();				//Need to start session
	if (isset($_POST['password']) || isset($_POST['username'])){
		$_SESSION['empty_user'] = FALSE;
		$_SESSION['empty_pass'] = FALSE;
		$_SESSION['wrong_login'] = FALSE;
		$_SESSION['player_id']=0;
		if(empty($_POST['username']))	// No username so clearly not valid
		{
			$_SESSION['empty_user'] = TRUE;
		}
		if(empty($_POST['password']))	//no password so clearly not valid
		{
			$_SESSION['empty_pass'] = TRUE;;
		}
		$user=$_POST['username'];
		$pass=$_POST['password'];
		require_once( 'connectionClass.php' );
		$g1 = new Connection( 'settings.ini' );
		$conn = $g1->getConnection();
		$sql = "SELECT * FROM Player WHERE player_name='$user' AND password='$pass'";
		$stmt = $conn->prepare( $sql );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$data = $stmt->fetchAll();
			
		if(	$_SESSION['empty_user'] == FALSE && $_SESSION['empty_pass'] == FALSE){
			if( count($data) != 1)
			{
				$_SESSION['wrong_login'] = TRUE;
				header("Location: playerLogin.php");
			}
			else{
				$_SESSION['logged_in'] = true;
				$_SESSION['player_id']=$data[0]['player_id'];
				$_SESSION['player_name']=$user;
			}
		}
		Else{
			header("Location: playerLogin.php");
		}
	}
	else{
		header("Location: playerLogin.php");
	}
}

function manualLogin($player_name,$player_id){
	session_start();				//Need to start session
	$_SESSION['logged_in'] = true;
	$_SESSION['player_id']=$player_id;
	$_SESSION['player_name']=$player_name;
}

function login_check(){
	if($_SESSION['logged_in'] == true){
		return true;
	}
	else{
		return false;
	}
}

function getid(){
	return $_SESSION['player_id'];
}

?>