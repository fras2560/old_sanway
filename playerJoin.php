<!DOCTYPE html>
<html>
<head>
<link
	href='http://fonts.googleapis.com/css?family=Noto+Sans|Noto+Serif|Roboto'
	rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="style.css">
<title>Sanway</title>
</head>
<body id='homebody' style="padding: 0px; margin: 0px; margin-top: 0px;">
	<div id="banner">
		<h1 style="text-align: center; font-family: Roboto, Arial;">
			<a href="index.htm"><img
				style="border: 0px solid; width: 300px; height: 300px;"
				alt="{ Sanway }" src="images/tropy.jpg"> </a>
		</h1>
		<div>
			<table
				style="width: 679px; height: 32px; text-align: center; margin-left: auto; margin-right: auto;">
				<tbody>
					<tr>
						<td
							style="text-align: center; font-family: Roboto Slab, Noto Sans, Arial;"><a
							href="player.php"><small>Player Changes</small> </a>
						</td>
						<td
							style="text-align: center; font-family: Roboto Slab, Noto Sans, Arial;"><a
							href="team.php"><small>Team Changes</small> </a></td>
					</tr>
				</tbody>
			</table>
		</div>
		<p id="navigation">
		 			<a href="home.php">Home</a> > <a href="playerJoinForm.php">Player Join Form</a> > Player Join
		 </p>
	</div>
	<h1>Player Join</h1>

	<div id='searchdiv'>

<?php
require_once( "Sanway.php" );
require_once( "playerClass.php" );

$d = new sanPublic();
?>


<?php
$player_name=$_POST['player_name'];
$d->joinPlayer();
$player_id=$d->getPlayerId($player_name);
session_start();				//Need to start session
$_SESSION['logged_in'] = true;
$_SESSION['player_id']=$player_id;
$_SESSION['player_name']=$player_name;
header("Location: home.php");
?>
	</div>

</body>
</html>

