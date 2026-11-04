<?php
require('./includes/config.php'); 

$from = isset($_POST['from']) ? $_POST['from'] : '';
$subject = isset($_POST['subject']) ? $_POST['subject']: '';
$comment = isset($_POST['comment']) ? $_POST['comment']: '';

if (empty($from) || empty( $subject) || empty($comment)) {
	$error_message='Παρακαλώ συμπληρώστε όλα τα πεδία.';
	$show_submit = TRUE;
} else {
	addComment($from, $subject, $comment);
	$error_message='Καταχωρήστηκαν τα παρακάτω στοιχεία : ';
	$show_submit = FALSE;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo SITETITLE ; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" type="text/css" href="css/bluebliss.css" />
</head>
<body>
<div id="mainContentArea">
	<div id="contentBox">
        <div id="title"><?php echo SITETITLE ; ?></div>
        
        <div id="linkGroup">
            <div class="link"><a href="index.php">Αρχική</a></div>
             <div class="link"><a href="admin/">Διαχείριση</a></div>
            <div class="link"><a href="contactus.php">Επικοινωνία</a></div>
        </div>
        
        <div id="blueBox"> 
          <div id="header"></div>
          <div class="contentTitle">Παρακαλώ αφήστε το σχόλιο σας.</div>
            <div class="pageContent">
			
			 <form action="" method="post">
				<p><?php echo $error_message; ?></p>
				<p>Όνομα:&nbsp;<input name="from" type="text" value="<?php echo $from; ?>" size="50" /></p>
				<p>Θέμα:&nbsp;&nbsp;&nbsp;<input name="subject" type="text" value="<?php echo $subject; ?>" size="50" /></p>
				<p><hr></p>
				<p>Σχόλιο:&nbsp;<input name="comment" type="textarea" value="<?php echo $comment; ?>" /></p>
				<?php  
				if ($show_submit) { ?>
					<p><input type="submit" name="submit" value="Καταχώρηση" class="button" /></p>
				<?php } else { ?>
					<p>Ευχαριστούμε πολύ για τα σχόλιά σας.</p>
				<?php } ?>
			</form>
            </div>
            <div id="footer">  </div>
        </div>
	</div>
</div>
</body>
</html>
