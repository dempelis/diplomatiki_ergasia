<?php 
login_required();

if(isset($_GET['logout'])){
	logout();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta charset="utf-8">
<title><?php echo SITETITLE;?></title>
<link href="<?php echo DIR;?>css/adminstyle.css" rel="stylesheet" type="text/css" />

<script src="<?php echo DIR;?>js/jquery-1.7.1.js"></script>
<!--
<script src="<?php echo DIR;?>js/jquery.ui.core.js"></script>
<script src="<?php echo DIR;?>js/jquery.ui.widget.js"></script>
-->
<script language="JavaScript" type="text/javascript">
	
	function deluser(username, name)
	{
	   if (confirm("Θέλετε σίγουρα να διαγράψετε τo χρήστη '" + name + "';"))
	   {
		  window.location.href = 'users.php?deluser=' + username;
	   }
	}
	
	function deladjective(code, adjective)
	{
	   if (confirm("Θέλετε σίγουρα να διαγράψετε τo επίθετο '" + adjective + "';"))
	   {
		  window.location.href = 'adjectives.php?deladjective=' + code;
	   }
	}
	
	function delcomment(id)
	{
		if (confirm("Θέλετε σίγουρα να διαγράψετε αυτό το σχόλιο ;")) {
			window.location.href = 'contactus.php?delcomment=' +id;
		}
	}
	
	
	
</script>
</head>
<body>

<div id="wrapper">
	<div id="navigation">
		<ul class="menu">
			<li><a href="<?php echo DIRADMIN;?>">Κεντρική Σελίδα</a></li>	
			<li><a href="<?php echo DIRADMIN;?>adjectives.php">Επίθετα</a></li>	
			<?php
				if (isAdvanced()) { 
					echo  "<li><a href='" .DIRADMIN . "users.php'>Χρήστες</a></li>";
					echo  "<li><a href='" .DIRADMIN . "contactus.php'>Σχόλια</a><li>";
					echo "<li><a href='". DIRADMIN. "imports.php'>Εισαγωγή</a><li>";
				}
			?>	
			<li><a href="<?php echo DIRADMIN;?>?logout">Αποσύνδεση</a></li>
		</ul>
	</div><!-- navigation div -->