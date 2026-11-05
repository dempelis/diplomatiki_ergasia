<?php 
login_required();

logout_if_requested();

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
	
	/*
	 * Διαγραφές και αποσύνδεση: γίνονται με POST και CSRF token (όχι με GET link).
	 * Ένας σύνδεσμος <a class="js-post-delete" data-url=".." data-field=".." data-value=".." data-confirm="..">
	 * ζητά επιβεβαίωση και στέλνει τη φόρμα.
	 */
	var CSRF_TOKEN = <?php echo json_encode(csrf_token()); ?>;

	$(function() {
		$(document).on('click', '.js-post, .js-post-delete', function(e) {
			e.preventDefault();
			var link = $(this);
			if (link.attr('data-confirm') && !confirm(link.attr('data-confirm'))) {
				return;
			}
			var form = $('<form method="post"></form>').attr('action', link.attr('data-url'));
			form.append($('<input type="hidden" />').attr('name', link.attr('data-field')).val(link.attr('data-value')));
			form.append($('<input type="hidden" name="csrf_token" />').val(CSRF_TOKEN));
			form.appendTo('body').submit();
		});
	});
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
			<li><a href="#" class="js-post" data-url="<?php echo h(DIRADMIN);?>" data-field="logout" data-value="1">Αποσύνδεση</a></li>
		</ul>
	</div><!-- navigation div -->