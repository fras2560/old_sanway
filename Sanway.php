<?php 
$SAN = '/home/fras2560/public_html';
require_once("$SAN/utilities.php");
require_once( "$SAN/connectionClass.php" );
require_once( "$SAN/teamClass.php" );
require_once("$SAN/playerClass.php");
require_once("$SAN/championshipsClass.php");
require_once("$SAN/pictureClass.php");
require_once("$SAN/newsClass.php");
//-------------------------------------------------------------------------

class sanPublic {
	const SETTINGS_FILE = 'settings.ini';
	private $player = null;
	private $conn = null;
	private $xml = null;
	private $team = null;
	private $championship = null;
	private $player_stats = null;
	private $goalie_stats = null;
	private $team_roster = null;
	private $channel = null;
	private $san = null;
	private $picture=null;
	private $news=null;
	//-------------------------------------------------------------------------
	/*
	 Initialize the DCRIS connection and XML writing objects.
	*/
	public function __construct() {
		try {
			$this->xml = new XMLWriter();
			$this->xml->openMemory();
			$this->xml->setIndentString( '  ' );
			$this->xml->setIndent( true );
			// Initialize the other classes.
			$this->san = new Connection( self::SETTINGS_FILE );
			$this->conn = $this->san->getConnection();
			$this->team = new team($this->conn, $this->xml  );
			$this->championship = new championships($this->conn, $this->xml  );
			$this->player = new player($this->conn, $this->xml  );
			$this->picture = new picture($this->conn, $this->xml  );		
			$this->news=new news($this->conn, $this->xml);
			}
		catch  (Exception $e) {
			print($e->getMessage().PHP_EOL);
		}
	}
	//-------------------------------------------------------------------------
	// Display Functions
	//-------------------------------------------------------------------------
	public function printit(){
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function getTeamId($team_name){
		try{
			$results=$this->team->getTeamId($team_name);
			$team_id=$results[0]['team_id'];
			return $team_id;
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function getPlayerId($player_name){
		try{
			$results=$this->player->getPlayerId($player_name);
			$player_id=$results[0]['player_id'];
			return $player_id;
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function displayAllnews(){
		try{
			$this->news->displayAllNews();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function writeLink($page,$text){
		$this->xml->startElement( 'a' );
		$this->xml->writeAttribute('href', "$page");
		$this->xml->text("$text");
		$this->xml->endElement();  // a
		
	}
		
	public function displayPic($pic_id){
		try{
			$this->picture->diplayPic($pic_id);
		} catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function displayNews(){
		try{
			$this->news->displayNews();
		}catch(Exception $e){
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function displayTeam() {
		try {
			if( isset( $_POST['byTeam'] ) ) {
				// Display the result of a name search.
				$team = $this->team->selectByTeam( $_POST );
			} else {
				// Display all members.
				$team = $this->team->selectAll();
			}
			$this->displayTeamEntries( $team );
		} catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function displayTeamLoginForm(){
		try {
			$this->team->formTeamSignIn();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		
	}
	public function displayChampionship(){
		try {
			$this->championship->displayChampionship($_POST);
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}	
	}
	public function championshipSelectForm(){
		try {
			$this->championship->championshipSelectForm();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function addPicForm(){
		try {
			$this->picture->InsertPicForm();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function addPic(){
		try{
			$this->picture->insertPic($_POST,$_FILES);	
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function displayTeamProfile($team_name){
		try{
			$result=$this->team->findByTeamName($team_name);
			$team=$result[0];
			$this->team->displayTeamInfoNoLink($team);	
			$team=$this->team->getCurrentRoster($team_name);
			$this->team->displayRoster($team);
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function displayPlayerProfile($player_name){
		try{
			
			$stats=$this->player->selectPlayersStats($player_name);
			if($stats==null){
				$players=$this->player->findPlayer($player_name);
				$this->player->displayPlayerInfoNoLink($players[0]);	
			}
			$this->displayPlayerStatsEntriesNoLink($stats);
			$teams=$this->player->selectPreviousTeams($player_name);
			$this->xml->startElement( 'table' );
			$this->xml->writeAttribute( 'cellpadding', 1 );
			$this->xml->writeAttribute( 'cellspacing', 1 );
			$this->xml->writeAttribute( 'border', 5 );
			$this->xml->writeAttribute( 'width', 250 );
			$this->xml->startElement( 'strong' );
			$this->xml->text( 'Previous Teams' );
			$this->xml->endElement();  // end the em
			$this->xml->startElement( 'tr' );
			$this->xml->endElement();  // end the table row (tr)
					
			foreach ($teams as $team){
				$this->xml->startElement( 'tr' );
				$this->xml->text($team['Year']);
				$this->xml->text(': ');
				$this->xml->text( $team['team_name'] );	
				$this->xml->endElement();  // end the table row (tr)
				
			}
			$this->xml->endElement();  // end the table
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function displayLeadersYear(&$category){
		try {
			if($category=='Assists'){
			$players = $this->player->selectLeadersAssists(&$_POST['year']);
			}else if($category=='Goals'){
			$players = $this->player->selectLeadersGoals(&$_POST['year']);
			}else if($category=='Points'){
			$players = $this->player->selectLeadersPoints(&$_POST['year']);
			}else if($category=='Games_Played'){
			$players = $this->player->selectLeadersGamesPlayed(&$_POST['year']);
			}else if($category=='Points_per_game'){
			$players = $this->player->selectLeadersPPG(&$_POST['year']);
			}
			$this->displayLeaderEntries( $players,$category );
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function displayLeaders(&$category){
		try {
			if($category=='Assists'){
			$players = $this->player->selectCurrentLeadersAssists(&$_POST['year']);
			}else if($category=='Goals'){
			$players = $this->player->selectCurrentLeadersGoals(&$_POST['year']);
			}else if($category=='Points'){
			$players = $this->player->selectCurrentLeadersPoints(&$_POST['year']);
			}else if($category=='Games_Played'){
			$players = $this->player->selectCurrentLeadersGamesPlayed(&$_POST['year']);
			}else if($category=='Points_per_game'){
			$players = $this->player->selectCurrentLeadersPPG(&$_POST['year']);
			}
			$this->displayLeaderEntries( $players,$category );
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function displayLeaderEntries( &$players,&$category ) {
		// Display the page title.
		$this->xml->startElement( 'h2' );
		$this->xml->text( $category );
		$this->xml->endElement();  // h2
		$this->xml->startElement( 'hr' );
		$this->xml->writeAttribute( 'class', 'thin' );
		$this->xml->endElement();  // hr
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 1 );
		$this->xml->writeAttribute( 'border', 5 );
		$this->xml->writeAttribute( 'width', 100 );
		
		foreach( $players as $player ) {
			// Display the basic team information.
			$this->xml->startElement( 'tr' );
			$this->player->displayLeader( $player,$category );
			$this->xml->endElement();  // end 'tr'
		}
		$this->xml->endElement();  // end the table
		
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	//-------------------------------------------------------------------------
	public function displayTeamEntries( &$teams ) {
		// Display the page title.
		$this->xml->startElement( 'h2' );
		$this->xml->text( 'Search Results' );
		$this->xml->endElement();  // h2
		$this->xml->startElement( 'hr' );
		$this->xml->writeAttribute( 'class', 'thin' );
		$this->xml->endElement();  // hr
		foreach( $teams as $team ) {
			// Display the basic team information.
			$this->team->displayTeamInfo( $team );
			$this->xml->startElement( 'hr' );
			$this->xml->writeAttribute( 'class', 'thin' );
			$this->xml->endElement();  // hr
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function updatePlayerProfileForm(){
		try {
			$this->player->formUpdateProfile();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function updatePlayerProfile(&$player_id){
		try {
			$this->player->updateProfile($_POST,$player_id);
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function playerJoinForm(){
		try {
			$this->player->formPlayerJoin();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function joinPlayer(){
		try {
			$this->player->playerJoin(&$_POST);
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function createTeam(){
		try {
			$this->team->createTeam(&$_POST);
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function createTeamForm(){
		try {
			$this->team->formCreateTeam();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function insertTeamPicForm(){
		try {
			$this->team->formAddPic();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function insertTeamPic(&$team_id){
		try {
			print($team_id);
			$this->team->insertTeamPic($_POST,$_FILES,$team_id);
			
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function displayPlayersStats(&$data){
		try {
			$pstats = $this->player->selectByPlayerName($data);
			if($pstats==null){
				$players=$this->player->findByPlayerName($data);
				foreach ($players as $player){
					$this->player->displayPlayerInfo($player);				
				}				
				print( $this->xml->outputMemory() . PHP_EOL );
			}else{
				$this->displayPlayerStatsEntries( $pstats );
			}
		} catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function displayPlayerStatsEntries( &$players ) {
		// Display the page title.
		$this->xml->startElement( 'h2' );
		$this->xml->text( 'Search Results' );
		$this->xml->endElement();  // h2
		$i=0;
		$last=-1;
		foreach( $players as $player ) {
			// Display the basic team information.
			if($player['player_id']!=$last){
				$this->xml->endElement();  // end the table
				
				
				$this->xml->startElement( 'hr' );
				$this->xml->writeAttribute( 'class', 'thin' );
				$this->xml->endElement();  // hr
				$result=$this->player->selectPlayer($player);
				$this->player->displayPlayerInfo($result[0]);
				
				$this->xml->startElement( 'table' );
				$this->xml->writeAttribute( 'cellpadding', 0 );
				$this->xml->writeAttribute( 'cellspacing', 2 );
				$this->xml->writeAttribute( 'border', 1);
				
				$this->xml->startElement( 'tr' );				
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'Year ' );
				$this->xml->endElement();  // end the strong
				$this->xml->endElement();  // end the table cell (td)
		
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'G ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
				
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'A ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
				
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'GP ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
				
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'P' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
				
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'Points Per Game ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
				$this->xml->endElement();  // end the table cell (tr)
				
				
				
			}
			$i=$i+1;
			$last=$player['player_id'];
			$this->player->displayPlayerStatInfo( $player );

		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function displayPlayerStatsEntriesNoLink( &$players ) {
		// Display the page title.
		$this->xml->startElement( 'h2' );
		$this->xml->text( 'Search Results' );
		$this->xml->endElement();  // h2
		$i=0;
		$last=-1;
		foreach( $players as $player ) {
			// Display the basic team information.
			if($player['player_id']!=$last){
				$this->xml->endElement();  // end the table
	
	
				$this->xml->startElement( 'hr' );
				$this->xml->writeAttribute( 'class', 'thin' );
				$this->xml->endElement();  // hr
				$result=$this->player->selectPlayer($player);
				$this->player->displayPlayerInfoNoLink($result[0]);
	
				$this->xml->startElement( 'table' );
				$this->xml->writeAttribute( 'cellpadding', 0 );
				$this->xml->writeAttribute( 'cellspacing', 2 );
				$this->xml->writeAttribute( 'border', 1);
	
				$this->xml->startElement( 'tr' );
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'Year ' );
				$this->xml->endElement();  // end the strong
				$this->xml->endElement();  // end the table cell (td)
	
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'G ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
	
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'A ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
	
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'GP ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
	
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'P' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
	
				$this->xml->startElement( 'td' );
				$this->xml->startElement( 'strong' );
				$this->xml->text( 'Points Per Game ' );
				$this->xml->endElement();  // end the em
				$this->xml->endElement();  // end the table cell (td)
				$this->xml->endElement();  // end the table cell (tr)
	
	
	
			}
			$i=$i+1;
			$last=$player['player_id'];
			$this->player->displayPlayerStatInfo( $player );
	
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function displayPlayers() {
		try {
			if( isset( $_POST['byTeam'] ) ) {
				// Display the result of a name search.
				$player = $this->player->selectByPlayerName( $_POST );
			} else {
				// Display all members.
				$player = $this->player->selectAll();
			}
			$this->displayPlayerEntries( $player );
		} catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	//-------------------------------------------------------------------------
	public function displayPlayerEntries( &$players ) {
		// Display the page title.
		$this->xml->startElement( 'h2' );
		$this->xml->text( 'Search Results' );
		$this->xml->endElement();  // h2
		$this->xml->startElement( 'hr' );
		$this->xml->writeAttribute( 'class', 'thin' );
		$this->xml->endElement();  // hr
		foreach( $players as $player ) {
			// Display the basic team information.
			$this->player->displayPlayerInfo( $player );
			$this->xml->startElement( 'hr' );
			$this->xml->writeAttribute( 'class', 'thin' );
			$this->xml->endElement();  // hr
		}
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function insertPlayerPicForm(){
		try {
			$this->player->formAddPic();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function insertPlayerPic(&$player_id){
		try {
			$this->player->insertPlayerPic($_POST,$_FILES,&$player_id);
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function findPlayerForm(){
		try {
			$this->player->formSearch();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function updateTeamForm(){
		try {
			$this->team->updateTeamProfile();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function updateTeam(&$team_id){
		try {
			$this->team->updateTeam($_POST,&$team_id);
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function addPlayerForm(){
		try {
			$this->team->formAddPlayer();
		}
		catch( Exception $e ) {
			
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function playerYearStatsForm(){
		try {
			$this->player->formYearStats();
		}
		catch( Exception $e ) {
				
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function cutPlayerForm(&$team_id){
		try {
			$this->team->formCutPlayer(&$team_id);
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function cutPlayer(&$team_id){
		try {
			$this->team->cutPlayer($team_id,$_POST['player_id']);
		}
		catch( Exception $e ) {
	
		}
	}
	public function insertPlayer(&$team_id){
		try {
			$this->team->insertPlayer($team_id,$_POST['player_id']);
		}
		catch( Exception $e ) {

		}
	}
	public function findCurrentRosterForm(){
		try {
			$this->team->formCurrentRoster();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function teamDisplayCurrentRoster(){
		$team_id=$_POST['team_id'];
		$players=$this->team->selectCurrentRoster($team_id);
				
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Players' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table cell (tr)
		foreach($players as $player){
			$this->team->displayPlayer($player);	
		}
		$this->xml->endElement();  // end the table
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	public function teamDisplayCurrentRosterII($team_id){
		$players=$this->team->selectCurrentRoster($team_id);
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Players' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table cell (tr)
		foreach($players as $player){
			$this->team->displayPlayer($player);
		}
		$this->xml->endElement();  // end the table
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function findRosterForm(){
		try {
			$this->team->formRoster();
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	public function teamDisplayRoster(){
		$team_id=$_POST['team_id'];
		$year=$_POST['year'];
		$players=$this->team->selectRoster($team_id,$year);
		$this->team->displayTeamName($team_id);
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Year' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $year );
		$this->xml->endElement();  // end the table cell (td)
		
		$this->xml->endElement();  // end the table cell (tr)
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Players' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table cell (tr)
		
		foreach($players as $player){
			$this->team->displayPlayer($player);
		}
		$this->xml->endElement();  // end the table
		print( $this->xml->outputMemory() . PHP_EOL );
	}
}
?>
