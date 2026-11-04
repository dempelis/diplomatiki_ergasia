<?php 

require('../includes/config.php'); 

login_required();
advanced_required();



if(isset($_GET['logout'])){
	logout();
}
else  if(isset($_GET['deluser'])){
		
	$deluser = $_GET['deluser'];
	$deluser = mysql_real_escape_string($deluser);
	if($deluser!="admin"){
		$sql = mysql_query("DELETE FROM users WHERE username = '$deluser' ");
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
			echo "<td>$user->username</td><td>$user->name</td>";
			echo "<td>$user->type</td>";
			echo "<td><a href=\"".DIRADMIN."edituser.php?id=$user->username\">Τροποποίηση</a> </td>";
			echo "<td> <a href=\"javascript:deluser('$user->username','$user->name');\">Διαγραφή</a></td>";	
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