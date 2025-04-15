<?php
function OpenCon()
{

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbase = "phacto";

    $conn = new mysqli($servername, $username, $password, $dbase) or die("Connect failed: %s\n" . $conn->error);

    return $conn;
}

function CloseCon($conn)
{
    $conn->close();
}
$conn = OpenCon(); // Establish the database connection and assign it to $conn
?>