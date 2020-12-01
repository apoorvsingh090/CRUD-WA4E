
<!DOCTYPE html>
<html>
<head> <title>Apoorv Singh</title>
<?php
session_start();
require_once "pdo.php";
if ( ! isset($_SESSION['name']) ) {
  die("ACCESS DENIED");
}//print_r($_SESSION);
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']))
{   $_SESSION['first_name']=$_POST['first_name'];
    $_SESSION['last_name']=$_POST['last_name'];
    $_SESSION['email']=$_POST['email'];
    $_SESSION['headline']=$_POST['headline'];
    $_SESSION['summary']=$_POST['summary'];
    
    for($i=1;$i<=9;$i++)
    {
      if(isset($_POST["year"."$i"]) && isset($_POST["desc"."$i"]))
      {
      $_SESSION["year"."$i"]=$_POST["year"."$i"];
      $_SESSION["desc"."$i"]=$_POST["desc"."$i"];
      }
      else{
      $_SESSION["year"."$i"]="";
      $_SESSION["desc"."$i"]="";
      }
    }
    for($i=1;$i<=9;$i++)
    {
      if(isset($_POST["eduyear"."$i"]) && isset($_POST["school"."$i"]))
      {
      $_SESSION["eduyear"."$i"]=$_POST["eduyear"."$i"];
      $_SESSION["school"."$i"]=$_POST["school"."$i"];
      }
      else{
      $_SESSION["eduyear"."$i"]="";
      $_SESSION["school"."$i"]="";
      }
    }
    

    if(empty($_SESSION['first_name']) || empty($_SESSION['email'])|| empty($_SESSION['last_name'])|| empty($_SESSION['headline'])|| empty($_SESSION['summary'])){
    $_SESSION['error'] ="All fields are required";
    header("Location: add.php");
    return;
}
   else if(!strpos($_POST['email'],'@')){
    $_SESSION['error'] = "Email must have an at-sign (@)";
   header("Location: add.php");
   return;
}
else{
    
    $stmt = $pdo->prepare('INSERT INTO Profile
  (user_id, first_name, last_name, email, headline, summary)
  VALUES ( :uid, :fn, :ln, :em, :he, :su)');


$stmt->execute(array(
  ':uid' => $_SESSION['user_id'],
  ':fn' => $_POST['first_name'],
  ':ln' => $_POST['last_name'],
  ':em' => $_POST['email'],
  ':he' => $_POST['headline'],
  ':su' => $_POST['summary']));
  $profile_id=$pdo->lastInsertId();
  for($i=1;$i<=9;$i++)
    {
      if(!empty($_SESSION["year"."$i"]) && !empty($_SESSION["desc"."$i"]))
      {if(!is_numeric($_SESSION["year"."$i"])){
        $_SESSION['error'] = 'Position year must be numeric';
        header("Location: add.php");
        return;
      }
      $stmt = $pdo->prepare('INSERT INTO Position
        (profile_id,rank,year,description)
        VALUES (:pro,:i, :yr, :desc)');
      
      $stmt->execute(array(
        ':pro' => $profile_id,
        ':i' => $i,
        ':yr' => $_SESSION["year"."$i"],
        ':desc' => $_SESSION["desc"."$i"],
        ));
      }
    }
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['eduyear' . $i])) continue;
        if (!isset($_POST['school' . $i])) continue;

        $eduyear = $_POST['eduyear' . $i];
        $school = $_POST['school' . $i];

        $stmt = $pdo->prepare("SELECT * FROM Institution where name = :xyz");
        $stmt->execute(array(":xyz" => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $institution_id = $row['institution_id'];
        } else {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES ( :name)');

            $stmt->execute(array(
                ':name' => $school,
            ));
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO Education
(profile_id, institution_id, year, rank)
VALUES ( :pid, :institution, :eduyear, :rank)');


        $stmt->execute(array(
                ':pid' => $profile_id,
                ':institution' => $institution_id,
                ':eduyear' => $eduyear,
                ':rank' => $rank)
        );

        $rank++;
        $_SESSION['success'] = 'Record added.';
        header("Location: index.php");
    return;
    }
}
  }

?>
<body>
<?php // line added to turn on color syntax highlight

if ( isset($_SESSION['error']) ) {
  echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
  unset($_SESSION['error']);
}?>
<div class="container">
<h1>Adding profile for <?= $_SESSION['name'] ?></h1>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
Education: <input type="submit" id="addedu" value="+">
<div id="edu_fields">
</div>
</p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>

<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countPos = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('$_SESSION["year2"]');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
countPos2 = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('$_SESSION["year2"]');
    $('#addedu').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine edu entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding edu "+countPos);
        $('#edu_fields').append(
            '<div id="edu'+countPos+'"> \
            <p>Year: <input type="text" name="eduyear'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#edu'+countPos+'\').remove();return false;"></p> \
            School: <textarea name="school'+countPos+'" rows="1" cols="8"></textarea>\
            </div>');
    });
});
</script>
</body>