<?php
/*******************************************************
 * Author: Geert Bergman (geert@scrivo.nl)
 * Version: $Id: login_common.php 866 2013-08-25 16:22:35Z geert $
 *
 * Copyright 2002-2010 Bergman IT-Advies
 * All Rights Reserved
 */

if (!defined("CHARSET")) {
	define("CHARSET", "utf-8");
}

define('META_TAGS',
"<meta name=\"author\" content=\"www.scrivo.nl\">\n".
"\t\t<meta name=\"generator\" content=\"Scrivo\">\n".
"\t\t<meta name=\"copyright\" content=\"".html_entity_decode ("&copy;", ENT_COMPAT, CHARSET)." 2002-".date("Y").", www.scrivo.nl\">\n".
"\t\t<meta name=\"description\" content=\"\">\n");

function page_start($title, $activeitem="", $utildir="") {
global $i18n;
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Strict//name_en">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo CHARSET?>">
		<link href="<?php echo $utildir?>support/style.css" rel="stylesheet" type="text/css">
		<meta name="robots" content="index,nofollow">
		<title><?php echo $title ?></title>
		<?php echo META_TAGS ?>
		<script type="text/javascript" src="../js/util.js"></script>
		<script type="text/javascript">

function size() {

	var h1 = window.innerHeight?window.innerHeight:document.documentElement.clientHeight;
	var h2 = document.getElementById("content").offsetHeight+60;
	var h3 = h1 > h2 ? h1 : h2;
	document.getElementById("bg1").style.top = (h3 - 522) + "px";
	document.getElementById("bg2").style.top = (h3 - 522) + "px";

	document.getElementById("cpyright").style.bottom = "";

	var h4 = document.getElementById("cpyright").offsetTop+60;
	if (h4 < h1) {
		document.getElementById("cpyright").style.bottom = 10 +"px";
	}

	var l = document.getElementsByTagName("INPUT");
	for (var i in l) {
		if (l[i].type == "text") {
			l[i].focus();
			break;
		}
	}
}
window.onload = size;
window.onresize = size;

		</script>
	</head>
		<body>
			<div class="menu" id="menu">
				<div class="item<?php echo $activeitem=='login'?' active':''?>">
					<a class="nav" href="<?php echo WWW_ROOT?>/scrivo/index.php"><?php echo $i18n["common.login"]?></a>
				</div>
				<div class="item<?php echo $activeitem=='bugreport'?' active':''?>">
					<a class="nav" href="<?php echo WWW_ROOT?>/scrivo/extra/bugreport.php"><?php echo $i18n["common.bugreport"]?></a>
				</div>
			</div>
			<div id="bg1" class="bg1<?php echo array_rand(array("yellow"=>1,"red"=>1,"orange"=>1,"purple"=>1))?>"></div>
			<div id="bg2"></div>
			<div id="maindiv">
				<div id="content_top" class="content_top"></div>

				<div id="content">
<?php
}

function page_end() {
global $i18n;
?>


				</div>
				<div style="" id="cpyright">Copyright <?php echo html_entity_decode ("&copy;", ENT_COMPAT, CHARSET)?> 2002-<?php echo date("Y")?> <a href="http://www.scrivo.nl/">Scrivo</a>, <?php echo $i18n["common.rights"]?></div>
		  </div>
	</body>
</html>
<?php
}

function replace_links($str, $replace) {
	$src = array("[LINKEND]");
	$rep = array("</a>");
	foreach ($replace as $k=>$v) {
		$src[] = "[LINKSTART.".$k."]";
		$rep[] = "<a href=\"$v\">";
	}
	return str_replace($src, $rep, $str);
}

?>