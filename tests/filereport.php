<?php

$files = array();

function get_directory($base="", $path = '.', $level = 0) {
	global $files;
	$ignore = array('.', '..');
	$dh = @opendir("$base$path");
	while(false !== ($file = readdir($dh))) {
		if(!in_array($file, $ignore)){
			if(is_dir("$base$path/$file")) {
				get_directory($base, "$path/$file", ($level+1) );
			} else {
		$files["$path/$file"] = "";
			}
		}
	}
	closedir($dh);
}

get_directory("../scrivo/", "Scrivo");

$fd = array();
if (($handle = fopen("filereport.csv", "r")) !== false) {
	while (($data = fgetcsv($handle, 1000, ",")) !== false) {
		if (isset($data[2])) {
			$file = $data[2];
			$fd[$file] = $data;
		}
	}
	fclose($handle);
}

$copy = $files;
$header = array();
$header["File"] = array_shift($fd);
$data = array_merge($files, $fd);
ksort($data);
//$data = array_merge($header, $data);

$str = "Documentation,Tests,File,Remarks\n";
foreach ($data as $file=>$cols) {
	$del = (!isset($copy[$file]))?"*: ":"";
	$doc = isset($cols[0]) ? $cols[0] : "  ";
	$tst = isset($cols[1]) ? $cols[1] : "  ";
	$dsc = isset($cols[3]) ? $cols[3] : "";
	$str .= "$del$doc,$tst,$file,$dsc\n";
}

file_put_contents("filereport.csv", $str);

?>