<?php
require('../includes/config.php'); 
if(logged_in()) {
	$host = $_SERVER['HTTP_HOST'];
	if (isset($_ENV['OPENSHIFT_APP_NAME'] )) {
		header('Location: ' . $host.'/project/site/'.DIRADMIN);
	} else {
		header('Location: '. $host.DIRADMIN);
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo SITETITLE;?></title>
<link href="<?php echo DIR;?>css/login.css" rel="stylesheet" type="text/css" />

</head>
<body>
<div class="lwidth">
	<div class="page-wrap">
		<div class="content">
		
		<?php 
		if( isset($_POST['submit']) ) {
			login($_POST['username'], $_POST['password']);
		}
		?>

		<div id="login">
			<p><?php echo messages();?></p>        
			<form method="post" action="">
				<p><label><strong>Username</strong><input type="text" name="username" /></label></p>
				<p><label><strong>Password</strong><input type="password" name="password" /></label></p>
				<p><br /><input type="submit" name="submit" class="button" value="login" /></p>
			</form>	  	  
		</div>
		<p style="padding-left: 180px;"><a  href="<?php echo DIR ;?>">Αρχική Σελίδα</a></p>
		</div>	
		<div class="clear"></div>		
	</div>
<div class="footer">&copy; <?php echo SITETITLE.' '. date('Y');?> </div>	
</div>
</body>
</html>