<?php 

require('../includes/config.php'); 


login_required();
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
if(isset($_POST['submit']) && is_valid_post()){

	$name = post_string('name');
	$definition = post_string('definition');
	$examples = post_string('examples');
	$code = post_string('code');

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
<?php echo csrf_field(); ?>


<table>
<tr><td>Κωδικός: </td><td> <input <?php echo $readonly ?> name="code" type="text" value="<?php echo h(strlen($code) ? $code : $randomcode); ?>"  size="103" /></td></tr>
<tr><td>Επίθετο: </td><td> <input name="name" type="text" value="<?php echo h($name) ?>"  size="103" /></td></tr>
<tr><td>Ορισμός: </td><td><input name="definition" type="text" value="<?php echo h($definition) ?>"  size="103" /></td></tr>
<tr><td>Παραδείγματα <br/>(διαχωρισμένα με κόμμα):</td><td> <textarea name="examples" cols="102" rows="4"><?php echo h($examples) ?></textarea> </td></tr>

</table>

<input type="submit" name="submit" value="Αποθήκευση" class="button" />

</form>




<br/><br/><br/>
<?php

include('footer.php');

?>
