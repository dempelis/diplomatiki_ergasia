<?php 

require('../includes/config.php'); 

if(isset($_POST['submit'])){


	$name = $_POST['name'];
	$username = $_POST['username'];
	$type = $_POST['type'];
	$password = $_POST['password'];
		
	
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