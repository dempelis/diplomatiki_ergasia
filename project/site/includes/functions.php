<?php
if (!defined('included')){
die('You cannot access this file directly!');
}

/*
 * ------------------------------------------------------------------
 *  Βοηθητικές συναρτήσεις ασφάλειας
 *  (γραμμένες ώστε να τρέχουν και σε PHP 5.3)
 * ------------------------------------------------------------------
 */

/*
 * Εκτελεί ένα prepared statement και επιστρέφει το PDOStatement.
 * Οι τιμές περνούν ΜΟΝΟ ως παράμετροι (?) και ποτέ μέσα στο SQL string,
 * οπότε δεν είναι δυνατό SQL injection, ακόμα κι αν προέρχονται από τη βάση.
 */
function db_query($sql, $params = array()) {
	global $db;
	$stmt = $db->prepare($sql);
	$i = 1;
	foreach ($params as $value) {
		if (is_array($value) || is_object($value)) {
			throw new InvalidArgumentException('db_query: parameter ' . $i . ' must be a scalar');
		}
		$stmt->bindValue($i, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
		$i++;
	}
	$stmt->execute();
	return $stmt;
}

/* true αν η εξαίρεση είναι παραβίαση περιορισμού (διπλό κλειδί, foreign key) */
function is_constraint_violation($e) {
	return ($e instanceof PDOException) && $e->getCode() == '23000';
}

/* Escape για εμφάνιση σε HTML, τόσο σε κείμενο όσο και σε τιμές attributes */
function h($str) {
	return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/* Κρυπτογραφικά ασφαλή τυχαία bytes */
function random_token_bytes($length) {
	if (function_exists('openssl_random_pseudo_bytes')) {
		$strong = false;
		$bytes = openssl_random_pseudo_bytes($length, $strong);
		if ($bytes !== false && $strong && strlen($bytes) == $length) {
			return $bytes;
		}
	}
	$fh = @fopen('/dev/urandom', 'rb');
	if ($fh) {
		$bytes = fread($fh, $length);
		fclose($fh);
		if (strlen($bytes) == $length) {
			return $bytes;
		}
	}
	throw new Exception('No secure source of random bytes available');
}

/* Σύγκριση strings σε σταθερό χρόνο (δεν υπάρχει hash_equals πριν την PHP 5.6) */
function equals_constant_time($a, $b) {
	if (!is_string($a) || !is_string($b) || strlen($a) != strlen($b)) {
		return false;
	}
	$diff = 0;
	$len = strlen($a);
	for ($i = 0; $i < $len; $i++) {
		$diff |= ord($a[$i]) ^ ord($b[$i]);
	}
	return $diff === 0;
}

/*
 * CSRF: κάθε φόρμα που αλλάζει δεδομένα στέλνει ένα token της συνεδρίας,
 * ώστε μια ξένη σελίδα να μη μπορεί να κάνει ενέργειες στο όνομα του χρήστη.
 */
function csrf_token() {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_token_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

function csrf_field() {
	return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '" />';
}

function csrf_check() {
	$sent = (isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])) ? $_POST['csrf_token'] : '';
	if (!isset($_SESSION['csrf_token']) || !equals_constant_time($_SESSION['csrf_token'], $sent)) {
		header('HTTP/1.1 403 Forbidden');
		die('Η φόρμα έληξε ή δεν είναι έγκυρη. Επιστρέψτε, ανανεώστε τη σελίδα και δοκιμάστε ξανά.');
	}
}

/* true μόνο για αίτημα POST με έγκυρο CSRF token (αλλιώς σταματά με 403) */
function is_valid_post() {
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
		return false;
	}
	csrf_check();
	return true;
}

/* Τιμή από $_POST ως string ('' αν λείπει ή δεν είναι string) */
function post_string($name) {
	return (isset($_POST[$name]) && is_string($_POST[$name])) ? $_POST[$name] : '';
}

/*
 * Κωδικοί: bcrypt μέσω crypt() ($2y$ υπάρχει από PHP 5.3.7).
 * Οι παλιοί κωδικοί MD5 γίνονται δεκτοί και αναβαθμίζονται σε bcrypt στο επόμενο login.
 */
function hash_password($password) {
	$salt = substr(strtr(base64_encode(random_token_bytes(16)), '+', '.'), 0, 22);
	$hash = crypt($password, '$2y$10$' . $salt);
	if (!is_string($hash) || strlen($hash) != 60) {
		throw new Exception('bcrypt is not available');
	}
	return $hash;
}

function is_legacy_md5_hash($stored) {
	return strlen($stored) == 32 && ctype_xdigit($stored);
}

function verify_password($password, $stored) {
	if (!is_string($stored) || $stored === '') {
		return false;
	}
	if (is_legacy_md5_hash($stored)) {
		return equals_constant_time(strtolower($stored), md5($password));
	}
	return equals_constant_time(crypt($password, $stored), $stored);
}

function valid_user_type($type) {
	return in_array($type, array('advanced', 'registered'), true);
}

/*
 * ------------------------------------------------------------------
 *  Συνεδρία / δικαιώματα
 * ------------------------------------------------------------------
 */

