<?php
	require ('../includes/config.php');
	include_once ('./simplehtmldom_1_5/simple_html_dom.php');
	$fh = fopen("all-adjectives.txt", "r");
	if ($fh == false) {
		echo '<p>Δεν μπόρεσα να ανοίξω το αρχείο</p>';
		exit;
	}
	$read_more = true;
	if (isset($_GET['pos']) ){
		fseek($fh,$_GET['pos'], SEEK_SET);
	} else {
		fclose($fh);
		header("Location: index.php");
		exit; 
	}
	$ln='';
	$str='';

	while (!feof($fh) && $read_more) {
		$ln = fgets($fh);
		$str .=$ln;
		
		if ( strpos($ln,'</dl>') != false )
			$read_more = false;
	}
	
	fclose($fh);
	$html = new simple_html_dom();
	$html->load($str);
	
	/*
	 * id of adjective 
	 */
	 $ret = $html->find('dl',0);
	 $code = $ret->id;
	/*
	 *  Επίθετο και καταλήξεις
	 */
	$ret = $html->find('b', 0);
	$mpe='';
	foreach ($ret->nodes as $c ) {
		if ($c->tag == 'text')
			$mpe .= $c->plaintext;
	}

	$adjs = array();
	$adjs = explode('-', $mpe);
	foreach ( $adjs as $i ) {
		trim ($i);
	}
	$adj = $adjs[0];
	unset($adjs);
	unset($mpe);
	/*
	 * Ορισμός  
	 */
	$ret = $html->find('dt',0);
	$adjdefs = array();
	foreach($ret->nodes as $c ) {
		if ($c->tag == 'text' && !strpos($c->innertext, '[') && !strpos($c->innertext, ']') && !strpos($c->innertext, 'ΕΠIΡ')  ) {
			$mpe = trim($c->innertext);
			if (!empty($mpe) && strlen($mpe) > 2) {
				$adjdefs[] = $mpe;
			}
		}
	}
	/* Παραδείγματα */
	$examples = array();
	$ret = $html->find('i');
	foreach($ret as $c ) {
			if ( $c->parent()->tag != 'p')
				$examples[] = $c->plaintext;
	}

	/* Properly close simple_html_dom to avoid memmory leaks */
	$html->clear();
	unset($html);
	unset($ret);
	/* Έλεγξε αν υπάρχει ήδη στην βάση */
	$adjs_list = array();
	$sql = "SELECT code, definition from adjectives WHERE adjective='".mysql_escape_string($_GET['adj'])."';";
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result)) {
		$a = new Adjective;
		$a->code = $row['code'];
		$a->definition = $row['definition'];
		$adjs_list[] = $a;
	}
		
?>
<p style="text-align:center; font-weight:bold; text-decoration: underline;"><?php echo $_GET['adj'];?></p>
<p style="margin: 1px;"><?php echo $str;?></p>
<!--
<p style="margin: 1px;"><span style="font-weight: bold;">Html : </span><?php echo htmlspecialchars($str); ?></p>
-->
<hr>
<?php
	if (count($adjs_list) ) {
	?>
	<p style="margin: 1px; padding-left: 5px;">Το επίθετο <?php echo $_GET['adj']; ?> υπάρχει ήδη : 
	<?php
		foreach($adjs_list as $a ) {
			echo '<a class="tooltips" href="adjective.php?code='. $a->code .'">'. $a->code .'<span>'.$a->definition.'</span></a>&nbsp;';
		}
	?>
	. Ακολουθήστε τον σύνδεσμο για να επεξεργαστείτε τον ορισμό.</p>
	<hr>
	<?php
	}	
	$i=0;
	if (count($adjdefs) ==  0 ) {
		/* parse error */
	?>
				<div id="div_<?php echo $i;?>">
				<form method="post" id="form_<?php echo $i;?>" name="form_<?php echo $i;?>" action="";>
				<input type="hidden" name="name" value="<?php echo $_GET['adj'];?>" />
				<table style="border: solid thin black; margin-top: 5px;">
				<tr><th colspan="2" style="text-align: center;"><?php echo $_GET['adj']; ?></th></tr>
				<tr><th>Code</th><td><input type="text" name="code" value="<?php echo $code ; ?>" /></td></tr>
				<tr><th>Ορισμός</th><td><input type="text" name="definition" value="" size="100" /></td></tr>
				<tr><th>Παράδειγμα</th><td><input type="text" name="examples" value="" size="100" /></td></tr>
				<tr><td colspan="2" style="text-align: right;"><span class="button" style="cursor: pointer;">Αποθήκευση</span></td></tr>
				</table>
				</form>
				</div>	
	<?php
	}
	foreach($adjdefs as $adjdef) {
	?>
				<div id="div_<?php echo $i;?>">
				<form method="post" id="form_<?php echo $i;?>" name="form_<?php echo $i;?>" action="";>
				<input type="hidden" name="name" value="<?php echo $_GET['adj'];?>" />
				<table style="border: solid thin black; margin-top: 5px;">
				<tr><th colspan="2" style="text-align: center;"><?php echo $_GET['adj']; ?></th></tr>
				<tr><th>Code</th><td><input type="text" name="code" value="<?php echo $code .'_'.$i; ?>" /></td></tr>
				<tr><th>Ορισμός</th><td><input type="text" name="definition" value="<?php echo $adjdef; ?>" size="100" /></td></tr>
				<tr><th>Παράδειγμα</th><td><input type="text" name="examples" value="<?php echo $examples[$i]; ?>" size="100" /></td></tr>
				<tr>
					<td colspan="2" style="text-align: right;">
						<span class="button" style="cursor: pointer;">Αποθήκευση</span>
					</td>
				</tr>
				</table>
				</form>
				</div>
	<?php
		$i++;
	}
	
?>
<script>
	$(function(){
		$(".button").click(function(){
			var f = $(this).parents("form");
			
			var request = $.ajax({
				url: "save_data.php",
				type: "POST",
				data: f.serialize(),
				datatype: "html"
			});
		
			request.done(function(msg) {
				alert(msg);
			});
		
			request.fail(function(jqXHR, textStatus) {
				alert("Request failed: " + textStatus);
			});
		});
	});
</script>