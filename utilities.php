<?php 

//------------------------------------------------------------------------- 
// Display Utility Functions 
//------------------------------------------------------------------------- 

function displayTextBoxRow( &$xml, $name, $label, $size, $value = null ) {
  $xml->startElement( 'tr' ); 

  $xml->startElement( 'td' ); 
  $xml->startElement( 'label' ); 
  $xml->writeAttribute( 'for', $name ); 
  $xml->text( "$label: " ); 
  $xml->endElement();  // label 
  $xml->endElement();  // td 

  $xml->startElement( 'td' ); 
  $xml->startElement( 'input' ); 
  $xml->writeAttribute( 'name', $name ); 
  $xml->writeAttribute( 'type', 'text' ); 
  $xml->writeAttribute( 'size', $size ); 
  $xml->writeAttribute( 'value', $value ); 
  $xml->endElement();  // input 
  $xml->endElement();  // td 

  $xml->endElement();  // tr 
  return; 
} 

//------------------------------------------------------------------------- 
function displayOption( &$xml, $value, $text, &$selected ) { 
  $xml->startElement( 'option' ); 
  $xml->writeAttribute( 'value', $value ); 

  if( in_array( $value, $selected ) ) { 
    $xml->writeAttribute( 'selected', 'selected' ); 
  } 
  $xml->text( $text ); 
  $xml->endElement(); // option 
  return; 
} 

//------------------------------------------------------------------------- 
function msg( &$xml, $string ) { 
  $xml->startElement( 'h3' ); 
  $xml->writeAttribute( 'style', 'color: red;' ); 
  $xml->text( $string ); 
  $xml->endElement(); // h3 
  return; 
} 

?> 