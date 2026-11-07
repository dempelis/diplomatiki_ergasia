<?php 

require('../includes/config.php'); 

login_required();

$search = "";
$username = $_SESSION['username'];




include('header.php');

if(isset($_POST['submit']) && is_valid_post()){

	$codes = (isset($_POST['code']) && is_array($_POST['code'])) ? array_values($_POST['code']) : array();
	$votes = (isset($_POST['vote']) && is_array($_POST['vote'])) ? array_values($_POST['vote']) : array();

	for($i=0;$i<count($codes);$i++){
		/* το addVote δέχεται μόνο ψήφους -2..2 και περνά τις τιμές ως παραμέτρους στο query */
		if(isset($votes[$i]) && is_numeric($votes[$i]))
			addVote($codes[$i],$votes[$i],$username);
	}
	$_SESSION['success']='Οι ψήφοι αποθηκεύτηκαν';
	
}

?>


<div id="content">

<?php 
	messages();
?>

<h1>Πολικότητα Επιθέτων</h1>
<br/><br/>


<?php
	
	$adjectives = getAdjectivesForVoting( $username );
	if(count($adjectives)>0){

?>
<br/>
<form action="" method="post">
	<?php echo csrf_field(); ?>
	<table>
		<tr>
			<th colspan='7' ><strong>Επίθετα </strong></th>
		</tr>
		<tr>
			<th><strong>Κωδικός</strong></th>
			<th><strong>Επίθετο</strong></th>
			<th><strong>Ορισμός</strong></th>
			<th><strong>Παραδείγματα</strong></th>
			<th><strong>Ψήφοι</strong></th>
		 	<th colspan='2'><strong>Ψήφος</strong></th>
		</tr>

		<?php
		$count=0;
		foreach ( $adjectives as $adjective){
			
			if($count==ADJECTIVE_MAX_VOTING)
				break;
			
			$code = $adjective->code;
			
			echo "<tr>";
			echo "<input type=hidden name='code[]' value='" . h($code) . "' />";
			echo "  <td>" . h($code) . "</td>
					<td>" . h($adjective->adjective) . "</td>
					<td>" . h($adjective->definition) . "</td>
					<td>" . h(implode(",", $adjective->examples)) ."</td>
					<td>" . (int)$adjective->votes . "</td>";
			echo "  <td colspan='2'> <select name='vote[]'><option value=''>Ψήφος</option><option>-2</option><option>-1</option><option>0</option><option>1</option><option>2</option></select> </td>";
			echo "</tr>";
			$count++;
			
		}
		?>
	</table>
	<input type="submit" name="submit" value="Αποστολή" class="button" />
	<!-- onclick = "window.history.back() ; return false; " --> 
	<a href="adjectives.php"  class="button">Επιστροφή</a>
	</form>
	<?php
	}
	?>


</div>


<br/><br/><br/>
<?php

include('footer.php');

?>