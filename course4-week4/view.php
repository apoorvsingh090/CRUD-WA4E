
<!DOCTYPE html>
<html>
<head>require_once "pdo.php";

<title>Apoorv Singh</title>
<?php 
session_start();

$message="success";
require_once "pdo.php";

$profile_id=$_REQUEST['profile_id'];
$stmt = $pdo->query("SELECT * FROM Profile WHERE profile_id=$profile_id");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt2 = $pdo->query("SELECT * FROM Position WHERE profile_id=$profile_id");
$rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
</head>
<body>
<?php if($message!="success") echo $message; ?>
<div class="container">
<h1>Profile summary</h1>

<ul>
<p>
<?php
foreach ( $rows as $row ) {
    echo "First Name: ".htmlentities($row['first_name']);
    echo "<br>Last Name: ".htmlentities($row['last_name']);
    echo "<br>Email: ".htmlentities($row['email']);
    echo "<br>Headline:<br>".htmlentities($row['headline']);
    echo "<br>"."Summary:"."<br>".htmlentities($row['summary']);
    
}echo "<br>Position:";
foreach ( $rows2 as $row ) {
    echo "<li>".htmlentities($row['year']);
    echo ":".htmlentities($row['description']);
    
}
?><p>
<a href="index.php">Done</a> 

</div>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script></body>
</html>



