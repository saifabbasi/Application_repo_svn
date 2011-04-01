<?php
function directoryToArray($directory, $recursive = true) {
  $array_items = array();
  if ($handle = opendir($directory)) {
	while (false !== ($file = readdir($handle))) {
	  if ($file != "." && $file != "..") {
		if (is_dir($directory. "/" . $file)) {
		  if($recursive) {
			$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
		  }
		  $file = $directory . "/" . $file;
		  $array_items[] = preg_replace("/\/\//si", "/", $file);
		} else {
		  $file = $directory . "/" . $file;
		  $array_items[] = preg_replace("/\/\//si", "/", $file);
		}
	  }
	}
	closedir($handle);
  }
  return $array_items;
}

function directoryToChecksumArray($directory, $recursive) {
  $array_items = array();
  if ($handle = opendir($directory)) {
	while (false !== ($file = readdir($handle))) {
	  if ($file != "." && $file != "..") {
		if (is_dir($directory. "/" . $file)) {
		  if($recursive) {
			$array_items = array_merge($array_items, directoryToChecksumArray($directory. "/" . $file, $recursive));
		  }
		} else {
		  $file = $directory . "/" . $file;
		  $rel_file = preg_replace('@.*/\.\./selfhost@', '', preg_replace("/\/\//si", "/", $file));
		  $array_items[$rel_file] = md5(file_get_contents($file));
		}
	  }
	}
	closedir($handle);
  }
  return $array_items;
}