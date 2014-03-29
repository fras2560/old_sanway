<?php 
require_once( "utilities.php" );

class championships{
	private $conn = null; // Connection information and utilities.
	private $xml = null;
	
	public function __construct( &$conn, &$xml ) {
		$this->conn = $conn;
		$this->xml = $xml;
	}
	
	public function selectYears(){
		try {
			$sql = "Select DISTINCT year From v_championship ORDER BY year";
			$stmt = $this->conn->prepare( $sql );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function selectChampionship(&$data){
		try {
			$sql = "Select * From v_championship WHERE Year=?";
			$i=1;
			$stmt = $this->conn->prepare( $sql );
			$stmt->bindValue( $i++, $data['year'] );
			$stmt->execute();
			$stmt->setFetchMode( PDO::FETCH_ASSOC );
			return $stmt->fetchAll();
		}catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
	}
	
	public function displayOptionYear( &$years, $selected = array() ) {
		foreach( $years as $year ) {
			displayOption( $this->xml, $year['Year'], $year['Year'], $selected );
		}
		return;
	}
	
	public function championshipSelectForm(){
		$this->xml->startElement( 'form' );
		$this->xml->writeAttribute( 'name', 'championshipDisplay' );
		$this->xml->writeAttribute( 'method', 'post' );
		$this->xml->writeAttribute( 'action', 'championshipDisplay.php' );
		
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
		
		$this->xml->endElement(); // p
		$this->xml->endElement();  // form
		print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function displayChampionship(&$year){
		$datas=$this->selectChampionship($year);
		$data=$datas[0];
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 0 );
		$this->xml->writeAttribute( 'border', 0 );
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Year: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $data['Year'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		$height = $data['height'];
		$width = $data['width'];
		$x = 300/$height;
		$width *= $x;
		$height *= $x;
		$id=$data['Year'];
		$this->xml->startElement('image');
		$this->xml->writeAttribute('src',"cimage.php?id=$id");
		$this->xml->writeAttribute('width',$width);
		$this->xml->writeAttribute('height',$height);
		$this->xml->endElement();
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'Champion Team: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $data['Team'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->startElement( 'strong' );
		$this->xml->text( 'MVP: ' );
		$this->xml->endElement();  // end the em
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->startElement( 'td' );
		$this->xml->text( $data['MVP'] );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		
		$this->xml->endElement();  // end the table
		$this->xml->text( $data['Description'] );	
		print( $this->xml->outputMemory() . PHP_EOL );
	}
}
?>