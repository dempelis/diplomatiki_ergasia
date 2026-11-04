<?php
if (!defined('included')){
die('You cannot access this file directly!');
}

function login($user, $pass){
   $user = strip_tags(mysql_escape_string($user));
   $pass = strip_tags(mysql_escape_string($pass));
   $pass = md5($pass);
 
   $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
   $result = mysql_query($sql) or die('Query failed. ' . mysql_error());
      
   if (mysql_num_rows($result) == 1) {
	  $_SESSION['authorized'] = true;
	  $_SESSION['username'] = $user;
	  $row = mysql_fetch_array($result);
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
	if( isset( $_SESSION['userType'])  &&  $_SESSION['userType'] == 'advanced') {
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
			$i = 0;
			while ($i < count($error)){
			$showError.= "<div class=msg-error>".$error[$i]."</div>";
			$i ++;}
			echo $showError;
	}
} 

function addComment($from, $subject, $comment){
	$from = mysql_escape_string($from);
	$subject = mysql_escape_string($subject);
	$comment = mysql_escape_string($comment);
	
	$sql = "INSERT INTO `contactus`( `from`, `subject`, `comment`) VALUES ('" . $from ."', '". $subject . "', '". $comment ."')";
	$result  = mysql_query($sql)  or die(mysql_error());
	
	if(!$result)
		return false;
	else 
		return true;
}
function getComments()
{
	$comment_list = array();
	$sql = "SELECT * FROM`contactus`";
	$result = mysql_query($sql);
	$i=0;
	while ($row = mysql_fetch_array($result)) {
		$comment = new Comment();
		$comment->id = $row['id'];
		$comment->from = $row['from'];
		$comment->subject = $row['subject'];
		$comment->comment = $row['comment'];
		
		$comment_list[$i] = $comment;
		$i++;
	}
	return $comment_list;
}
function getUsers()
{
	$user_list=array();
	$sql="SELECT * FROM users ORDER BY username ";
	
	$result = mysql_query($sql);
	$i=0;
	while($row = mysql_fetch_array($result)) {
		$user=new User();
		$user->username=$row['username'];
		$user->password=$row['password'];
		$user->name=$row['name'];
		$user->type=$row['userType'];
		
		$user_list[$i]=$user;
		$i++;
	}
	
	return $user_list;
}


function getUser($username)
{
	$username = mysql_escape_string($username);
	
	$user=new User();
	$sql="SELECT * FROM users WHERE username='$username'" ;
	
	$result = mysql_query($sql);
	
	while( $row = mysql_fetch_array($result)) {
		$user->username=$row['username'];
		$user->password=$row['password'];
		$user->name=$row['name'];
		$user->type=$row['userType'];
	}
	
	return $user;
}

function updateUser($username,$password ,$name,$type){
	
	$name = mysql_escape_string($name);
	$username = mysql_escape_string($username);
	$password = mysql_escape_string($password);
	$type = mysql_escape_string($type);
	
	if($password=='')
		$sql="UPDATE users SET name='$name', userType='$type' WHERE username='$username'";
	else
		$sql="UPDATE users SET name='$name',userType='$type', password=MD5('$password') WHERE username='$username'";
	
	$result = mysql_query($sql);
	if(!$result)
		return false;
	else 
		return true;

}


function addUser($username,$name,$password,$type ){
	
	$name = mysql_escape_string($name);
	$username = mysql_escape_string($username);
	$password = mysql_escape_string($password);
	$type = mysql_escape_string($type);
	
	
	$result  = mysql_query("INSERT INTO users (username,password,name,userType) VALUES ('$username',MD5('$password'),'$name','$type')")  or die(mysql_error());
	
	if(!$result)
		return false;
	else 
		return true;
}

function countAdjectives() {
	$sql = "SELECT COUNT(*) FROM adjectives";
	$result = mysql_query($sql) or die (mysql_error());
	$total = mysql_fetch_row($result);

	return $total[0];
}
function showAdjectives($page, $show=NUM_OF_ADJ_TO_SHOW) {
	$adjective_list = array();
	$start = ($page - 1) * NUM_OF_ADJ_TO_SHOW;
	$sql = "SELECT * FROM adjectives LIMIT " . $start .", " . $show;
	// var_dump($sql); exit;
	$result = mysql_query($sql) or die(mysql_error());
	
	while($row = mysql_fetch_array($result))
	{
		$adjective=new Adjective();
		$adjective->code=$row['code'];
		$adjective->adjective=$row['adjective'];
		$adjective->definition=$row['definition'];
		
		$sql="SELECT example FROM examples where code='$adjective->code' ";
		$res = mysql_query($sql);
		$i=0;
		while($myrow = mysql_fetch_array($res))
		{
			$adjective->examples[$i] = $myrow['example'];
			$i++;
		}
		$adjective_list[]=$adjective;		
	}
	return $adjective_list;
}
function getAdjectives($search){
	$search = mysql_escape_string(trim($search));

	$adjective_list=array();
	$sql="SELECT * FROM adjectives where adjective LIKE '$search' ";
	
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result))
	{
		$adjective=new Adjective();
		$adjective->code=$row['code'];
		$adjective->adjective=$row['adjective'];
		$adjective->definition=$row['definition'];
		
		$sql="SELECT example FROM examples where code='$adjective->code' ";
		$res = mysql_query($sql);
		$i=0;
		while($myrow = mysql_fetch_array($res))
		{
			$adjective->examples[$i] = $myrow['example'];
			$i++;
		}
		$adjective_list[]=$adjective;		
	}
	
	return $adjective_list;

}

