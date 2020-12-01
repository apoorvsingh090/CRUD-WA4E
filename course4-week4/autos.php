
<!DOCTYPE html>
<html>
<head>
<title>Apoorv Singh</title>
<?php
$message="success";
require_once "pdo.php";
if(!isset($_GET['name'])){die("Name parameter missing");
}
if(isset($_REQUEST['logout']))header('Location: index.php');
if ( isset($_POST['make']) && !empty($_POST['make']) && isset($_POST['year'])&& isset($_POST['mileage'])) 
{   if(!(is_numeric($_POST['year']) && is_numeric($_POST['mileage']) ))
   $message="Mileage and year must be numeric";
else{
    $sql = "INSERT INTO autos (make, year, mileage) 
              VALUES (:make, :year, :mileage)";
    echo("<pre>\n".$sql."\n</pre>\n");
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':make' => $_POST['make'],
        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage']));
}}
else $message="Make is required";

$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
</head>
<body>
<?php if($message!="success") echo $message; ?>
<div class="container">
<h1>Tracking Autos for <?= $_GET['name'] ?></h1>
<form method="post">
<p>Make:
<input type="text" name="make" size="60"/></p>
<p>Year:
<input type="text" name="year"/></p>
<p>Mileage:
<input type="text" name="mileage"/></p>
<input type="submit" value="Add">
<input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>
<ul>
<p>
<?php
foreach ( $rows as $row ) {
    echo "<li><tr><td>";
    echo(htmlentities($row['make']));
    echo(" </td><td>");
    echo($row['year']);
    echo(" </td><td>");
    echo($row['mileage']);
    echo("</td><td>\n");
}
?>

</ul>
</div>
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script></body>
</html>



