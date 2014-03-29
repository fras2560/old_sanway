<?php

require_once( "Sanway.php" );
require_once( "playerClass.php" );
$d = new sanPublic();
?>

<h1>Added Pic </h1>
<hr class="thick" />
<td><a href="addPicForm.php">Back</a></td>
<?php
$d->addPic();
?>