<?php
$host='localhost';
$username='root';
$password='karima';
$dbname='gestionlivres';
$connection=new mysqli($host,$username,$password,$dbname);

if($connection->connect_error){
    die ("connexion failed".$connection->connect_error) ;
}
else{
    echo"";
}
?>