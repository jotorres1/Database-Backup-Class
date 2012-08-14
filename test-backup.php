<?php 
include('db_backup.class.php');

$b = new db_backup( 'test', 'test2', 'root', '' );

$b->initiate_backup();
?>