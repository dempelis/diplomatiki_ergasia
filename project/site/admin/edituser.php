<?php 
require('../includes/config.php'); 

login_required();
advanced_required();

if(isset($_POST['submit'])){

	$username = $_POST['username'];
	$password = $_POST['password'];
	$type = $_POST['type'];
	$name = $_POST['name'];
	
	
	updateUser($username,$password ,$name,$type );
	
	
	$_SESSION['success'] = 'Τα στοιχεία του χρήστη  τροποποιήθηκαν';
	header("Location: users.php");
	exit();

}
if( !isset($_GET['id'])){
	header("Location: users.php");
	exit();
}

include("header.php");
?>


<div id="content">

<h1>Τροποποίηση Στοιχείων Χρήστη</h1>

<?php
$username= $_GET['id'];
$user = getUser($username);

?>


<form action="" method="post">

<p>Όνομα χρήστη:<br /> <input name="username" type="text"  readonly="readonly"  value="<?php echo $user->username;?>" size="103" /></p>
<p>Όνομα:<br /> <input name="name" type="text"   value="<?php echo $user->name;?>" size="103" /></p>
<p>Νέος κωδικός:<br /> <input name="password" type="password"   value="" size="103" /></p>


Ο κωδικός πρόσβασης θα αλλάξει μονάχα αν συμπληρώσετε το αντίστοιχο πεδίο.

<p><input type="submit" name="submit" value="Submit" class="button" /></p>

<p>Τύπος:<br /> 
	<select name="type">
	<option <?php if($user->type=='advanced') echo 'selected'; ?> value="advanced">Διαχειριστής</option>
	<option <?php if($user->type=='registered') echo 'selected'; ?>  value="registered">Εγγεγραμένος</option>
	</select>
</p>


</form>

</div>

<?php
include("footer.php");

?>