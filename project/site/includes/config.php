<?php
/* το cookie της συνεδρίας δεν είναι προσβάσιμο από JavaScript και δεν δεχόμαστε session id από το URL */
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

/*
 * Μη αναμενόμενα σφάλματα (π.χ. της βάσης) καταγράφονται στο error log του server
 * και ο χρήστης βλέπει μόνο ένα γενικό μήνυμα, χωρίς λεπτομέρειες για τα queries.
 */
function diplomatiki_exception_handler($e) {
	error_log('diplomatiki: ' . $e);
	if (!headers_sent()) {
		header('HTTP/1.1 500 Internal Server Error');
	}
	echo 'Sorry! Something went wrong. Please try again later.';
	exit(1);
}
set_exception_handler('diplomatiki_exception_handler');

/*
 * Τα στοιχεία σύνδεσης στη βάση (DBHOST, DBUSER, DBPASS, DBNAME) δεν βρίσκονται στο repo.
 * Τα γράφει το applications.sh από το .env στο /etc/diplomatiki/db.php.
 * Μπορεί να οριστεί άλλη θέση με τη μεταβλητή περιβάλλοντος DIPLOMATIKI_DB_CONFIG.
 */
$dbConfigFile = getenv('DIPLOMATIKI_DB_CONFIG') ? getenv('DIPLOMATIKI_DB_CONFIG') : '/etc/diplomatiki/db.php';
if (!is_readable($dbConfigFile)) {
	die("Sorry! There seems to be a problem connecting to our database.");
}
require $dbConfigFile;

/*
 * Σύνδεση με PDO. Όλα τα queries γίνονται με prepared statements (βλ. db_query στο functions.php).
 * ATTR_EMULATE_PREPARES=false: οι παράμετροι στέλνονται χωριστά από το SQL στον MySQL server.
 */
try {
	$db = new PDO(
		'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8',
		DBUSER,
		DBPASS,
		array(
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		)
	);
} catch (PDOException $e) {
	error_log('diplomatiki: database connection failed: ' . $e->getMessage());
	die("Sorry! There seems to be a problem connecting to our database.");
}


/*
 * Δημιούργησε τις σταθερές DIR και DIRADMIN 
 * το REQUEST_URI αφορά το script που μας έκανε include / require 
 * τα script που μας κάνούν include είναι είτε στο project / είτε στο project /admin
 */
$path = pathinfo($_SERVER['REQUEST_URI']);
$app_root ='';
if (empty($path['extension']) ){
	/* this was included by index which was called implicit */
	$app_root = $path['dirname'];
	if (strlen($app_root) > 1 ) {
		// we have /somepath 
		$app_root .= '/';
	}
	if ($path['basename'][0]!='?') //do not include GET variables
		$app_root .= $path['basename'].'/';
		
	if ($app_root[strlen($app_root)-1] != '/')
		$app_root .= '/';
} else {
	/* this was included by /somepath/sthing.php  so we can safely use dirname on it*/
	$app_root = $path['dirname'].'/';
}
if (basename($app_root) == 'admin') {
	$app_root = dirname($app_root). '/';
}
/* Παραδοχή: το admin/ στο project root είναι το τελευταίο directory με αυτό το όνομα στο path */
if ( ($pos = strpos($app_root, 'admin'))!= false ) {
	do { 
		$cur = strpos($app_root, 'admin', $pos + strlen('admin'));
			if ($cur != false )
				$pos = $cur;
	} while ($cur!= false);
	$app_root = substr($app_root,0, $pos);
}

define ( 'DIR', $app_root);
define ( 'DIRADMIN', $app_root . 'admin/');
unset ($path);
unset ($app_root);
/*
if (isset($_ENV['OPENSHIFT_APP_NAME'] )) {
	define ('DIR', '/project/site/');
	define ('DIRAMDIN', '/project/site/admin/');
} else { 	
	define('DIR','/diplomatiki/');
	define('DIRADMIN', '/diplomatiki/admin/');
}
*/
define('SITETITLE','Πολικότητα επιθέτων');
define('NUM_OF_ADJ_TO_SHOW', 5);
define('included', 1);

define('ADJECTIVE_MAX_VOTING', 5);

class Adjective
{
	public $code="N/A";
	public $definition="N/A";
	public $adjective="N/A";
	public $examples = array();
	public $votes = 0;
}

class User
{
	public $username="N/A";
	public $password="N/A";
	public $name="N/A";
	public $type="N/A";
}

class Example
{
	public $code;
	public $adjective;
	public $example;

}
class Comment
{
	public $id;
	public $from;
	public $subject;
	public $comment;
}
include('functions.php');
?>