function getAdjective($code){
	$code = mysql_escape_string($code);

	$adjective=new Adjective();
	$sql="SELECT * FROM adjectives where code='$code' ";
	
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result)) {
		$adjective->code=$row['code'];
		$adjective->adjective=$row['adjective'];
		$adjective->definition=$row['definition'];
		
		$sql="SELECT example FROM examples where code='$adjective->code' ";
		$res = mysql_query($sql);
		$i=0;
		while($myrow = mysql_fetch_array($res))
		{
			$adjective->examples[$i] = $myrow['example'];
			$i++;
		}
	}
	
	return $adjective;
}

function getRanking($code){

	$rankings = array(0,0,0,0,0);
	$code = mysql_escape_string($code);
	$sql="SELECT count( * ) as count , rank FROM `rankings`  WHERE code ='$code' GROUP BY rank ";
	$result = mysql_query($sql);
	$i=2;
	$votes=0;
	while($row = mysql_fetch_array($result)) {
		$rankings[$row['rank']+2] = $row['count'];
		$votes= $votes +  $row['count'];
	}
	if($votes==0)
		return 0;
	else	
	return $rankings;

}

function addAdjective($code,$adjective,$definition ,$examples){
	$code = mysql_escape_string($code);
	$adjective = mysql_escape_string($adjective);
	$definition = mysql_escape_string($definition);
	$examples = explode(',', mysql_escape_string($examples));
	
	$sql = "replace INTO adjectives (code,adjective,definition) VALUES ('$code','$adjective','$definition')";
	$res  = mysql_query($sql )  or die(mysql_error());
	
	foreach ($examples as $example){
		$sql = "insert INTO examples (code,example) VALUES ('$code','$example')";
		$result  = mysql_query($sql )  or die(mysql_error());
	}
	
	if($res)
		return true;
	else 
		return false;
}

function getAdjectivesForVoting($username){
	$username = mysql_escape_string($username);

	$adjective_list=array();
	$sql="SELECT * FROM `adjectives` WHERE code not in ( select code from rankings where username='$username') LIMIT 1000 " ;
	
	$result = mysql_query($sql) or die(mysql_error());
	
	while($row = mysql_fetch_array($result)) {
		$adjective=new Adjective();
		$adjective->code=$row['code'];
		$adjective->adjective=$row['adjective'];
		$adjective->definition=$row['definition'];
		
		$sql="SELECT example FROM examples where code='$adjective->code' ";
		$res = mysql_query($sql);
		$i=0;
		while($myrow = mysql_fetch_array($res))	{
			$adjective->examples[$i] = $myrow['example'];
			$i++;
		}
		
		$sql="SELECT count(*) as count FROM rankings where code='$adjective->code' ";
		$res = mysql_query($sql);
		$myrow = mysql_fetch_array($res);
		$adjective->votes = $myrow['count'];
		
		$adjective_list[]=$adjective;		
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

function addVote($code,$vote,$username){
	$result  = mysql_query("INSERT INTO rankings (code,rank,username) VALUES ('$code','$vote','$username')")  or die(mysql_error());
}
?>
