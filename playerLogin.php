
<!DOCTYPE html>
<html>
<head>
<link
	href='http://fonts.googleapis.com/css?family=Noto+Sans|Noto+Serif|Roboto'
	rel='stylesheet' type='text/css'>
<link rel="stylesheet" type="text/css" href="style.css">
<title>Sanway Create Team</title>
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
		 			<a href="home.php">Home</a> > Create Team
		</p>
	</div>
	<h1>Create a Team</h1>

	<div id='searchdiv'>

<?php 
	session_start();
    require_once( "player_login.php" );
?>
<form name="nameForm" action="player_loginPortal.php" method="post">
<html> 
  <table>
  	<tr>
  		<td></td>
  		<td><i>
  		<?php 
  		if ($_SESSION['empty_user'] == TRUE){
  		echo "*No username entered!";}
  		?></i>
  		</td>
  	</tr>
  	<tr>
  		<td></td>
  		<td><i>
  		<?php 
  		if ($_SESSION['wrong_login'] == TRUE){
  		echo "*Invalid Username/Password combination!";}
  		?></i>
  		</td>
  	</tr>
    <tr>
      <td>
        <label for="username">Player Name: </label>
      </td>
      <td>
        <input name="username" type="text" size="25" />
      </td>
    </tr>
    <tr>
    	<td></td>
    	<td><i>
    	<?php 
  			if ($_SESSION['empty_pass'] == TRUE){
  			echo "*No password entered!";}
  		?></i>
  		</td>
  	</tr>
    <tr>
      <td>
        <label for="password">Password: </label>
      </td>
      <td>
        <input name="password" type="password" size="25" />
      </td>
    </tr>   
  </table>
  
 <p style="clear: both;">
    <input name="login" type="submit" value="Login" />
    <input name="home" type="button" value="Home" onClick="window.location='home.php'">
 </form>

	</div>

</body>
</html>
