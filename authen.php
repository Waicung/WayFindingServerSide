<?php
//database information
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "WayFinding";
$conn=mysqli_connect($servername,$username,$password,$db_name);

//check the connection
if (mysqli_connect_error($conn))
{
   die("Connection failed: " . $conn->connect_error);
}

//receive credential from request
$username = $_POST["username"];
$password = $_POST["password"];
/*$username = 'tuser';
$password = 'tpass';*/
$sql = "SELECT * FROM users where username='$username' and password='$password'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($result);
if($row!=null){
$data = $row[1].",".$row[2];
echo $data;
}

mysqli_close($conn); 
?>