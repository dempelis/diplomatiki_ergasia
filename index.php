<?php
	/*
	 * For Openshift only :: Redirect to project root
	 * copied from php.net documentation
	 */
	 $host = $_SERVER['HTTP_HOST'];
//	 $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	 $extra = 'project/site/';
	 header("Location: http://$host/$extra");
	 exit;
 ?>
