<?php 	//for rendering movies in the Tutorials section
	//getting "zend plugin not registered" error when trying to make this a method in Publisher.Controller.php

function ShowMovie($vid, $w=false, $h=false) {
	$w = $w ? $w : 960;
	$h = $h ? $h : 576;
	
	if($vid)
		$out = '<object width="'.$w.'" height="'.$h.'"><param name="movie" value="http://www.youtube.com/v/'.$vid.'&amp;hl=en_US&amp;fs=1&amp;" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><embed src="http://www.youtube.com/v/'.$vid.'&amp;hl=en_US&amp;fs=1&amp;" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$w.'" height="'.$h.'"></embed></object>';
	
	return $o = $out ? $out : '';
}//end ShowMovie()
