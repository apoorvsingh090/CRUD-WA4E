
<?php
require_once "pdo.php";
session_start();
?>
<html>
<head><title>Apoorv Singh's Resume Registry </title>

</head>
<body>
<?php
if(isset($_SESSION['name'])){
echo("<h1>Apoorv Singh's Resume Registry</h1><p>");
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}
echo('<table border="1">'."\n");
$stmt = $pdo->query("SELECT * FROM Profile");
    echo "<th>Name</th>";
    echo "<th>Headline</th>";
    echo "<th>Action</th>";
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

    echo "<tr><td>";
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.htmlentities($row["first_name"]).'</a>');
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td><td>");
    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo("</td></tr>\n");
}

echo("<a href='add.php'>Add New Entry</a><br>");
echo("<a href='logout.php'>Logout</a>");
}
else{
echo('<div class="container">');
echo("<h1>Apoorv Singh's Resume Registry</h1><p>");
echo('<a href="login.php">Please log in</a>');
echo('<p>');
echo('</div>');
}

echo("</body>");
?>
