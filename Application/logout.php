<?php
session_start();
session_destroy();
header('LOCATION: connexion.php');
exit;

?>