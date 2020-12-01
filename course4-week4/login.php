<script>function doValidate() {

console.log('Validating...');

try {

pw = document.getElementById('id_1723').value;
em=document.getElementById('email').value;
console.log("Validating pw="+pw);

if (pw == null || pw == "" ||em=="" || em==null) {

alert("Both fields must be filled out");

return false;

}
else if(!em.match('@')){
    alert("Invalid email address");
    return false;
}
return true;

} catch(e) {

return false;

}

return false;

}</script>
<?php
session_start();
require_once "pdo.php";
if(isset($_POST['email']) && isset($_POST['pass'])){
    if(empty($_POST['email']) ||empty($_POST['pass']))
{
    $_SESSION['error'] ="User name and password are required";
    header("Location: login.php");
    return;
}
$check = hash('md5','XyZzy12*_'.$_POST['pass']);

$stmt = $pdo->prepare('SELECT user_id, name FROM users

WHERE email = :em AND password = :pw');

$stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));

$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row !== false ) {

    $_SESSION['name'] = $row['name'];
    
    $_SESSION['user_id'] = $row['user_id'];
    
    // Redirect the browser to index.php
    
    header("Location: index.php");
    
    return;
}
else{
    $_SESSION['error'] = "Incorrect password";
    header("Location: login.php");
    return;
}

}
?>
<!DOCTYPE html>
<html>
<head>
<title>Apoorv Singh</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php // line added to turn on color syntax highlight
if ( isset($_SESSION['error']) ) {
  echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
  unset($_SESSION['error']);
}?>
<form method="POST" action="login.php">
<label for="email">Email</label>
<input type="text" name="email" id="email"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<a href="index.php">Cancel</a>
</form>


<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is php123 -->
</p>
</div>
</body>
