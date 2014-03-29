<?php 
require_once( "utilities.php" );

class news{
	private $conn = null; // Connection information and utilities.
	private $xml = null;
	public function __construct( &$conn, &$xml ) {
		$this->conn = $conn;
		$this->xml = $xml;
	}
	public function getNews(){
		$name=$data['player_name'];
		$sql = 'SELECT * FROM News ORDER BY news_id DESC LIMIT 10';
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, "$name%" );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$stmt->execute();
		return $stmt->fetchAll();
	}
	public function getAllNews(){
		$name=$data['player_name'];
		$sql = 'SELECT * FROM News ORDER BY news_id DESC';
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, "$name%" );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$stmt->execute();
		return $stmt->fetchAll();
	}
	public function displayAllNews(){
			$news=$this->getAllNews();
			$this->xml->startElement( 'table' );
			$this->xml->writeAttribute( 'cellpadding', 1 );
			$this->xml->writeAttribute( 'cellspacing', 1 );
			$this->xml->writeAttribute( 'border', 1 );
		
			$this->xml->startElement( 'tr' );
			$this->xml->startElement( 'td' );
			$this->xml->text( 'Recent News' );
			$this->xml->endElement();  // end the table cell (td)
			$this->xml->endElement();  // end the table row (tr)
		
			foreach($news as $new){
				$this->xml->startElement( 'tr' );
				$this->xml->startElement( 'td' );
				$this->xml->text( $new['news'] );
				$this->xml->endElement();  // end the table cell (td)
				$this->xml->endElement();  // end the table row (tr)
			}
			$this->xml->endElement();  // end the table
			print( $this->xml->outputMemory() . PHP_EOL );
	}
	
	public function displayNews(){
		$news=$this->getNews();
		$this->xml->startElement( 'table' );
		$this->xml->writeAttribute( 'cellpadding', 1 );
		$this->xml->writeAttribute( 'cellspacing', 1 );
		$this->xml->writeAttribute( 'border', 1 );
		
		$this->xml->startElement( 'tr' );
		$this->xml->startElement( 'td' );
		$this->xml->text( 'Recent News' );
		$this->xml->endElement();  // end the table cell (td)
		$this->xml->endElement();  // end the table row (tr)
		
		foreach($news as $new){
			$this->xml->startElement( 'tr' );
			$this->xml->startElement( 'td' );
			$this->xml->text( $new['news'] );
			$this->xml->endElement();  // end the table cell (td)
			$this->xml->endElement();  // end the table row (tr)		
		}
		$this->xml->endElement();  // end the table
		print( $this->xml->outputMemory() . PHP_EOL );
		
		
	}
	
}
?>