function login($user, $pass){
	$user = is_string($user) ? $user : '';
	$pass = is_string($pass) ? $pass : '';

	$row = db_query("SELECT username, password, userType FROM users WHERE username = ?", array($user))->fetch();

	if ($row && verify_password($pass, $row['password'])) {
		if (is_legacy_md5_hash($row['password'])) {
			db_query("UPDATE users SET password = ? WHERE username = ?", array(hash_password($pass), $row['username']));
		}
		/* νέο session id μετά το login, ώστε να μη γίνεται session fixation */
		session_regenerate_id(true);
		$_SESSION['authorized'] = true;
		$_SESSION['username'] = $row['username'];
		$_SESSION['userType'] = $row['userType'];
		header('Location: '.DIRADMIN);
		exit();
	} else {
		$_SESSION['error'] = 'Sorry, wrong username or password';
	}
}


function logged_in() {
	if( isset($_SESSION['authorized'])  && $_SESSION['authorized'] == true) {
		return true;
	} else {
		return false;
	}
}

function isAdvanced() {
	if( logged_in() && isset( $_SESSION['userType'])  &&  $_SESSION['userType'] == 'advanced') {
		return true;
	} else {
		return false;
	}
}

function login_required() {
	if(logged_in()) {
		return true;
	} else {
		header('Location: login.php');
		exit();
	}
}

function advanced_required() {
	if(isAdvanced()) {
		return true;
	} else {
		$_SESSION['error'] = "Δεν έχετε πρόσβαση στη συγκεκριμένη σελίδα";
		header('Location: index.php');
		exit();
	}
}

function logout(){
	session_destroy();
	header('Location:login.php');
	exit();
}

function messages() {
    $message = '';
    if(isset($_SESSION['success']) && $_SESSION['success'] != '') {
        $message = '<div class="msg-ok">'.$_SESSION['success'].'</div>';
        $_SESSION['success'] = '';
    }
    if( isset($_SESSION['error']) &&  $_SESSION['error'] != '') {
        $message = '<div class="msg-error">'.$_SESSION['error'].'</div>';
        $_SESSION['error'] = '';
    }
    echo "$message";
}

function errors($error){
	if (!empty($error)) {
			$showError = '';
			$i = 0;
			while ($i < count($error)){
			$showError.= "<div class=msg-error>".h($error[$i])."</div>";
			$i ++;}
			echo $showError;
	}
}

/*
 * ------------------------------------------------------------------
 *  Σχόλια
 * ------------------------------------------------------------------
 */

function addComment($from, $subject, $comment){
	db_query("INSERT INTO `contactus` (`from`, `subject`, `comment`) VALUES (?, ?, ?)",
		array((string)$from, (string)$subject, (string)$comment));
	return true;
}

function getComments()
{
	$comment_list = array();
	$result = db_query("SELECT * FROM `contactus`");
	while ($row = $result->fetch()) {
		$comment = new Comment();
		$comment->id = $row['id'];
		$comment->from = $row['from'];
		$comment->subject = $row['subject'];
		$comment->comment = $row['comment'];

		$comment_list[] = $comment;
	}
	return $comment_list;
}

function deleteComment($id) {
	db_query("DELETE FROM contactus WHERE id = ?", array((int)$id));
}

/*
 * ------------------------------------------------------------------
 *  Χρήστες
 * ------------------------------------------------------------------
 */

function rowToUser($row) {
	$user = new User();
	$user->username = $row['username'];
	$user->password = $row['password'];
	$user->name = $row['name'];
	$user->type = $row['userType'];
	return $user;
}

function getUsers()
{
	$user_list = array();
	$result = db_query("SELECT * FROM users ORDER BY username");
	while ($row = $result->fetch()) {
		$user_list[] = rowToUser($row);
	}
	return $user_list;
}


function getUser($username)
{
	$row = db_query("SELECT * FROM users WHERE username = ?", array((string)$username))->fetch();
	return $row ? rowToUser($row) : new User();
}

function updateUser($username,$password ,$name,$type){
	if (!valid_user_type($type)) {
		return false;
	}
	if ($password == '') {
		db_query("UPDATE users SET name = ?, userType = ? WHERE username = ?",
			array((string)$name, $type, (string)$username));
	} else {
		db_query("UPDATE users SET name = ?, userType = ?, password = ? WHERE username = ?",
			array((string)$name, $type, hash_password((string)$password), (string)$username));
	}
	return true;
}


function addUser($username,$name,$password,$type ){
	if (!valid_user_type($type) || $username == '' || $password == '') {
		return false;
	}
	try {
		db_query("INSERT INTO users (username, password, name, userType) VALUES (?, ?, ?, ?)",
			array((string)$username, hash_password((string)$password), (string)$name, $type));
	} catch (PDOException $e) {
		if (is_constraint_violation($e)) {
			return false; // το username υπάρχει ήδη
		}
		throw $e;
	}
	return true;
}

function deleteUser($username) {
	db_query("DELETE FROM users WHERE username = ?", array((string)$username));
}

