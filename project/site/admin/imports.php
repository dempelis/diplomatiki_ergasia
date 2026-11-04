<?php 
	function create_index($filename) {
		$fh = fopen($filename, "r");
		if ($fh == false) {
			return false;
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
				if (strstr($str, 'α καπέλα') ){
						$adj = 'α καπέλα';
				} else {
					$start = strpos($str,'<b>')+3;
					$end = strpos($str,' ', $start);
					$adj = trim(substr($str, $start, ($end - $start + 1)));
				}
				
				$data[]=array('adj'=>$adj, 'offset'=>$pos);	
			}
		//	echo $adj . ',' . $pos ."\n";
			
		}
		fclose($fh);
		return $data;
	}
	require('../includes/config.php'); 
	include_once ('./simplehtmldom_1_5/simple_html_dom.php');
	
	login_required();
	advanced_required();
	
	if(isset($_GET['logout'])){
		logout();
	}
	include('header.php');
?>
<script>
	function get_data(adjective, offset) {
		var request = $.ajax({
			url: "load_data.php",
			type: "GET",
			data: { "adj" : adjective, "pos" : offset },
			// data: "pos=" + offset,
			datatype: "html"
		});
		
		request.done(function(msg) {
			$("#wspace").html(msg);
		});
		
		request.fail(function(jqXHR, textStatus) {
			alert("Request failed: " + textStatus);
		});
	}
	$(function(){
		$("#mglass").click(function(){
			var request = $.ajax({
				url: "create_index.php",
				type: "GET",
				data: { "like" : $("#search").val() },
				datatype: "html"
			});
			
			request.done(function(msg) {
				$("#index-data").html(msg);
			});
			
			request.fail(function(jqXHR, textStatus) {
				alert("Request failed: " + textStatus);
			});
		});
	});
</script>
<div id="content">
	<p>Εισαγωγή στην βάση δεδομένων επιθέτων που έχουν "κατεβαστεί" από το 
	<a href="http://www.greek-language.gr/greekLang/modern_greek/tools/lexica/triantafyllides/">Λεξικό της κοινής νεοελληνικής</a><p>
	<div id="index" style="width: 160px; height: 660px; margin-bottom: 8px; float: left;">
		<div id="title-search" style="width: 160px; height: 60px;" >
		<p style="text-align: center; text-decoration: underline; margin-top:0px; margin-bottom: 3px; font-weight: bold;">Ευρετήριο</p>
		<center>
		<input type="text" name="search" id="search" value="" size="15" />&nbsp;<img src="<?php echo DIR.'css/images/mglass.png'; ?>" id="mglass" height="16" width="16" />
		</center>
		</div>
		<div id="index-data" style="width: 160px; height: 600px; overflow-y: scroll">
	<?php
		$index_data = create_index('all-adjectives.txt');
		if($index_data == false ) {
			echo '<p>Απέτυχε η δημιουργία του ευρετηρίου</p>';
		} else {
			$i=0;
			foreach ($index_data as $el ) {
				?>
				<p  class="adj" style="cursor: pointer; margin: 0px; background-color: <?php echo $i%2?'#FFFFFF':'#E5E5E5'; ?>;" onclick="<?php echo 'get_data(\''.$el['adj'].'\','.$el['offset'].')';?>"><?php echo $el['adj']; ?></p>
				<?php	
				$i++;
			}
		}
	?>
		</div>
	</div><!--index-->
		<div id="wspace" style="width: auto; height: 660px; overflow-y: scroll; margin-bottom: 8px; padding: 5px 0px 0px 5px;">
		<p>Παρακαλώ επιλέξτε ένα επίθετο από το ευρετήριο</p>
	</div><!--wspace-->
</div> <!-- content -->
<?php
	include('footer.php');
?>