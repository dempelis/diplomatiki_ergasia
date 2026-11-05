<?php 

require('../includes/config.php'); 

login_required();
advanced_required();



if(isset($_GET['logout'])){
	logout();
}
else  if(isset($_POST['deluser']) && is_valid_post()){

	$deluser = post_string('deluser');
	if($deluser!="admin"){
		deleteUser($deluser);
		$_SESSION['success'] = "O χρήστης διαγράφηκε";
	}
	else{
		$_SESSION['error'] = "O χρήστης admin δεν είναι δυνατό να διαγραφεί."; 
	
	}
	
    header('Location: ' .DIRADMIN. 'users.php');
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
			<th colspan='5' ><strong>Χρήστες </strong></th>
		</tr>
		<tr>
			<th><strong>username</strong></th>
			<th><strong>Όνομα</strong></th>
			<th><strong>Τύπος</strong></th>
			
			<th colspan='2'><strong>Ενέργεια</strong></th>
		</tr>

		<?php
		$users = getUsers();
		foreach ( $users as $user){
			echo "<tr>";
			echo "<td>" . h($user->username) . "</td><td>" . h($user->name) . "</td>";
			echo "<td>" . h($user->type) . "</td>";
			echo "<td><a href=\"" . h(DIRADMIN . "edituser.php?id=" . urlencode($user->username)) . "\">Τροποποίηση</a> </td>";
			echo "<td> <a href=\"#\" class=\"js-post-delete\" data-url=\"users.php\" data-field=\"deluser\""
				. " data-value=\"" . h($user->username) . "\""
				. " data-confirm=\"" . h("Θέλετε σίγουρα να διαγράψετε τo χρήστη '" . $user->name . "';") . "\">Διαγραφή</a></td>";
			echo "</tr>";
		}
		?>
</table>
	<p><a href="<?php echo DIRADMIN;?>adduser.php" class="button">Προσθήκη Χρήστη</a></p>





</div>
<br/><br/><br/><br/>
<?php

include('footer.php');

?>