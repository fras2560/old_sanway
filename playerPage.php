<?php 

require_once( "player_login.php" );
require_once( "Sanway.php" );
require_once( "championshipsClass.php" );
session_start();
$d = new sanPublic();
?>
<!DOCTYPE html>
<html>
<head>
<link
	href='http://fonts.googleapis.com/css?family=Noto+Sans|Noto+Serif|Roboto'
	rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="style.css">
<title>Sanway Player Page</title>
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
							href="displayPlayers.php"><small>All Players</small> </a>
						</td>
						<td
							style="text-align: center; font-family: Roboto Slab, Noto Sans, Arial;"><a
							href="displayTeam.php"><small>All Teams</small> </a></td>
						<td
							style="text-align: center; font-family: Roboto Slab, Noto Sans, Arial;"><a
							href="championshipSelectForm.php"><small>Championships</small> </a></td>
						<td
							style="text-align: center; font-family: Roboto Slab, Noto Sans, Arial;"><a
							href="nostats.php"><small>Stats</small> </a></td>
						<td
							style="text-align: center; font-family: Roboto Slab, Noto Sans, Arial;"><a
							href="displayNews.php"><small>News</small> </a></td>
					
					</tr>
					<tr>
					<?php 
						$d->findPlayerForm();
					?>
					</tr>				
				</tbody>
			</table>
		</div>
		<p id="navigation">
		 			<a href="home.php">Home</a> > Player Page
		</p>
	</div>
	<h1>Player Page</h1>

	<div id='searchdiv'>

<?php
require_once( "player_login.php" );
require_once( "Sanway.php" );
require_once( "championshipsClass.php" );
session_start();
$d = new sanPublic();
if($_SESSION['logged_in'] == true){
	$user=$_SESSION['player_name'];
	print("Logged in: $user  ");
	$d->writeLink("player_logout.php", "Player logout");
}else{
	$d->writeLink("player.php", "Player Login");
}
if($_SESSION['tlogged_in'] == true){
	$user=$_SESSION['team_name'];
	print("  Logged in: $user  ");
	$d->writeLink("team_logout.php", "Team logout");
}else{
	$d->writeLink("team.php", "Team Login");
}
$d->printit();
?>

<?php 
$d = new sanPublic();
$player_name=$_GET['player_name'];
$d->displayPlayerProfile($player_name);
?>
	</div>

</body>
</html>
