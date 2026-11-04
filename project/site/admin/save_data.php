<?php
	include ('../includes/config.php');
	if (count($_POST) ){
		$name = $_POST['name'];
		$definition = $_POST['definition'];
		$examples = $_POST['examples'];
		$code = $_POST['code'];

		if ( ($res= addAdjective($code,$name,$definition ,$examples)) > 0 )
			echo 'Αποθηκεύτηκε με επιτυχία το επίθετο ' . $name. ' με κωδικό  '. $code . 'με ορισμό "' . $definition . '" και με κείμενο παραδειγμάτων "' . $examples . '"';
		else 
			echo 'Προβλημα επικοινωνίας με την βάση δεδομένων';
	} else {
		echo 'Δεν παρελήφθησαν δεδομένα (empty POST)';
	} 
	
?>