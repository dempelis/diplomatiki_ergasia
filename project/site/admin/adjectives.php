<?php 

require('../includes/config.php'); 

login_required();

$search = "";
$username = $_SESSION['username'];

if(isset($_POST['deladjective'])){
	/* μόνο οι διαχειριστές διαγράφουν επίθετα (όπως εμφανίζεται και το κουμπί) */
	advanced_required();

	if (is_valid_post()) {
		deleteAdjective(post_string('deladjective'));
		$_SESSION['success'] = "To επίθετο διαγράφηκε";
	}
	
	
    header("Location: adjectives.php");
   	exit();
}

include('header.php');



if(isset($_POST["search"]) )
	$search = post_string("search");


?>


<div id="content">

<?php 
	messages();
?>

<h1>Διαχείριση Επιθέτων</h1>
<br/><br/>

<form action="" method="post">

	<h3>Αναζήτηση Επιθέτων</h3>
	<p>Επίθετο:<br /> <input name="search" type="text" value="<?php echo h($search) ?>" size="103" /></p>
	<p><input type="submit" name="submit" value="Αναζήτηση" class="button" /></p>
</form>

<?php
	if(   $search !='' ) 
	{
		$adjectives = getAdjectives( $search );
		
	
		
?>
<br/>
	<table>
		<tr>
			<th colspan='9' ><strong>Επίθετα </strong></th>
		</tr>
		<tr>
			<th><strong>Κωδικός</strong></th>
			<th><strong>Επίθετο</strong></th>
			<th><strong>Ορισμός</strong></th>
			<th><strong>Παραδείγματα</strong></th>
			<th colspan="3"><strong>Ranking</strong></th>
		 	<th colspan='2'><strong>Ενέργεια</strong></th>
		</tr>

		<?php
		
		foreach ( $adjectives as $adjective){
			$code = $adjective->code;

			echo "<tr>";
			echo "  <td>" . h($code) . "</td>
					<td>" . h($adjective->adjective) . "</td>
					<td>" . h($adjective->definition) . "</td>
					<td>" . h(implode(",", $adjective->examples)) ."</td>
					<td colspan='3'>
							<img src='" . h(DIR . "ranking.php?code=" . urlencode($code)) . "' />
					</td>";


			if(isAdvanced()){
				echo "<td> <a href=\"" . h(DIRADMIN . "adjective.php?code=" . urlencode($code)) . "\">Τροποποίηση</a> </td>";
				echo "<td> <a href=\"#\" class=\"js-post-delete\" data-url=\"adjectives.php\" data-field=\"deladjective\""
					. " data-value=\"" . h($code) . "\""
					. " data-confirm=\"" . h("Θέλετε σίγουρα να διαγράψετε τo επίθετο '" . $adjective->adjective . "';") . "\">Διαγραφή</a></td>";
			}
			else 	
				echo "<td colspan=\"2\" > &nbsp; </td>";
			echo "</tr>";
		}
		?>
	</table>
	
	
	<?php
	}
	?>


</div>
<hr/>
<p>
<?php
if(isAdvanced()){
?>
<p><a href="<?php echo DIRADMIN;?>adjective.php" class="button">Προσθήκη Επίθετου</a>
<?php 
}
?>
<a href="<?php echo DIRADMIN;?>voting.php" class="button">Ψήφος πολικότητας</a></p>


<br/><br/><br/>
<?php

include('footer.php');

?>