/*
 * ------------------------------------------------------------------
 *  Επίθετα
 * ------------------------------------------------------------------
 */

function getExamples($code) {
	$examples = array();
	$result = db_query("SELECT example FROM examples WHERE code = ?", array((string)$code));
	while ($row = $result->fetch()) {
		$examples[] = $row['example'];
	}
	return $examples;
}

function rowToAdjective($row) {
	$adjective = new Adjective();
	$adjective->code = $row['code'];
	$adjective->adjective = $row['adjective'];
	$adjective->definition = $row['definition'];
	$adjective->examples = getExamples($row['code']);
	return $adjective;
}

function countAdjectives() {
	$total = db_query("SELECT COUNT(*) FROM adjectives")->fetchColumn();
	return (int)$total;
}

function showAdjectives($page, $show=NUM_OF_ADJ_TO_SHOW) {
	$page = max(1, (int)$page);
	$show = max(1, (int)$show);
	$start = ($page - 1) * $show;

	$adjective_list = array();
	$result = db_query("SELECT * FROM adjectives LIMIT ?, ?", array($start, $show));
	while ($row = $result->fetch()) {
		$adjective_list[] = rowToAdjective($row);
	}
	return $adjective_list;
}

function getAdjectives($search){
	$adjective_list = array();
	$result = db_query("SELECT * FROM adjectives WHERE adjective LIKE ?", array(trim((string)$search)));
	while ($row = $result->fetch()) {
		$adjective_list[] = rowToAdjective($row);
	}
	return $adjective_list;
}

function getAdjective($code){
	$row = db_query("SELECT * FROM adjectives WHERE code = ?", array((string)$code))->fetch();
	return $row ? rowToAdjective($row) : new Adjective();
}

function getRanking($code){
	$rankings = array(0,0,0,0,0);
	$votes = 0;
	$result = db_query("SELECT COUNT(*) AS count, `rank` FROM `rankings` WHERE code = ? GROUP BY `rank`", array((string)$code));
	while ($row = $result->fetch()) {
		$rankings[$row['rank'] + 2] = (int)$row['count'];
		$votes += (int)$row['count'];
	}
	if ($votes == 0)
		return 0;
	else
		return $rankings;
}

/*
 * Αποθηκεύει (νέο ή υπάρχον) επίθετο και αντικαθιστά τα παραδείγματά του.
 * Παλιότερα γινόταν με REPLACE INTO, που στη MySQL είναι DELETE + INSERT: λόγω του
 * ON DELETE CASCADE έσβηνε και όλες τις ψήφους (rankings) του επιθέτου σε κάθε επεξεργασία.
 */
function addAdjective($code,$adjective,$definition ,$examples){
	global $db;
	$code = (string)$code;

	$db->beginTransaction();
	try {
		db_query("INSERT INTO adjectives (code, adjective, definition) VALUES (?, ?, ?)
		          ON DUPLICATE KEY UPDATE adjective = VALUES(adjective), definition = VALUES(definition)",
			array($code, (string)$adjective, (string)$definition));

		db_query("DELETE FROM examples WHERE code = ?", array($code));
		foreach (explode(',', (string)$examples) as $example) {
			db_query("INSERT INTO examples (code, example) VALUES (?, ?)", array($code, $example));
		}
		$db->commit();
	} catch (Exception $e) {
		$db->rollBack();
		throw $e;
	}
	return true;
}

function deleteAdjective($code) {
	db_query("DELETE FROM adjectives WHERE code = ?", array((string)$code));
}

/*
 * ------------------------------------------------------------------
 *  Ψηφοφορία
 * ------------------------------------------------------------------
 */

function getAdjectivesForVoting($username){
	$adjective_list = array();
	$result = db_query("SELECT * FROM `adjectives` WHERE code NOT IN (SELECT code FROM rankings WHERE username = ?) LIMIT 1000",
		array((string)$username));

	while ($row = $result->fetch()) {
		$adjective = rowToAdjective($row);
		$adjective->votes = (int)db_query("SELECT COUNT(*) FROM rankings WHERE code = ?", array($adjective->code))->fetchColumn();
		$adjective_list[] = $adjective;
	}

	usort($adjective_list, "cmp");

	return $adjective_list;
}

function cmp($a, $b){
	if($a->votes < $b->votes)
		return false;
	else
		return true;
}

/* Καταχωρεί ψήφο (-2 .. 2). Επιστρέφει false αν η ψήφος δεν είναι έγκυρη ή έχει ήδη δοθεί. */
function addVote($code,$vote,$username){
	if (!is_scalar($code) || !is_numeric($vote)) {
		return false;
	}
	$vote = (int)$vote;
	if ($vote < -2 || $vote > 2) {
		return false;
	}
	try {
		db_query("INSERT INTO rankings (code, `rank`, username) VALUES (?, ?, ?)",
			array((string)$code, $vote, (string)$username));
	} catch (PDOException $e) {
		if (is_constraint_violation($e)) {
			return false; // διπλή ψήφος ή ανύπαρκτο επίθετο
		}
		throw $e;
	}
	return true;
}
?>
