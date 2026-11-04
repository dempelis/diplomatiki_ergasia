<?php 
$fh = fopen("all-adjectives.txt", "r");
if ($fh == false) {
	echo 'Το αρχείο δεν υπάρχει / δεν ανοίγει';
	exit;
}

$i=0;
$data = array();
while (!feof($fh)) {
	$read_more = true;
	$ln='';
	$str='';
	
	$pos = ftell($fh);
	
	do {
		$ln = fgets($fh);
		$str .=$ln;
	
		if ( strpos($ln,'</dl>') != false )
			$read_more = false;
	} while ($read_more && !feof($fh));
	
	if ( !$read_more ) {
	/* if we really read what we were expecting */
		if (strstr($str, 'α καπέλα')  ){
				$adj = 'α καπέλα';
		} else {
			$start = strpos($str,'<b>')+3;
			$end = strpos($str,' ', $start);
			$adj = trim(substr($str, $start, ($end - $start + 1)));
		}
	
		if(isset($_GET['like']) ) {
			$test_str = mb_substr($adj,0, mb_strlen($_GET['like']));
			$t = strcmp($test_str, $_GET['like']);
			if ($t < 0 )
				continue;
			else if ($t == 0 ) 
				$data[] = array('adj'=>$adj, 'offset'=>$pos);
			else 
				break;
		} else {
			$data[]=array('adj'=>$adj, 'offset'=>$pos);
		}

	}
}
fclose($fh);
$i=0;
foreach ($data as $el ) {
?>
	<p  class="adj" style="cursor: pointer; margin: 0px; background-color: <?php echo $i%2?'#FFFFFF':'#E5E5E5'; ?>;" onclick="<?php echo 'get_data(\''.$el['adj'].'\','.$el['offset'].')';?>"><?php echo $el['adj']; ?></p>
<?php	
	$i++;
}
?>