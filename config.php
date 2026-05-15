<?php

$host = "localhost";  /* sql206.infinityfree.com */
$user = "root";  /* if0_41922519 */
$password = "";  /* chashibondhu123 */
$database = "chashibondhu";  /* if0_41922519_chashibondhu */

$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error){
    die("Connection failed: ". $conn->connect_error);
}

?>