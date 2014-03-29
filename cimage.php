<?php
require_once( 'connectionClass.php' );
// Displays an image blob extracted from a database.
try {
	// Connect to the database.
	$g1 = new Connection( 'settings.ini' );
	$conn = $g1->getConnection();
	// Extract the mime type and image data.
	$sql = 'SELECT mime_type, pic FROM v_championship WHERE Year = ?';
	$stmt = $conn->prepare( $sql );
	$stmt->bindValue( 1, $_GET['id'] );
	$stmt->execute();
	$stmt->setFetchMode( PDO::FETCH_ASSOC );
	$result = $stmt->fetchAll();
	if( count( $result ) > 0 ) {
		// Make sure the image is found.
		$row = $result[0];
		// Extract the file extention from the mime type:
		// 'image/[extension]'
		$ext = explode( '/', $row['mime_type'] );
		$len = strlen( $row['pic'] );
		// Send the content headers:
		// The content type:
		header( "content-type: {$row['mime_type']}" );
		// The number of bytes in the image.
		header( "content-length: $len" );
		// Information for saving the image.
		header( "content-disposition: inline; filename=\"image.{$ext[1]}\"" );
		// Send the actual binary image data.
		print( $row['pic'] );
	} else {
		// The image is not found - return empty data.
		print( '' );
	}
} catch( Exception $e ) {
	print( $e->getMessage() );
}
?>