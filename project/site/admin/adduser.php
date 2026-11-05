<?php 

require('../includes/config.php');

/* ο έλεγχος πρέπει να γίνεται ΠΡΙΝ από την επεξεργασία της φόρμας (παλιά γινόταν μόνο στο header.php) */
login_required();
advanced_required();

if(isset($_POST['submit']) && is_valid_post()){


	$name = post_string('name');
	$username = post_string('username');
	$type = post_string('type');
	$password = post_string('password');
		
	
	if(addUser($username ,$name,$password,$type)   ){
		$_SESSION['success'] = 'O χρήστης προστέθηκε ';	
		header("Location: users.php");
	}
	else{
		$_SESSION['error'] = 'Πρόβλημα κατά την εισαγωγή του χρήστη, δοκιμάστε διαφορετικό username. ';	
		header("Location: users.php");
			
	}
	exit();

}


include("header.php");



?>


<div id="content">

<h1>Προσθήκη Χρήστη</h1>



<form action="" method="post">
<?php echo csrf_field(); ?>

<p>Όνομα:<br /> <input name="name" type="text" value="" size="103" /></p>
<p>username:<br /> <input name="username" type="text" value="" size="103" /></p>
<p>Τύπος:<br /> 
	<select name="type">
	<option value="advanced">Διαχειριστής</option>
	<option value="registered">Εγγεγραμένος</option>
	</select>
</p>
<p>Password:<br /> <input name="password" type="password" value="" size="103" /></p>


<p><input type="submit" name="submit" value="Submit" class="button" /></p>
</form>

</div>

<?php
include("footer.php");

?>