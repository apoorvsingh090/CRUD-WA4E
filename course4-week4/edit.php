<?php
require_once('pdo.php');
session_start();

$isCancelling = isset($_POST['cancel']);
if ( $isCancelling ) {
	header("Location: index.php");
    return;
};


$isInputReady = isset($_POST['first_name']) &&
				isset($_POST['last_name']) &&
				isset($_POST['email']) &&
				isset($_POST['headline'])&&
				isset($_POST['summary']);


if ($isInputReady) { //check if numeric
	$isInputLong = strlen($_POST['first_name']) > 0 &&
					strlen($_POST['last_name']) > 0 &&
					strlen($_POST['email']) > 0 &&
					strlen($_POST['headline'])>0 &&
					strlen($_POST['summary']) > 0;


	if (!$isInputLong ) {
		$_SESSION['error'] = "All fields are required";
		header('Location: edit.php?profile_id='.$_GET['profile_id']);
		return;
	}if(!strpos($_POST['email'],'@')){
		$_SESSION['error'] = "Email must have an at-sign (@)";
		header('Location: edit.php?profile_id='.$_GET['profile_id']);
	   return;
	}
	
}

if ( $isInputReady && ! isset($_SESSION['error']) ) {
	$in_stmt = $pdo->prepare('UPDATE Profile SET first_name = :first_name, last_name = :last_name, email = :email, headline = :headline,summary=:summary WHERE profile_id = :profile_id');
	$in_stmt->execute(array(
		':first_name'=>$_POST['first_name'],
		':last_name'=>$_POST['last_name'],
		':email'=>$_POST['email'],
		':headline'=>$_POST['headline'],
		':profile_id'=>$_POST['profile_id'],
		':summary'=>$_POST['summary']
	));
	$sql = "DELETE FROM Position WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
	$stmt->execute(array(':zip' => $_REQUEST['profile_id']));
	$stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
	for($i=1;$i<=9;$i++)
    {
      if(!empty($_POST["year"."$i"]) && !empty($_POST["desc"."$i"]))
      {$stmt = $pdo->prepare('INSERT INTO Position
        (profile_id,rank,year,description)
        VALUES (:pro,:i, :yr, :desc)');
      
      $stmt->execute(array(
        ':pro' => $_POST['profile_id'],
        ':i' => $i,
        ':yr' => $_POST["year"."$i"],
        ':desc' => $_POST["desc"."$i"],
        ));
      }
	}
	$rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['eduyear' . $i])) continue;
        if (!isset($_POST['school' . $i])) continue;
        $year = $_POST['eduyear' . $i];
        $school = $_POST['school' . $i];

        print_r($year.$school) ;

        $institution_id = false;

        $stmt = $pdo->prepare('SELECT institution_id FROM
    Institution WHERE name = :name;');
        $stmt->execute(array(':name' => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row != false) $institution_id = $row['institution_id'];

        if ($institution_id === false) {
            $stmt = $pdo->prepare('INSERT INTO Institution
    (name) VALUES (:name)');
            $stmt->execute(array(':name' => $school));
            $institution_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO Education
    (profile_id, rank, year, institution_id)
    VALUES ( :pid, :rank, :year, :iid)');
        $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':iid' => $institution_id)
        );

        $rank++;
    }
	$_SESSION['success'] = "Record edited";
	header("Location: index.php");
	return;
};

if ( ! isset($_GET['profile_id']) && ! $isInputReady  ){
	$_SESSION['error'] = "Missing profile_id";
	header('Location: index.php');
	return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

$first_name = htmlentities($row['first_name']);
$last_name = htmlentities($row['last_name']);
$email = htmlentities($row['email']);
$headline = htmlentities($row['headline']);
$profile_id = $row['profile_id'];
$summary=htmlentities($row['summary']);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Apoorv Singh</title>
		<?php require_once('bootstrap.php'); ?>
</head>
<body>
	<div class="container">
		<?php // Flash pattern
		if ( isset($_SESSION['error']) ) {
		    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
		    unset($_SESSION['error']);
		} ?>
		<h1>Edit profile</h1>
		<form method="post">
            <p>First Name:
                <input type="text" name="first_name" size="60" value="<?php echo $first_name ?>"/></p>
            <p>Last Name:
                <input type="text" name="last_name" size="60" value="<?php echo $last_name?>"/></p>
            <p>Email:
                <input type="text" name="email" size="30" value="<?php echo $email ?>"/></p>
            <p>Headline:<br/>
                <input type="text" name="headline" size="80" value="<?php echo $headline ?>"/></p>
            <p>Summary:<br/>
                <textarea name="summary" rows="8" cols="80"><?php echo $summary ?></textarea>
                <?php

                $countEdu = 0;

                echo('<p>Education: <input type="submit" id="addEdu" value="+">' . "\n");
                echo('<div id="edu_fields">');
                
                            '<p>Year: <input type="text" name="eduyear' . $countEdu . '" value="">
<input type="button" value="-" onclick="$(\'#edu' . $countEdu . '\').remove();return false;\"></p>
<p>School: <input type="text" size="80" name="school' . $countEdu . '" class="school" 
value="" />';
                        echo "\n</div>\n";

                $countPos = 0;

                echo('<p>Position: <input type="submit" id="addPos" value="+">' . "\n");
                echo('<div id="position_fields">');
                
                            '<br>Year: <input type="text" name="year' . $countPos . '" value="">
<input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"><br>';

                ?>
    </div>
    <input type="submit" value="Save">
    <input type="submit" name="cancel" value="Cancel">
</form>
<script>
    countPos = 0;
    countEdu = 0;

    // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
    $(document).ready(function () {
        window.console && console.log('Document ready called');

        $('#addPos').click(function (event) {
            // http://api.jquery.com/event.preventdefault/
            event.preventDefault();
            if (countPos >= 9) {
                alert("Maximum of nine position entries exceeded");
                return;
            }
            countPos++;
            window.console && console.log("Adding position " + countPos);
            $('#position_fields').append(
                '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#position' + countPos + '\').remove();return false;"><br>\
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
        });

        $('#addEdu').click(function (event) {
            event.preventDefault();
            if (countEdu >= 9) {
                alert("Maximum of nine education entries exceeded");
                return;
            }
            countEdu++;
            window.console && console.log("Adding education " + countEdu);

            $('#edu_fields').append(
                '<div id="edu' + countEdu + '"> \
            <p>Year: <input type="text" name="eduyear' + countEdu + '" value="" /> \
            <input type="button" value="-" onclick="$(\'#edu' + countEdu + '\').remove();return false;"><br>\
            <p>School: <input type="text" size="80" name="school' + countEdu + '" class="school" value="" />\
            </p></div>'
            );

            $('.school').autocomplete({
                source: "school.php"
            });

        });

    });

</script>
</body>
</html>
