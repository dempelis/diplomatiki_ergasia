<?php 
require('../includes/config.php'); 

login_required();
advanced_required();

if(isset($_GET['logout'])){
	logout();
}
else  if(isset($_GET['delcomment'])){
		
	$delcomment = $_GET['delcomment'];
	$delcomment = mysql_real_escape_string($delcomment);
	$sql = mysql_query("DELETE FROM contactus WHERE id = '$delcomment' ");
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
			echo "<td>$cmnt->from</td><td>$cmnt->subject</td>";
			echo "<td>$cmnt->comment</td>";
			echo "<td> <a href=\"javascript:delcomment('$cmnt->id');\">Διαγραφή</a></td>";	
			echo "</tr>";
		}
		?>
</table>
</div>
<br/><br/><br/><br/>
<?php

include('footer.php');

?>