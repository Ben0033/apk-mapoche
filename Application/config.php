<?php
 $host ='localhost';
 $dbname = 'apk-Mapoche';
 $username ='root';
 $password ='';

 try {
    $con = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 }catch (PDOException $e){
    die("Erreur de connexion : " . $e->getMessage());
 }
 ?>
