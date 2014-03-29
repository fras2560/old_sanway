<?php 
require_once( "utilities.php" );

class player{
	private $conn = null; // Connection information and utilities.
	private $xml = null;

	//-------------------------------------------------------------------------
	public function __construct( &$conn, &$xml ) {
		$this->conn = $conn;
		$this->xml = $xml;
	}
	public function writeName($player_name){
		$this->xml->startElement( 'a' );
		$this->xml->writeAttribute('href', "playerPage.php?player_name=$player_name");
		$this->xml->text($player_name);
		$this->xml->endElement();  // a
		
	}
	public function playerFindByName($player_name){
		try{
			$sql = "SELECT * FROM Player Where player_name LIKE ? ";
			$stmt = $this->conn->prepare( $sql );
			$i = 1;
			$stmt->bindValue( $i++, "$player_name%" );
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			$stmt->execute();
			return $results=$stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function getPlayerId($player_name){
		try{
			$sql = "SELECT * FROM Player Where player_name LIKE ? ";
			$stmt = $this->conn->prepare( $sql );
			$i = 1;
			$stmt->bindValue( $i++, "$player_name%" );
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			$stmt->execute();
			return $results=$stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function writeTName($team_name){
		$this->xml->startElement( 'a' );
		$this->xml->writeAttribute('href', "teamPage.php?team_name=$team_name");
		$this->xml->text($player_name);
		$this->xml->endElement();  // a
	}
	
	public function selectPreviousTeams($player_name){
		try{
			$sql = "SELECT * FROM v_team_roster WHERE player_name LIKE ? ORDER BY Year DESC";
			$stmt = $this->conn->prepare( $sql );
			$i = 1;
			$stmt->bindValue( $i++, "$player_name%" );
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			$stmt->execute();
			return $stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectPlayersStats($player_name){
		try{
		$sql = "SELECT * FROM v_player_stats WHERE Name LIKE ? ORDER BY YEAR";
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, "$player_name%" );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$stmt->execute();
		return $stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function findPlayer(&$playername){
		try{
			$sql = 'SELECT * FROM v_player WHERE Name LIKE ? ORDER BY Name';
			$stmt = $this->conn->prepare( $sql );
			$i = 1;
			$stmt->bindValue( $i++, "$playername%" );
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			$stmt->execute();
			return $stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function findByPlayerName(&$data){
		try{
		$name=$data['player_name'];
		$sql = 'SELECT * FROM v_player WHERE Name LIKE ? ORDER BY Name';
		print($name);
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, "$name%" );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$stmt->execute();
		return $stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectByPlayerName( &$data ) {
		try {
		$name=$data['player_name'];
		$sql = 'SELECT * FROM v_player_stats WHERE Name LIKE ? ORDER BY Name, YEAR';
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, "$name%" );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$stmt->execute();
		return $stmt->fetchAll();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectAll() {
		try {
			$sql = 'SELECT * FROM v_player ORDER BY Name';
			$stmt = $this->conn->prepare( $sql );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectStatYears(){
		try {
			$sql = 'Select DISTINCT s_year From Player_Stats ORDER By s_year';
			$stmt = $this->conn->prepare( $sql );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectPlayer(&$data){
		try {
			$name=$data['player_id'];
			$sql = 'SELECT * FROM v_player where player_id = ?';
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$name );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function writeNews($news){
		try{
			$sql = "INSERT INTO News (news) VALUES (?)";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$news );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return ;
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		
	}
	
	public function playerJoin(&$data){
		try {
			$playername=$data['player_name'];
			$pw=$data['password'];
			$sql = "INSERT INTO Player (player_name,password) VALUES ('$playername','$pw')";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			//$stmt->bindValue( $i++,$date['player_name'] );
			//$stmt->bindValue( $i++,$date['password'] );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			$news="$playername has joined the league";
			$this->writeNews($news);
			return ;
		}catch( Exception $e ) {
			
			msg( $this->xml, 'Unable to Join. Check to see if you are already register. If problems continue contact fras2560@mylaurier.ca' );
		}
	}
	public function selectLeadersAssists(&$year){
		try {
			$sql = "SELECT * FROM v_player_stats WHERE YEAR=? ORDER BY Assists DESC LIMIT 5;";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$year );
			//$stmt->bindValue( $i++,$category);
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectLeadersGoals(&$year){
		try {
			$sql = "SELECT * FROM v_player_stats WHERE YEAR=? ORDER BY Goals DESC LIMIT 5;";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$year );
			//$stmt->bindValue( $i++,$category);
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectLeadersPoints(&$year){
		try {
			$sql = "SELECT * FROM v_player_stats WHERE YEAR=? ORDER BY Points DESC LIMIT 5;";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$year );
			//$stmt->bindValue( $i++,$category);
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectLeadersGamesPlayed(&$year){
		try {
			$sql = "SELECT * FROM v_player_stats WHERE YEAR=? ORDER BY Games_Played DESC LIMIT 5;";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$year );
			//$stmt->bindValue( $i++,$category);
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectLeadersPPG(&$year){
		try {
			$sql = "SELECT * FROM v_player_stats WHERE YEAR=? ORDER BY Points_per_game DESC LIMIT 5;";
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->bindValue( $i++,$year );
			//$stmt->bindValue( $i++,$category);
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	
	
	public function selectCurrentLeadersAssists(){
		try {
			$sql = 'SELECT * FROM fras2560.v_player_stats WHERE YEAR=YEAR(CURDATE()) ORDER BY Assists DESC LIMIT 5 ;';
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectCurrentLeadersGoals(){
		try {
			$sql = 'SELECT * FROM fras2560.v_player_stats WHERE YEAR=YEAR(CURDATE()) ORDER BY Goals DESC LIMIT 5 ;';
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectCurrentLeadersPoints(){
		try {
			$sql = 'SELECT * FROM fras2560.v_player_stats WHERE YEAR=YEAR(CURDATE()) ORDER BY Points DESC LIMIT 5 ;';
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function selectCurrentLeadersGamesPlayed(){
		try {
			$sql = 'SELECT * FROM fras2560.v_player_stats WHERE YEAR=YEAR(CURDATE()) ORDER BY Games_Played DESC LIMIT 5 ;';
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectCurrentLeadersPPG(){
		try {
			$sql = 'SELECT * FROM fras2560.v_player_stats WHERE YEAR=YEAR(CURDATE()) ORDER BY Points_per_game DESC LIMIT 5 ;';
			$stmt = $this->conn->prepare( $sql );
			$i=1;
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function insert(&$data){
		$sql='INSERT INTO Player (player_name) VALUES (?)';
		$stmt= $this->conn->prepare($sql);
		$i=1;
		$stmt->bindValue( $i++, $data['player_name'] );
		return $stmt->execute();
	}
	
	public function updateProfile(&$data,&$player_id){
		try{
			//$player_id=$data['player_id'];
			$quote=$data['quote'];
			$sql = 'UPDATE Player SET';
			$favp=$data['Favourite_Player'];
			$yp=$data["Years_Played"];
			$pw=$data['password'];
			$count=0;
			$sql='Update Player SET';
			if($favp!=''){
				$sql .= " fav_player = '$favp'";
				$count=$count+1;
			}if($yp!=''){
				if($count>0){
					$sql .=",";
				}
				$num=(int)$yp;
				$max=date('Y')-2007;
				if($num>$max){
					throw new Exception("Can not have played more then $max years ");
				}
				$sql .= " years_played = $yp ";
				$count=$count+1;
			}
			
			if($quote!=''){
				if($count>0){
					$sql .=",";
				}
				$sql .= " quote = '$quote'";
				$count=$count+1;
			}
			if($pw!=''){
				if($count>0){
					$sql .=",";
				}
				$sql .= " password = '$pw'";
				$count=$count+1;
			}
				
			if($sql!='Update Player SET'){
				$sql .=" WHERE player_id =$player_id;";
				$stmt= $this->conn->prepare($sql);
				print($sql);
				return $stmt->execute();
			}
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}

	public function displayOptionYear( &$years, $selected = array() ) {
		foreach( $years as $year ) {
			displayOption( $this->xml, $year['s_year'], $year['s_year'], $selected );
		}
		return;
	}
	public function displayOption( &$players, $selected = array() ) {
		foreach( $players as $player ) {
			displayOption( $this->xml, $player['player_id'], $player['Name'], $selected );
		}
		return;
	}

	public function insertPlayerPic(&$data,&$file, &$player_id){
		try{
			$info = getimagesize( $file['image']['tmp_name'] );
			if( !$info ) {
				// getimagesize returns false - not a valid image file.
				print( '<p>Bad image file.</p>' );
			} else {
				// Get a file handle for the image file.
				$fp = fopen( $file['image']['tmp_name'], 'rb' );
				// Define the SQL INSERT statement.
				$sql='UPDATE Player SET height=?,width=?,mime_type=?,picture=? WHERE player_id= ?';
				$stmt = $this->conn->prepare( $sql );
				$i = 1;
				// The member ID has already been assigned.
				$stmt->bindValue( $i++, $info[1] );
				$stmt->bindValue( $i++, $info[0] );
				$stmt->bindValue( $i++, $info['mime'] );
				$stmt->bindValue( $i++, $fp, PDO::PARAM_LOB );
				$stmt->bindValue( $i++, $player_id );
				$stmt->execute();
			}
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		fclose($fp);
	}

	public function displayPlayerInfo( &$player ) {
		// Write out the name.
		$id=$player	['player_id'];
		// Write out the address and institution table.
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
			
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Name: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->writeName($player['Name']);
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		//Scale the phone height and width properly using formula h*x=310, x = h/310 to obtain consistent scale for every image
		$height = $player['height'];
		$width = $player['width'];
		$x = 100/$height;
		$width *= $x;
		$height *= $x;
		if ($height !=null){
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pimage.php?id=$id");
			$this->xml->writeAttribute('width',100);
			$this->xml->writeAttribute('height',100);
			$this->xml->endElement();
		}else{
			$id=1;
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pic.php?id=$id");
			$this->xml->writeAttribute('width',100);
			$this->xml->writeAttribute('height',100);
			$this->xml->endElement();
		}
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Favourit Player: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Favourite_Player'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Quote: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Quote'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'MVP Titles ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['MVP_Titles'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Championships: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Championships'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)


		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Career Points ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Career_Points'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)

		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Power Ranking ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Power_Ranking'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Years Played ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Years_Played'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		$this->xml->endElement();  // end the table
	}

	public function displayPlayerInfoNoLink( &$player ) {
		// Write out the name.
		$id=$player	['player_id'];
		// Write out the address and institution table.
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
			
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Name: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text($player['Name']);
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		//Scale the phone height and width properly using formula h*x=310, x = h/310 to obtain consistent scale for every image
		$height = $player['height'];
		$width = $player['width'];
		$x = 100/$height;
		$width *= $x;
		$height *= $x;
		if ($height !=null){
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pimage.php?id=$id");
			$this->xml->writeAttribute('width',100);
			$this->xml->writeAttribute('height',100);
			$this->xml->endElement();
		}else{
			$id=1;
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pic.php?id=$id");
			$this->xml->writeAttribute('width',100);
			$this->xml->writeAttribute('height',100);
			$this->xml->endElement();
		}
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Favourit Player: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Favourite_Player'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Quote: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Quote'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'MVP Titles ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['MVP_Titles'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Championships: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Championships'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Career Points ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Career_Points'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
	
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Power Ranking ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Power_Ranking'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Years Played ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Years_Played'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		$this->xml->endElement();  // end the table
	}
	
	public function displayLeader( &$player,&$category ) {
		// Write out the name.
		$id=$player	['player_id'];
		
		$this->xml->startElement( 'table' );
		
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 1 );
		$this->xml->writeAttribute( 'border', 5 );
		$this->xml->writeAttribute( 'width', 250 );
		$this->xml->startElement( 'tr' );
		
		$height = $player['height'];
		$width = $player['width'];
		$x = 50/$height;
		$width *= $x;
		$height *= $x;
		$this->xml->startElement( 'td' );
		
		if ($height !=null){
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pimage.php?id=$id");
			$this->xml->writeAttribute('width',100);
			$this->xml->writeAttribute('height',100);
			$this->xml->endElement();
		}else{
			$id=1;
			$this->xml->startElement('image');
			$this->xml->writeAttribute('src',"pic.php?id=$id");
			$this->xml->writeAttribute('width',100);
			$this->xml->writeAttribute('height',100);
			$this->xml->endElement();
		}
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		
		$this->writeName($player['Name']);
		#$this->xml->text( $player['Name'] );
		
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $player[$category] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		$this->xml->endElement();  // end the table
	}
	
	public function displayPlayerStatInfo(&$player){
		// Write out the name.
		$id=$player	['player_id'];
		// Write out the address and institution table.
		$this->xml->startElement( 'tr' );

		$this->xml->startElement( 'td' );
		$this->xml->text( $player['YEAR'] );
		$this->xml->endElement();  // end the table cell (td)

		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Goals'] );
		$this->xml->endElement();  // end the table cell (td)

		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Assists'] );
		$this->xml->endElement();  // end the table cell (td)

		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Games_Played'] );
		$this->xml->endElement();  // end the table cell (td)

		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Points'] );
		$this->xml->endElement();  // end the table cell (td)

		$this->xml->startElement( 'td' );
		$this->xml->text( $player['Points_per_game'] );
		$this->xml->endElement();  // end the table cell (td)

		$this->xml->endElement();  // end the table cell (tr)

	}
	public function formAddPic(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'action', 'playerAddPic.php' );
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
		$this->xml->writeAttribute( 'value', 'Add Pic' );
		$this->xml->endElement();  // input
		$this->xml->endElement();  // p
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function formSearch(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'playerFindPlayer' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'playerFindPlayer.php' );

		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'player_id' );
		$this->xml->text( 'Player Search ' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'player_name' );
		$this->xml->writeAttribute( 'id', 'player_name' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 20 );
		$this->xml->writeAttribute( 'maxlength', 20 );
		$this->xml->writeAttribute( 'value', $player[0]['player_name'] );
		$this->xml->endElement();  // input
		$this->xml->endElement(); // p
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function formUpdateProfile(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'playerUpdate' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'playerUpdate.php' );

		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Quote ' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'quote' );
		$this->xml->writeAttribute( 'id', 'quote' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 40 );
		$this->xml->writeAttribute( 'maxlength', 40 );
		$this->xml->writeAttribute( 'value', $player[0]['Quote'] );
		$this->xml->endElement();  // input

		
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Years Played ' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'Years_Played' );
		$this->xml->writeAttribute( 'id', 'Years_Played' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 2 );
		$this->xml->writeAttribute( 'maxlength', 2 );
		$this->xml->writeAttribute( 'value', $player[0]['Years_Played'] );
		$this->xml->endElement();  // input

		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Favourite Player ' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'Favourite_Player' );
		$this->xml->writeAttribute( 'id', 'Favourite_Player' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 20 );
		$this->xml->writeAttribute( 'maxlength', 20 );
		$this->xml->writeAttribute( 'value', $player[0]['Favourite_Player'] );
		$this->xml->endElement();  // input

		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Password ' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'password' );
		$this->xml->writeAttribute( 'id', 'password' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size',20  );
		$this->xml->writeAttribute( 'maxlength', 20 );
		$this->xml->writeAttribute( 'value', $player[0]['password'] );
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
	public function formYearStats(){
		
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'playerYearStats' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'playerYearStats.php' );
		// Display list of models.
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'year' );
		$this->xml->text( 'Year: ' );
		$this->xml->endElement();  // label
		// Display a drop-down list of all models.
		$years = $this->selectStatYears();
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
	public function formPlayerJoin(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'playerJoin' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'playerJoin.php' );
		
		$this->xml->startElement( 'p' );
		$this->xml->startElement( 'label' );
		$this->xml->writeAttribute( 'for', 'quote' );
		$this->xml->text( 'Full Name' );
		$this->xml->endElement();  // end 'label'
		$this->xml->startElement( 'input' );
		$this->xml->writeAttribute( 'name', 'player_name' );
		$this->xml->writeAttribute( 'id', 'player_name' );
		$this->xml->writeAttribute( 'type', 'text' );
		$this->xml->writeAttribute( 'size', 20 );
		$this->xml->writeAttribute( 'maxlength', 20 );
		$this->xml->writeAttribute( 'value', $player[0]['player_name'] );
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
		$this->xml->writeAttribute( 'value', $player[0]['password'] );
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