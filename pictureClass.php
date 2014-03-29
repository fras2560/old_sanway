<?php 
require_once( "utilities.php" );

class picture{
	private $conn = null; // Connection information and utilities.
	private $xml = null;

	//-------------------------------------------------------------------------
	public function __construct( &$conn, &$xml ) {
		$this->conn = $conn;
		$this->xml = $xml;
	}
	public function insertPic(&$data,&$file){
		try{
			$info = getimagesize( $file['image']['tmp_name'] );
			if( !$info ) {
				// getimagesize returns false - not a valid image file.
				print( '<p>Bad image file.</p>' );
			} else {
				// Get a file handle for the image file.
				$fp = fopen( $file['image']['tmp_name'], 'rb' );
				// Define the SQL INSERT statement.
				$sql='Insert INTO Photos (height,width,mime_type,picture) VALUES (?,?,?,?)';
				$stmt = $this->conn->prepare( $sql );
				$i = 1;
				// The member ID has already been assigned.
				$stmt->bindValue( $i++, $info[1] );
				$stmt->bindValue( $i++, $info[0] );
				$stmt->bindValue( $i++, $info['mime'] );
				$stmt->bindValue( $i++, $fp, PDO::PARAM_LOB );
				$stmt->execute();
			}
		}
		catch( Exception $e ) {
			msg( $this->xml, $e->getMessage() );
		}
		fclose($fp);
	}
	public function InsertPicForm(){
			$this->xml->startElement( 'form' );
			$this->xml->writeAttribute( 'action', 'addPic.php' );
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
		
	public function selectPic($pic_id){
		$sql = 'SELECT * FROM Photos WHERE photo_id = ?';
		$stmt = $this->conn->prepare( $sql );
		$i = 1;
		$stmt->bindValue( $i++, $pic_id );
		$stmt->setFetchMode( PDO::FETCH_ASSOC );
		$stmt->execute();
		return $stmt->fetchAll();
	}
		
	public function diplayPic($pic_id){
		$lists=$this->selectPic($pic_id);
		$info=$lists[0];
		$height=$info['height'];
		$width=$info['width'];
		$x = 400/$height;
		$width *= $x;
		$height *= $x;
		
		$this->xml->startElement('image');
		$this->xml->writeAttribute('src',"pic.php?id=$pic_id");
		$this->xml->writeAttribute('width',$width);
		$this->xml->writeAttribute('height',$height);
		$this->xml->endElement();
		print( $this->xml->outputMemory() . PHP_EOL );
		
	}
	
}
?>