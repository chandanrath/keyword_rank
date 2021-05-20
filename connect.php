<?php

 //localhost
/*
$servername = "localhost";
$username   = "yfjxmqdnbb";
$password   = "pXMhQ8r8kv";
$db         = "yfjxmqdnbb";
*/


$servername = "localhost";
$username   = "rathtest_rank";
$password   = "rjT9S6n^BbhT";
$db         = "rathtest_rank";

$conn = mysqli_connect($servername, $username, $password, $db);


/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}else{

  //echo"DB conncet";
}


?>