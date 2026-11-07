<?php
require('../includes/config.php');

/* καλείται με AJAX από τη σελίδα εισαγωγής (imports.php): μόνο για διαχειριστές */
login_required();
advanced_required();

$like = (isset($_GET['like']) && is_string($_GET['like'])) ? $_GET['like'] : null;

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
	
		if($like !== null ) {
			$test_str = mb_substr($adj,0, mb_strlen($like));
			$t = strcmp($test_str, $like);
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
	<p  class="adj" style="cursor: pointer; margin: 0px; background-color: <?php echo $i%2?'#FFFFFF':'#E5E5E5'; ?>;" onclick="<?php echo h('get_data(' . json_encode($el['adj']) . ',' . (int)$el['offset'] . ')');?>"><?php echo h($el['adj']); ?></p>
<?php	
	$i++;
}
?>