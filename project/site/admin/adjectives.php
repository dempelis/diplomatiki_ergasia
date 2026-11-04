<?php 

require('../includes/config.php'); 

login_required();

if(isset($_GET['logout'])){
	logout();
}

$search = "";
$username = $_SESSION['username'];

if(isset($_GET['deladjective'])){
		
	$deladjective = $_GET['deladjective'];

	$deladjective = mysql_escape_string($deladjective);
	
	$result = mysql_query("DELETE FROM adjectives WHERE code= '$deladjective'") or die(mysql_error());

	
	if($result)
		$_SESSION['success'] = "To επίθετο διαγράφηκε"; 
	
	
    header("Location: adjectives.php");
   	exit();
}

include('header.php');



if(isset($_POST["search"]) )
	$search = $_POST["search"];


?>


<div id="content">

<?php 
	messages();
?>

<h1>Διαχείριση Επιθέτων</h1>
<br/><br/>

<form action="" method="post">

	<h3>Αναζήτηση Επιθέτων</h3>
	<p>Επίθετο:<br /> <input name="search" type="text" value="<?php echo $search ?>" size="103" /></p>
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
			echo "  <td>$code</td>
					<td>$adjective->adjective</td>
					<td>$adjective->definition</td>
					<td>" . implode(",", $adjective->examples) ."</td>
					<td colspan='3'>
					<!--	<iframe frameborder=0 width='150' height='100' src='" . DIR  ."ranking.php?code=$code'></iframe> -->
							<img src='" . DIR . "ranking.php?code=$code' />
					</td>";		
					
			
			if(isAdvanced()){
				echo "<td> <a href=\"".DIRADMIN."adjective.php?code=$code\">Τροποποίηση</a> </td>";
				echo "<td> <a href=\"javascript:deladjective('$code', '$adjective->adjective');\">Διαγραφή</a></td>";	
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