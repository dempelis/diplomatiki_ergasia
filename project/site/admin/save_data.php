<?php
	require ('../includes/config.php');

	/* καλείται με AJAX από τη σελίδα εισαγωγής (imports.php): μόνο για διαχειριστές */
	login_required();
	advanced_required();

	/* η απάντηση εμφανίζεται με alert(), άρα είναι απλό κείμενο και όχι HTML */
	header('Content-Type: text/plain; charset=utf-8');

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($_POST)) {
		csrf_check();

		$name = post_string('name');
		$definition = post_string('definition');
		$examples = post_string('examples');
		$code = post_string('code');

		if ( addAdjective($code,$name,$definition ,$examples) )
			echo 'Αποθηκεύτηκε με επιτυχία το επίθετο ' . $name. ' με κωδικό  '. $code . 'με ορισμό "' . $definition . '" και με κείμενο παραδειγμάτων "' . $examples . '"';
		else
			echo 'Προβλημα επικοινωνίας με την βάση δεδομένων';
	} else {
		echo 'Δεν παρελήφθησαν δεδομένα (empty POST)';
	}

?>
