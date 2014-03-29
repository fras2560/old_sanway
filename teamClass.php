<?php 
require_once( "utilities.php" );

class team{
	private $conn = null; // Connection information and utilities.
	private $xml = null;

	//-------------------------------------------------------------------------
	public function __construct( &$conn, &$xml ) {
		$this->conn = $conn;
		$this->xml = $xml;
	}
	public function getCurrentRoster($team_name){
		try{
			
			$sql = "Select * from v_team_roster Where team_name = ? AND Year=YEAR(CURDATE())";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,"$team_name" );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return  $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}		
	}

	public function getTeamId($team_name){
		try{
			$sql = "SELECT * FROM Team Where team_name LIKE ? ";
			$stmt = $this->conn->prepare( $sql );
			$i = 1;
			$stmt->bindValue( $i++, "$team_name%" );
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			$stmt->execute();
			return $stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function writeName($player_name){
		$this->xml->startElement( 'a' );
		$this->xml->writeAttribute('href', "playerPage.php?player_name=$player_name");
		$this->xml->text($player_name);
		$this->xml->endElement();  // a
	}
	public function findByTeamName($team_name){
		try{
			$sql = "Select * from Team Where team_name = ?";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,"$team_name" );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return  $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}	
	}
	
	public function writeTName($team_name){
		$this->xml->startElement( 'a' );
		$this->xml->writeAttribute('href', "teamPage.php?team_name=$team_name");
		$this->xml->text($team_name);
		$this->xml->endElement();  // a
	}
	
	public function selectTeam($team_id){
		$sql = 'Select * FROM Team WHERE team_id=?';
		$stmt = $this->conn->prepare( $sql );
		$i=1;
		$stmt->bindValue( $i++, $team_id );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		return $stmt->fetchAll();	
	}	
	public function writeNews($news){
		try{
			$sql = "INSERT INTO News (news) VALUES (?)";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$news );
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->execute();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	
	}	
	public function selectTeams(){
		$sql = 'Select * FROM Team';
		$stmt = $this->conn->prepare( $sql );
		$i=1;
		$stmt->bindValue( $i++, $team_id );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		
		return $stmt->fetchAll();
	}
	public function selectYears(){
		try {
			$sql = "Select DISTINCT year From Team_Roster ORDER BY year";
			$stmt = $this->conn->prepare( $sql );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			
			return $stmt->fetchAll();
			
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function createTeam(&$data){
		try {
			$tname=$data['team_name'];
			$pw=$data['password'];
			$sql = "INSERT INTO Team (team_name,password) VALUES ('$tname','$pw')";
			$stmt = $this->conn->prepare( $sql );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			$string="$tname has joined the league";
			$this->writeNews($string);
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function getPlayerNameTeamName($team_id,$player_id){
		$sql = 'Select * from v_team_roster Where player_id = ? AND team_id = ?;';
		
		$stmt = $this->conn->prepare( $sql );
		$i=1;
		$stmt->bindValue( $i++, $player_id );
		$stmt->bindValue( $i++, $team_id );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		return $stmt->fetchAll();
	}
	public function insertPlayer(&$team_id,&$player_id){
		$sql = 'INSERT INTO Team_Roster (team_id, player_id, year) VALUES (?, ?, YEAR(CURDATE()));';
		$stmt = $this->conn->prepare( $sql );
		$i=1;
		$stmt->bindValue( $i++, $team_id );
		$stmt->bindValue( $i++, $player_id );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$results=$this->getPlayerNameTeamName($team_id, $player_id);
		$result=$results[0];
		
		$p_name=$result['player_name'];
		$t_name=$result['team_name'];
		$string="$p_name has been added to the $t_name";
		print($string);
		$this->writeNews($string);
				
		return ;
	}

	public function cutPlayer(&$team_id,&$player_id){
		$sql = 'DELETE FROM Team_Roster WHERE player_id=? AND team_id=? AND year=YEAR(CURDATE())';
		$stmt = $this->conn->prepare( $sql );
		$i=1;
		$stmt->bindValue( $i++, $player_id );
		$stmt->bindValue( $i++, $team_id );
		$results=$this->getPlayerNameTeamName($team_id, $player_id);
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$result=$results[0];
		$p_name=$result['player_name'];
		$t_name=$result['team_name'];
		$string="$p_name has been cut by the $t_name";
		$this->writeNews($string);
		print("$string");
		return; 
	}
	public function updateTeam(&$data,&$team_id){
		try{
			$team_name=$data['team_name'];
			$sql = 'UPDATE Team SET';
			$team_captain=$data['captain'];
			$password=$data["password"];
			$count=0;
			if($team_name!=''){
				$sql .= " team_name = '$team_name'";
				$count=$count+1;
			}if($team_captain!=''){
				if($count>0){
					$sql .=",";
				}
				$sql .= " captain = '$team_captain' ";
				$count=$count+1;
			}
			if($password!=''){
				if($count>0){
					$sql .=",";
				}
				$sql .= " password = '$password'";
				$count=$count+1;
			}
			if($sql!='Update Team SET'){
				$sql .=" WHERE team_id =$team_id;";
				print($sql);
				$stmt= $this->conn->prepare($sql);
				print($sql);
				return $stmt->execute();
			}
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectFreePlayers(){
		try {
			$sql = 'Select * from Player AS p WHERE p.player_id NOT IN (SELECT player_id from Team_Roster where year=YEAR(CURDATE())) ORDER BY p.player_name';
			$stmt = $this->conn->prepare( $sql );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectCurrentRoster($team_id){
		
		$sql = 'Select * from v_team_roster WHERE team_id=? AND Year=YEAR(CURDATE())';
		$i = 1;
		$stmt = $this->conn->prepare( $sql );
		$stmt->bindValue( $i++, $team_id );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		return $stmt->fetchAll();
	}
	public function selectRoster($team_id,$year){
		$sql = 'Select * from v_team_roster WHERE team_id=? AND Year=?';
		$stmt = $this->conn->prepare( $sql );
		$i=1;
		$stmt->bindValue( $i++, $team_id );
		$stmt->bindValue( $i++, $year );
		$stmt->execute();
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		return $stmt->fetchAll();
	}
	
	public function selectByTeam( &$data ) {
		$sql = 'SELECT * FROM v_team_wins WHERE team_id = ?';
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, $data['team_id'] );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$stmt->execute();
		return $stmt->fetchAll();
	}
	public function selectAll() {
		try {
			$sql = 'SELECT * FROM v_team_wins ORDER BY team_name';
			$stmt = $this->conn->prepare( $sql );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();

		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function insert(&$data){
		$sql='INSERT INTO Team (team_name,captain) VALUES (?,?)';
		$stmt= $this->conn->prepare($sql);
		$i=1;
		$stmt->bindValue( $i++, $data['team_name'] );
		$stmt->bindValue( $i++, $data['captain'] );
		return $stmt->execute();
	}
	public function updateTeamName( &$data ) {
		$sql = 'UPDATE Team SET team_name = ? WHERE team_id = ?';
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, $data['team_name'] );
		$stmt->bindValue( $i++, $data['team_id'] );
		return $stmt->execute();
	}
	public function insertTeamPic(&$data,&$file,&$team_id){
		try{
			print("The team id");
			print($team_id);
			$info = getimagesize( $file['image']['tmp_name'] );
			if( !$info ) {
				// getimagesize returns false - not a valid image file.
				print( '<p>Bad image file.</p>' );
			} else {
				// Get a file handle for the image file.
				$fp = fopen( $file['image']['tmp_name'], 'rb' );
				// Define the SQL INSERT statement.
				$sql='UPDATE Team SET height=?,width=?,mime_type=?,team_pic=? WHERE team_id= ?';
				$stmt = $this->conn->prepare( $sql );
				$i = 1;
				// The member ID has already been assigned.
				$stmt->bindValue( $i++, $info[1] );
				$stmt->bindValue( $i++, $info[0] );
				$stmt->bindValue( $i++, $info['mime'] );
				$stmt->bindValue( $i++, $fp, PDO::PARAM_LOB );
				$stmt->bindValue( $i++, $team_id );
				$stmt->execute();
			}
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		fclose($fp);
	}
	public function displayOption( &$teams, $selected = array() ) {
		foreach( $teams as $team ) {
			displayOption( $this->xml, $team['team_id'], $team['team_name'], $selected );
		}
		return;
	}
	public function displayOptionPlayer( &$players, $selected = array() ) {
		foreach( $players as $player ) {
			displayOption( $this->xml, $player['player_id'], $player['player_name'], $selected );
		}
		return;
	}
	public function displayOptionYear( &$years, $selected = array() ) {
		foreach( $years as $year ) {
			displayOption( $this->xml, $year['year'], $year['year'], $selected );
		}
		return;
	}
	public function displayTeamInfo( &$team ) {
		// Write out the name.
		$id=$team['team_id'];			
		// Write out the address and institution table.
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
			
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Team: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );

		$this->writeTName($team['team_name']);
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		//Scale the phone height and width properly using formula h*x=310, x = h/310 to obtain consistent scale for every image
		$height = $team['height'];
		$width = $team['width'];
		$x = 300/$height;
		$width *= $x;
		$height *= $x;
		if ($height!=null){
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"image.php?id=$id");
			$this->xml->writeAttribute('width',200);
			$this->xml->writeAttribute('height',200);
			$this->xml->endElement();
		}else{
			$id=2;
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pic.php?id=$id");
			$this->xml->writeAttribute('width',200);
			$this->xml->writeAttribute('height',200);
			$this->xml->endElement();
		}
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Captain: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['captain'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Championships: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['Champs_Win'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)


		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Goals For: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['goals_for'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Goals Against: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['goals_against'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)


		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Goals Against: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['goals_against'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Pim: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['team_pim'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)


		$this->xml->endElement();  // end the table
	}

	public function displayTeamInfoNoLink( &$team ) {
		// Write out the name.
		$id=$team['team_id'];
		// Write out the address and institution table.
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
			
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Team: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
	
		$this->xml->text($team['team_name']);
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		//Scale the phone height and width properly using formula h*x=310, x = h/310 to obtain consistent scale for every image
		$height = $team['height'];
		$width = $team['width'];
		$x = 300/$height;
		$width *= $x;
		$height *= $x;
		if ($height!=null){
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"image.php?id=$id");
			$this->xml->writeAttribute('width',200);
			$this->xml->writeAttribute('height',200);
			$this->xml->endElement();
		}else{
			$id=2;
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pic.php?id=$id");
			$this->xml->writeAttribute('width',200);
			$this->xml->writeAttribute('height',200);
			$this->xml->endElement();
		}
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Captain: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['captain'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Championships: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['Champs_Win'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Goals For: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['goals_for'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Goals Against: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['goals_against'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Goals Against: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['goals_against'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Total Pim: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $team['team_pim'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
	
		$this->xml->endElement();  // end the table
	}
	
	
	public function displayPlayer(&$team){
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->writeName($team['player_name']);
		#$this->xml->text( $team['player_name'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		print( $this->xml->outputMemory() . PHP_EOL );
		
	}
	
	public function displayRoster($team){
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 1 );
		$this->xml->writeAttribute( 'border', 0 );
			
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Current Roster' );
		$this->xml->endElement();  // end the em		
		$this->xml->endElement();  // end the table row (tr)
		
		foreach($team as $player){
			$this->xml->startElement( 'tr' );
			$this->writeName($player['player_name']);
			$this->xml->endElement();  // end the table row (tr)	
		}
		$this->xml->endElement();  // end the table 	
		
	}
	public function displayTeamName($team_id){
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Team Name' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$teams=$this->selectTeam($team_id);
		$team=$teams[0];
		$this->xml->text( $team['team_name'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		
		$this->xml->endElement();  // end the table
		print( $this->xml->outputMemory() . PHP_EOL );	
	}
	public function formAddPic(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'action', 'teamAddPic.php' );
		$this->xml->writeAttribute( 'enctype', 'multipart/form-data' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'type', 'hidden' );
		$this->xml->writeAttribute( 'name', 'MAX_FILE_SIZE' );
		$this->xml->writeAttribute( 'value', '1000000' );
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'file' );
		$this->xml->text( 'File to Upload ' );
		$this->xml->endElement();  // label
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'type', 'file' );
		$this->xml->writeAttribute( 'name', 'image' );
		$this->xml->endElement();  // input
		$this->xml->endElement();  // p
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Insert' );
		$this->xml->endElement();  // input
		$this->xml->endElement();  // p
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function formAddPlayer(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'teamAddPlayer' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'teamAddPlayer.php' );
		
		// Display list of models.
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'player_id' );
		$this->xml->text( 'Available Players: ' );
		$this->xml->endElement();  // label
		// Display a drop-down list of all models.
		$player = $this->selectFreePlayers();
		$this->xml->startElement( 'select' );
		$this->xml->writeAttribute( 'name', 'player_id' );
		$this->xml->writeAttribute( 'id', 'player_id' );
		$this->xml->writeAttribute( 'size', 1 );
		$this->displayOptionPlayer( $player, array() );
		$this->xml->endElement();  // end 'select'
		
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Add Player' );
		$this->xml->endElement();  // input
		
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function formCutPlayer(&$team_id){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'teamCutPlayer' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'teamCutPlayer.php' );
		
		// Display list of models.
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'player_id' );
		$this->xml->text( 'Current Players: ' );
		$this->xml->endElement();  // label
		// Display a drop-down list of all models.
		$player = $this->selectCurrentRoster($team_id);
		$this->xml->startElement( 'select' );
		$this->xml->writeAttribute( 'name', 'player_id' );
		$this->xml->writeAttribute( 'id', 'player_id' );
		$this->xml->writeAttribute( 'size', 1 );
		$this->displayOptionPlayer( $player, array() );
		$this->xml->endElement();  // end 'select'
		
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Cut Player' );
		$this->xml->endElement();  // input
		
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function formCurrentRoster(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'teamCurrentRoster' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'teamCurrentRoster.php' );
		
		// Display list of models.
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'team_id' );
		$this->xml->text( 'Teams: ' );
		$this->xml->endElement();  // label
		// Display a drop-down list of all models.
		$teams = $this->selectAll();
		$this->xml->startElement( 'select' );
		$this->xml->writeAttribute( 'name', 'team_id' );
		$this->xml->writeAttribute( 'id', 'team_id' );
		$this->xml->writeAttribute( 'size', 1 );
		$this->displayOption( $teams, array() );
		$this->xml->endElement();  // end 'select'
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Display' );
		$this->xml->endElement();  // input
		
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function formRoster(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'teamRoster' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'teamRoster.php' );
	
		// Display list of models.
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'team_id' );
		$this->xml->text( 'Teams: ' );
		$this->xml->endElement();  // label
		// Display a drop-down list of all models.
		$teams = $this->selectAll();
		$this->xml->startElement( 'select' );
		$this->xml->writeAttribute( 'name', 'team_id' );
		$this->xml->writeAttribute( 'id', 'team_id' );
		$this->xml->writeAttribute( 'size', 1 );
		$this->displayOption( $teams, array() );
		$this->xml->endElement();  // end 'select'
	
		// Display list of models.
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'year' );
		$this->xml->text( 'Year: ' );
		$this->xml->endElement();  // label
		// Display a drop-down list of all models.
		$years = $this->selectYears();
		$this->xml->startElement( 'select' );
		$this->xml->writeAttribute( 'name', 'year' );
		$this->xml->writeAttribute( 'id', 'year' );
		$this->xml->writeAttribute( 'size', 1 );
		$this->displayOptionYear( $years, array() );
		$this->xml->endElement();  // end 'select'
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Display' );
		$this->xml->endElement();  // input
	
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function updateTeamProfile(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'teamUpdate' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'teamUpdate.php' );
		
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Team Name ' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'team_name' );
		$this->xml->writeAttribute( 'id', 'team_name' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 60 );
		$this->xml->writeAttribute( 'maxlength', 60 );
		$this->xml->writeAttribute( 'value', $team[0]['team_name'] );
		$this->xml->endElement();  // input
		
		
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Captain ' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'captain' );
		$this->xml->writeAttribute( 'id', 'captain' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 25 );
		$this->xml->writeAttribute( 'maxlength', 25 );
		$this->xml->writeAttribute( 'value', $team[0]['captain'] );
		$this->xml->endElement();  // input
		
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Password' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'password' );
		$this->xml->writeAttribute( 'id', 'password' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 20 );
		$this->xml->writeAttribute( 'maxlength', 20 );
		$this->xml->writeAttribute( 'value', $team[0]['password'] );
		$this->xml->endElement();  // input
		
		
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Update' );
		$this->xml->endElement();  // input
		$this->xml->endElement(); // p
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function formTeamSignIn(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'team_loginPortal' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'home.php' );
		
		$this->xml->startElement( 'p' );
		// Display list of team.
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'year' );
		$this->xml->text( 'Team Name ' );
		$this->xml->endElement();  // label
		// Display a drop-down list of all models.
		$teams = $this->selectTeams();
		$this->xml->startElement( 'select' );
		$this->xml->writeAttribute( 'name', 'year' );
		$this->xml->writeAttribute( 'id', 'year' );
		$this->xml->writeAttribute( 'size', 1 );
		$this->displayOption( $teams, array() );
		$this->xml->endElement();  // end 'select'
		$this->xml->endElement();  // end 'p'
		
		
		if ($_SESSION['empty_pass'] == TRUE){
			echo "*No password entered!";
		}
		$this->xml->startElement( 'p' );
		
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Password' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'password' );
		$this->xml->writeAttribute( 'id', 'password' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 20 );
		$this->xml->writeAttribute( 'maxlength', 20 );
		$this->xml->writeAttribute( 'value', $team[0]['password'] );
		$this->xml->endElement();  // input
		$this->xml->endElement();  // end 'p'
		
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Log In' );
		$this->xml->endElement();  // input
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'button' );
		$this->xml->writeAttribute( 'value', 'Home' );
		$this->xml->writeAttribute( 'onClick', "window.location='home.php'" );
		$this->xml->endElement();  // input
		
		$this->xml->endElement(); // p
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function formCreateTeam(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'createTeam' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'createTeam.php' );
		
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Team Name' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'team_name' );
		$this->xml->writeAttribute( 'id', 'team_name' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 30 );
		$this->xml->writeAttribute( 'maxlength', 30 );
		$this->xml->writeAttribute( 'value', $team[0]['team_name'] );
		$this->xml->endElement();  // input
		
		
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Password' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'password' );
		$this->xml->writeAttribute( 'id', 'password' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 20 );
		$this->xml->writeAttribute( 'maxlength', 20 );
		$this->xml->writeAttribute( 'value', $team[0]['password'] );
		$this->xml->endElement();  // input
		
		
		
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'insert' );
		$this->xml->writeAttribute( 'type', 'submit' );
		$this->xml->writeAttribute( 'value', 'Join' );
		$this->xml->endElement();  // input
		$this->xml->endElement(); // p
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
		
	}
	

}
?>