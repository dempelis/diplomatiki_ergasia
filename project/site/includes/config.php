<?php
session_start();

define('DBHOST','localhost');
define('DBUSER','sentiment');
define('DBPASS','JohnSnow');
define('DBNAME','sentiment');

$conn = @mysql_connect (DBHOST, DBUSER, DBPASS);
$conn = @mysql_select_db (DBNAME);
if(!$conn){
	die( "Sorry! There seems to be a problem connecting to our database.");
}
mysql_query("SET NAMES UTF8");


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
