<?php

define('DB_SERVER',"localhost");
define('DB_USERNAME',"root");
define('DB_PASSWORD',"");
define('DB_DATABASE',"dbsakura_mobile");

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if(!$conn){
    die("Connection Failed: ". mysqli_connect_error());
}

?>
