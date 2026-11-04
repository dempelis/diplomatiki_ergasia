<?php
require('./includes/config.php'); 

if(isset($_GET['code'])){
	$values = getRanking($_GET['code']);

// Get the total number of columns we are going to plot

    $columns  = count($values);

// Get the height and width of the final image

    $width = 150;
    $height = 100;
	
	// Generate the image variables

    $im        = imagecreate($width,$height);
    $gray      = imagecolorallocate ($im,0xcc,0xcc,0xcc);
    $gray_lite = imagecolorallocate ($im,0xee,0xee,0xee);
    $gray_dark = imagecolorallocate ($im,0x7f,0x7f,0x7f);
    $white     = imagecolorallocate ($im,0xff,0xff,0xff);
	$black     = imagecolorallocate ($im,0,0,0);
	
	$red     = imagecolorallocate ($im,255,0,0);
	$yellow     = imagecolorallocate ($im,255,255,0);
	$green     = imagecolorallocate ($im,0,255,0);
	$blue     = imagecolorallocate ($im,0,0,255);
	
	$colors = array($red  ,$yellow   , $gray , $green ,$blue);
	
	
	if($columns!=5){
	
		imagefilledrectangle($im,0,0,$width,$height,$white);
		imagestring($im, 5, 0, $height/2, "No Ranking", $black);
		header ("Content-type: image/png");
		imagepng($im);
		exit(0);
	}


// Set the amount of space between each column

    $padding = 5;

// Get the width of 1 column

    $column_width = $width / $columns ;


	
    
// Fill in the background of the image

    imagefilledrectangle($im,0,0,$width,$height,$white);
    
    $maxv = 0;

// Calculate the maximum value we are going to plot

    for($i=0;$i<$columns;$i++)$maxv = max($values[$i],$maxv);

	$j=-2;
// Now plot each column
    $height= $height-20; 	
    for($i=0;$i<$columns;$i++)
    {
        $column_height = ($height / 100) * (( $values[$i] / $maxv) *100);

        $x1 = $i*$column_width;
        $y1 = $height-$column_height;
        $x2 = (($i+1)*$column_width)-$padding;
        $y2 = $height;
		
        imagefilledrectangle($im,$x1,$y1,$x2,$y2,$colors[$i]);


		
		$col_name = $j+$i;
		
		imagestring($im, 5, $x1, $height, $col_name, $black);
		if($column_height>10)
			imagestring($im, 5, $x1, $y1, $values[$i] , $gray_dark);
    }
	
// Send the PNG header information. Replace for JPEG or GIF or whatever

    header ("Content-type: image/png");
    imagepng($im);
}
?>