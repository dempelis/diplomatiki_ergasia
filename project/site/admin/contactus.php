<?php 
require('../includes/config.php'); 

login_required();
advanced_required();

if(isset($_GET['logout'])){
	logout();
}
else  if(isset($_POST['delcomment']) && is_valid_post()){

	deleteComment(post_string('delcomment'));
	$_SESSION['success'] = "Το σχόλιο διαγράφηκε"; 
		
    header('Location: ' .DIRADMIN. 'contactus.php');
   	exit();
}

include('header.php');

?>


<div id="content">

<?php 
	
	messages();
?>
<br/>

<table>
		<tr>
			<th colspan='4' ><strong>Σχόλια Χρηστών</strong></th>
		</tr>
		<tr>
			<th><strong>Από</strong></th>
			<th><strong>Θέμα</strong></th>
			<th><strong>Σχόλιο</strong></th>
			<th><strong>Ενέργεια</strong></th>
		</tr>

		<?php
		$comments = getComments();
		foreach ( $comments as $cmnt){
			echo "<tr>";
			/* τα σχόλια έρχονται από τη δημόσια φόρμα επικοινωνίας: πάντα escape */
			echo "<td>" . h($cmnt->from) . "</td><td>" . h($cmnt->subject) . "</td>";
			echo "<td>" . h($cmnt->comment) . "</td>";
			echo "<td> <a href=\"#\" class=\"js-post-delete\" data-url=\"contactus.php\" data-field=\"delcomment\""
				. " data-value=\"" . h($cmnt->id) . "\""
				. " data-confirm=\"" . h("Θέλετε σίγουρα να διαγράψετε αυτό το σχόλιο ;") . "\">Διαγραφή</a></td>";
			echo "</tr>";
		}
		?>
</table>
</div>
<br/><br/><br/><br/>
<?php

include('footer.php');

?>