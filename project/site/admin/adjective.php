<?php 

require('../includes/config.php'); 


advanced_required();

$username = $_SESSION['username'];

$code="";
$name="";
$definition="";
$examples="";
$readonly="";

if(isset($_GET['code'])){
	$readonly=" readonly ";
	$adjective = getAdjective($_GET['code']);
	$code= $adjective->code;
	$name=$adjective->adjective;
	$definition=$adjective->definition;
	$examples= implode(',',$adjective->examples);

}
if(isset($_POST['submit'])){

	$name = $_POST['name'];
	$definition = $_POST['definition'];
	$examples = $_POST['examples'];
	$code = $_POST['code'];

	$res= addAdjective($code,$name,$definition ,$examples);
	
	if( $res>0 ) {
		$_SESSION['success'] = 'Οι αλλαγές αποθηκεύτηκαν με επιτυχία';	
			
	}

}

$randomcode = '0_' . time();


include("header.php");

?>


<div id="content">

<?php 
	messages();
?>

<h1>Προσθήκη Επιθέτου</h1>

<form action="" method="post" >


<table>
<tr><td>Κωδικός: </td><td> <input <?php echo $readonly ?> name="code" type="text" value="<?php echo strlen($code) ? $code : $randomcode; ?>"  size="103" /></td></tr>
<tr><td>Επίθετο: </td><td> <input name="name" type="text" value="<?php echo $name ?>"  size="103" /></td></tr>
<tr><td>Ορισμός: </td><td><input name="definition" type="text" value="<?php echo $definition ?>"  size="103" /></td></tr>
<tr><td>Παραδείγματα <br/>(διαχωρισμένα με κόμμα):</td><td> <textarea name="examples" cols="102" rows="4"><?php echo $examples ?></textarea> </td></tr>

</table>

<input type="submit" name="submit" value="Αποθήκευση" class="button" />

</form>




<br/><br/><br/>
<?php

include('footer.php');

?>
