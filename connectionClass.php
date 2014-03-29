<?php
// Comment out of production system.
//error_reporting( E_ALL );
//ini_set( 'display_errors', TRUE );

/*
 Creates a connection to a PDO database. This connection is then exposed
 (read only) through the 'getConnection' method.

 The initialization (.ini) file must be of the form:

 [database]
 dsn = "mysql:host=localhost;dbname=database name"
 usr = "user"
 pwd = "password"

 The .ini file should be hidden by the web server
 (done by system administrator):

 <FilesMatch "\.ini">
 Order deny,allow
 Deny from All
 Satisfy all
 </FilesMatch>

 This file is accessed by:

 require_once( "somelocation/connectionClass.php" );

 Usage:

 $dcris = new Connection( 'dcris.ini' );
 $sql = 'SELECT * FROM table';
 $conn = $dcris->getConnection();
 $stmt = $conn->prepare( $sql );
 ...
 */
//-------------------------------------------------------------------------
class Connection {
  private $connection = null;

  //-------------------------------------------------------------------------
  public function __construct( $settings_file ) {
    // Read database settings from external settings file.
    if( $settings = parse_ini_file( $settings_file, TRUE ) ) {
      // parse_ini_file returns FALSE if file cannot be opened.
      $dsn = $settings['database']['dsn'];
      $usr = $settings['database']['usr'];
      $pwd = $settings['database']['pwd'];
      // Define the options array for persistent connections
      // and errors as exceptions.
      $options = array( PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION );
      // Connect to the database.
      $this->connection = new PDO( $dsn, $usr, $pwd, $options );
    } else {
      // Settings file name is not exposed.
      throw new exception( 'Error: Unable to open settings file.' );
    }
  }

  //-------------------------------------------------------------------------
  public function getConnection() {
    return( $this->connection );
  }

  //-------------------------------------------------------------------------
}
?>