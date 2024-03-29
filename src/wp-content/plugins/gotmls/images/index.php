<?php
/**
 * GOTMLS Plugin Global Variables and Functions
 * @package GOTMLS
*/

if (!function_exists("GOTMLS_define")) {
function GOTMLS_define($DEF, $val) {
	if (!defined($DEF))
		define($DEF, $val);
}}

$file = basename(__FILE__);
GOTMLS_define("GOTMLS_local_images_path", substr(__FILE__, 0, strlen(__FILE__) - strlen($file)));
GOTMLS_define("GOTMLS_plugin_path", substr(dirname(__FILE__), 0, strlen(dirname(__FILE__)) - strlen(basename(dirname(__FILE__)))));
if (is_file(GOTMLS_plugin_path.$file) && ($contents = @file_get_contents(GOTMLS_plugin_path.$file)) && preg_match('/\nversion:\s*([0-9\.]+)/i', $contents, $match))
	GOTMLS_define("GOTMLS_Version", $match[1]);
else
	GOTMLS_define("GOTMLS_Version", "Unknown");
GOTMLS_define("GOTMLS_require_version", "3.3");
if (isset($wp_version) && ($wp_version))
	GOTMLS_define("GOTMLS_wp_version", $wp_version);
if (!function_exists("__")) {
function __($text, $domain = "gotmls") {
	return $text;
}}

function GOTMLS_htmlentities($TXT, $flags = ENT_COMPAT, $encoding = "UTF-8") {
	$prelen = strlen($TXT);
	if ($prelen == 0)
		return "";
	$encoded = htmlentities($TXT, $flags, $encoding);
	if (strlen($encoded) == 0) {
		$encoding = "ISO-8859-1";
		$encoded = htmlentities($TXT, $flags, $encoding);
	}
	if (strlen($encoded) == 0)
		$encoded = __("Failed to encode HTML entities!",'gotmls');
	$GLOBALS["GOTMLS"]["tmp"]["encoding"] = $encoding;
	return $encoded;
}

function GOTMLS_htmlspecialchars($TXT, $flags = ENT_COMPAT, $encoding = "UTF-8") {
	$prelen = strlen($TXT);
	if ($prelen == 0)
		return "";
	$encoded = htmlspecialchars($TXT, $flags, $encoding);
	if (strlen($encoded) == 0) {
		$encoding = "ISO-8859-1";
		$encoded = htmlspecialchars($TXT, $flags, $encoding);
	}
	if (strlen($encoded) == 0)
		$encoded = __("Failed to encode HTML characters!",'gotmls');
	$GLOBALS["GOTMLS"]["tmp"]["encoding"] = $encoding;
	return $encoded;
}

$bad = array("eval", "preg_replace", "auth_pass");
$GLOBALS["GOTMLS"] = array(
	"MT" => microtime(true), 
	"tmp"=>array("HeadersError"=>"", "onLoad"=>"", "file_contents"=>"", "new_contents"=>"", "threats_found"=>array(), 
		"skip_dirs" => array(".", ".."), "scanfiles" => array(), "nonce"=>array(),
		"mt" => ((isset($_REQUEST["mt"])&&is_numeric($_REQUEST["mt"]))?$_REQUEST["mt"]:microtime(true)), 
		"threat_files" => array("htaccess"=>".htaccess","timthumb"=>"thumb.php"), 
		"threat_levels" => array(__("Database Injections",'gotmls')=>"db_scan",__("htaccess Threats",'gotmls')=>"htaccess",__("TimThumb Exploits",'gotmls')=>"timthumb",__("Known Threats",'gotmls')=>"known",__("Core File Changes",'gotmls')=>"wp_core",__("Potential Threats",'gotmls')=>"potential"), 
		"apache" => array(),
		"skip_ext"=>array("png", "jpg", "jpeg", "gif", "bmp", "tif", "tiff", "psd", "svg", "doc", "docx", "ttf", "fla", "flv", "mov", "mp3", "pdf", "css", "pot", "po", "mo", "so", "exe", "zip", "7z", "gz", "rar"),
		"execution_time" => 60,
		"default" => array("msg_position" => array("80px", "40px", "400px", "600px")),
		"Definition" => array("Default" => "CCIGG"),
		"definitions_array" => array(
			"potential"=>array(
				$bad[0]=>array("CCIGG", "/[^a-z_\\/'\"]".$bad[0]."\\(.+\\)+\\s*;/i"),
				$bad[1]." /e"=>array("CCIGG", "/".$bad[1]."[\\s*\\(]+(['\"])([\\!\\/\\#\\|\\@\\%\\^\\*\\~]).+?\\2[imsx]*e[imsx]*\\1\\s*,[^,]+,[^\\)]+[\\);\\s]+/i"),
				$bad[2]=>array("CCIGG", "/\\\$".$bad[2]."\\s*=.+;/i"),
				"function add_action wp_enqueue_script json2"=>array("CCIGG", "/json2\\.min\\.js/i"),
				"Tagged Code"=>array("CCIGG", "/\\#(\\w+)\\#.+?\\#\\/\\1\\#/is"),
				"protected by copyright"=>array("CCIGG", "/\\/\\* This file is protected by copyright law and provided under license. Reverse engineering of this file is strictly prohibited. \\*\\//i")),
		)
	)
);
if (isset($_SERVER["HTTP_HOST"]))
	$SERVER_HTTP = 'HOST://'.$_SERVER["HTTP_HOST"];
elseif (isset($_SERVER["SERVER_NAME"]))
	$SERVER_HTTP = 'NAME://'.$_SERVER["SERVER_NAME"];
elseif (isset($_SERVER["SERVER_ADDR"]))
	$SERVER_HTTP = 'ADDR://'.$_SERVER["SERVER_ADDR"];
else
	$SERVER_HTTP = "NULL://not.anything.com";
if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"])
	$SERVER_HTTP .= ":".$_SERVER["SERVER_PORT"];
$SERVER_parts = explode(":", $SERVER_HTTP.":");
if ((isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == 1)) || (count($SERVER_parts) > 2 && $SERVER_parts[2] == "443"))
	$GLOBALS["GOTMLS"]["tmp"]["protocol"] = "https:";
else
	$GLOBALS["GOTMLS"]["tmp"]["protocol"] = "http:";
GOTMLS_define("GOTMLS_script_URI", preg_replace('/\&(last_)?mt=[0-9\.]+/i', '', str_replace('&amp;', '&', GOTMLS_htmlspecialchars($_SERVER["REQUEST_URI"], ENT_QUOTES))).'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"]);
GOTMLS_define("GOTMLS_plugin_home", "https://gotmls.net/");

if (!function_exists("GOTMLS_encode")) {
function GOTMLS_encode($unencoded_string) {
	if (function_exists("base64_encode"))
		$encoded_string = base64_encode($unencoded_string);
	elseif (function_exists("mb_convert_encoding"))
		$encoded_string = mb_convert_encoding($unencoded_string, "BASE64", "UTF-8");
	else
		$encoded_string = "Cannot encode: $unencoded_string function_exists: ";
	$encoded_array = explode("=", $encoded_string."=");
	return strtr($encoded_array[0], "+/0", "-_=").(count($encoded_array)-1);
}}

if (!function_exists("GOTMLS_decode")) {
function GOTMLS_decode($encoded_string) {
	$tail = 0;
	if (strlen($encoded_string) > 1 && is_numeric(substr($encoded_string, -1)) && substr($encoded_string, -1) > 0)
		$tail = substr($encoded_string, -1) - 1;
	else
		$encoded_string .= "$tail";
	$encoded_string = strtr(substr($encoded_string, 0, -1), "-_=", "+/0").str_repeat("=", $tail);
	if (function_exists("base64_decode"))
		return base64_decode($encoded_string);
	elseif (function_exists("mb_convert_encoding"))
		return mb_convert_encoding($encoded_string, "UTF-8", "BASE64");
	else
		return "Cannot decode: $encoded_string";
}}

GOTMLS_define("GOTMLS_Failed_to_list_LANGUAGE", __("Failed to list files in directory!",'gotmls'));
GOTMLS_define("GOTMLS_Run_Quick_Scan_LANGUAGE", __("Quick Scan",'gotmls'));
GOTMLS_define("GOTMLS_View_Quarantine_LANGUAGE", __("View Quarantine",'gotmls'));
GOTMLS_define("GOTMLS_View_Scan_Log_LANGUAGE", __("View Scan History",'gotmls'));
GOTMLS_define("GOTMLS_require_version_LANGUAGE", sprintf(__("This Plugin requires WordPress version %s or higher",'gotmls'), GOTMLS_require_version));
GOTMLS_define("GOTMLS_Scan_Settings_LANGUAGE", __("Scan Settings",'gotmls'));
GOTMLS_define("GOTMLS_Loading_LANGUAGE", __("Loading, Please Wait ...",'gotmls'));
GOTMLS_define("GOTMLS_Automatically_Fix_LANGUAGE", __("Automatically Fix SELECTED Files Now",'gotmls'));

if (function_exists("plugins_url"))
	GOTMLS_define("GOTMLS_images_path", plugins_url('/', __FILE__));
elseif (function_exists("plugin_dir_url"))
	GOTMLS_define("GOTMLS_images_path", plugin_dir_url(__FILE__));
elseif (isset($_SERVER["DOCUMENT_ROOT"]) && ($_SERVER["DOCUMENT_ROOT"]) && strlen($_SERVER["DOCUMENT_ROOT"]) < __FILE__ && substr(__FILE__, 0, strlen($_SERVER["DOCUMENT_ROOT"])) == $_SERVER["DOCUMENT_ROOT"])
	GOTMLS_define("GOTMLS_images_path", substr(dirname(__FILE__), strlen($_SERVER["DOCUMENT_ROOT"])));
elseif (isset($_SERVER["SCRIPT_FILENAME"]) && isset($_SERVER["DOCUMENT_ROOT"]) && ($_SERVER["DOCUMENT_ROOT"]) && strlen($_SERVER["DOCUMENT_ROOT"]) < strlen($_SERVER["SCRIPT_FILENAME"]) && substr($_SERVER["SCRIPT_FILENAME"], 0, strlen($_SERVER["DOCUMENT_ROOT"])) == $_SERVER["DOCUMENT_ROOT"])
	GOTMLS_define("GOTMLS_images_path", substr(dirname($_SERVER["SCRIPT_FILENAME"]), strlen($_SERVER["DOCUMENT_ROOT"])));
else
	GOTMLS_define("GOTMLS_images_path", "/wp-content/plugins/update/images/");

function GOTMLS_user_can() {
	if (is_multisite())
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] = "manage_network";
	elseif (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"]) || $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] == "manage_network")
		$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"] = "activate_plugins";
	if (current_user_can($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["user_can"]))
		return true;
	else
		return false;
}

if (!defined("ABSPATH")) {
	define("ABSPATH", dirname(dirname(__FILE__)).'/safe-load/');
	$root_path = dirname(ABSPATH);
	while (strlen($root_path) > 1 && !is_file($root_path."/wp-config.php"))
		$root_path = dirname($root_path);
	if (is_file($root_path."/wp-config.php"))
		include_once($root_path."/wp-config.php");
	else
		die("No wp-config!");
}

function GOTMLS_update_option($index, $value = array()) {
	return update_option('GOTMLS_'.$index.'_blob', GOTMLS_encode(serialize($value)));
}

function GOTMLS_get_option($index, $value = array()) {
	if (is_array($tmp = get_option('GOTMLS_'.$index.'_array', array())) && count($tmp)) {
		GOTMLS_update_option($index, $tmp);
		delete_option('GOTMLS_'.$index.'_array');
	} else
		$tmp = $value;
	return unserialize(GOTMLS_decode(get_option('GOTMLS_'.$index.'_blob', GOTMLS_encode(serialize($tmp)))));
}

$GOTMLS_chmod_file = (0644);
$GOTMLS_chmod_dir = (0755);
$GLOBALS["GOTMLS"]["tmp"]["nonce"] = GOTMLS_get_option('nonce', array());
$GLOBALS["GOTMLS"]["tmp"]["settings_array"] = get_option('GOTMLS_settings_array', array());
$GLOBALS["GOTMLS"]["tmp"]["definitions_array"] = GOTMLS_get_option('definitions', $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]);
GOTMLS_define("GOTMLS_siteurl", get_option("siteurl", $GLOBALS["GOTMLS"]["tmp"]["protocol"].$SERVER_parts[1].((count($SERVER_parts) > 2 && ($SERVER_parts[2] == '80' || $SERVER_parts[2] == '443'))?"":":".$SERVER_parts[2])."/"));
$GLOBALS["GOTMLS"]["log"] = get_option('GOTMLS_scan_log/'.(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"0.0.0.0").'/'.$GLOBALS["GOTMLS"]["tmp"]["mt"], array());
if (!(isset($GLOBALS["GOTMLS"]["log"]["settings"]) && is_array($GLOBALS["GOTMLS"]["log"]["settings"])))
	$GLOBALS["GOTMLS"]["log"]["settings"] = $GLOBALS["GOTMLS"]["tmp"]["settings_array"];
GOTMLS_define("GOTMLS_installation_key", md5(GOTMLS_siteurl));
GOTMLS_define("GOTMLS_update_home", "//updates.gotmls.net/".GOTMLS_installation_key."/");

if (!function_exists("GOTMLS_Invalid_Nonce")) {
function GOTMLS_Invalid_Nonce($pre = "//Error: ") {
	return $pre.__("Invalid or expired Nonce Token!",'gotmls').(isset($_REQUEST["GOTMLS_mt"])?(" (".GOTMLS_htmlspecialchars($_REQUEST["GOTMLS_mt"]).((strlen($_REQUEST["GOTMLS_mt"]) == 32)?(isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]])?$GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]:" !found)"):" !len[".strlen($_REQUEST["GOTMLS_mt"])."])")):" (GOTMLS_mt !set)").__("Refresh and try again?",'gotmls');
}}

if (!function_exists("GOTMLS_set_nonce")) {
function GOTMLS_set_nonce($context = "NULL") {
	$hour = round(($GLOBALS["GOTMLS"]["tmp"]["mt"]/60)/60);
	$transient_name = md5(substr(number_format(microtime(true), 9, '-', '/'), 6).GOTMLS_installation_key.GOTMLS_plugin_path);
	if (isset($GLOBALS["GOTMLS"]["tmp"]["nonce"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["nonce"])) {
		foreach ($GLOBALS["GOTMLS"]["tmp"]["nonce"] as $nonce_key => $nonce_value) {
			if (($nonce_value > $hour) || (($nonce_value + 24) < $hour))
				unset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$nonce_key]);
			elseif ($nonce_value == $hour)
				$transient_name = $nonce_key;
		}
	}
	if (!isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$transient_name])) {
		$GLOBALS["GOTMLS"]["tmp"]["nonce"][$transient_name] = $hour;
		if (!GOTMLS_update_option('nonce', $GLOBALS["GOTMLS"]["tmp"]["nonce"]))
			return ("$context=DB-err:".preg_replace('/[\r\n]+/', " ", GOTMLS_htmlspecialchars(print_r($GLOBALS["GOTMLS"]["tmp"]["nonce"],1).$wpdb->last_error)));
	}
	return 'GOTMLS_mt='.$transient_name;
}}

if (!function_exists("GOTMLS_get_nonce")) {
function GOTMLS_get_nonce() {
	if (isset($_REQUEST["GOTMLS_mt"])) {
		if (is_array($_REQUEST["GOTMLS_mt"])) {
			foreach ($_REQUEST["GOTMLS_mt"] as $_REQUEST_GOTMLS_mt)
				if (strlen($_REQUEST_GOTMLS_mt) == 32 && isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST_GOTMLS_mt]))
					return $GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST_GOTMLS_mt];
			return 0;
		} elseif (strlen($_REQUEST["GOTMLS_mt"]) == 32 && isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]]))
			return $GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]];
		else
			return "";
	} else
		return false;
}}

function GOTMLS_fileperms($file) {
	if ($prm = @fileperms($file)) {
		if (($prm & 0xC000) == 0xC000)
			$ret = "s";
		elseif (($prm & 0xA000) == 0xA000)
			$ret = "l";
		elseif (($prm & 0x8000) == 0x8000)
			$ret = "-";
		elseif (($prm & 0x6000) == 0x6000)
			$ret = "b";
		elseif (($prm & 0x4000) == 0x4000)
			$ret = "d";
		elseif (($prm & 0x2000) == 0x2000)
			$ret = "c";
		elseif (($prm & 0x1000) == 0x1000)
			$ret = "p";
		else
			$ret = "u";
		$ret .= (($prm & 0x0100)?"r":"-").(($prm & 0x0080)?"w":"-");
		$ret .= (($prm & 0x0040)?(($prm & 0x0800)?"s":"x" ):(($prm & 0x0800)?"S":"-"));
		$ret .= (($prm & 0x0020)?"r":"-").(($prm & 0x0010)?"w":"-");
		$ret .= (($prm & 0x0008)?(($prm & 0x0400)?"s":"x" ):(($prm & 0x0400)?"S":"-"));
		$ret .= (($prm & 0x0004)?"r":"-").(($prm & 0x0002)?"w":"-");
		$ret .= (($prm & 0x0001)?(($prm & 0x0200)?"t":"x" ):(($prm & 0x0200)?"T":"-"));
		return $ret;
	} else
		return "stat failed!";
}

function GOTMLS_file_details($file) {
	return '<div id="file_details_'.md5($file).'" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>File Details: '.GOTMLS_htmlspecialchars(basename($file)).'</b><br />in: '.dirname(realpath($file)).'<br />size: '.filesize(realpath($file)).' ( '.ceil(strlen(GOTMLS_htmlspecialchars($GLOBALS["GOTMLS"]["tmp"]["file_contents"]))/1024).' KB )<br />encoding: '.(isset($GLOBALS["GOTMLS"]["tmp"]["encoding"])?$GLOBALS["GOTMLS"]["tmp"]["encoding"]:(function_exists("mb_detect_encoding")?mb_detect_encoding($GLOBALS["GOTMLS"]["tmp"]["file_contents"]):"Unknown")).'<br />permissions: '.GOTMLS_fileperms(realpath($file)).'<br />Owner/Group: '.fileowner(realpath($file)).'/'.filegroup(realpath($file)).' (you are: '.getmyuid().'/'.getmygid().')<br />modified:'.date(" Y-m-d H:i:s ", filemtime(realpath($file))).'<br />changed:'.date(" Y-m-d H:i:s ", filectime(realpath($file))).'</div>';
}

function GOTMLS_admin_url($url = '') {
	if (function_exists("admin_url"))
		return admin_url($url);
	else {
		return "../../../../wp-admin/$url";
	}
}

function GOTMLS_close_button($box_id, $margin = '6px') {
	return '<a href="javascript:void(0);" style="float: right; color: #F00; overflow: hidden; width: 20px; height: 20px; text-decoration: none; margin: '.$margin.'" onclick="showhide(\''.$box_id.'\');"><span class="dashicons dashicons-dismiss"></span>X</a>';
}

function GOTMLS_get_styles($pre_style = '<style>') {
	$head_nonce = GOTMLS_set_nonce(__FUNCTION__."272");
	return $pre_style.'
span.GOTMLS_date {float: right; width: 130px; white-space: nowrap;}
.GOTMLS_page {float: left; border-radius: 10px; padding: 0 5px;}
.GOTMLS_quarantine_item {margin: 4px 12px;}
.rounded-corners {margin: 10px; border-radius: 10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border: 1px solid #000;}
.shadowed-box {box-shadow: -3px 3px 3px #666; -moz-box-shadow: -3px 3px 3px #666; -webkit-box-shadow: -3px 3px 3px #666;}
.sidebar-box {background-color: #CCC;}
iframe {border: 0;}
.GOTMLS-scanlog li a {display: none;}
.GOTMLS-scanlog li:hover a {display: block;}
.GOTMLS-sidebar-links {list-style: none;}
.GOTMLS-sidebar-links li img {margin: 3px; height: 16px; vertical-align: middle;}
.GOTMLS-sidebar-links li {margin-bottom: 0 !important;}
.popup-box {background-color: #FFC; display: none; position: absolute; left: 0px; z-index: 10;}
.shadowed-text {text-shadow: #00F -1px 1px 1px;}
.sub-option {float: left; margin: 3px 5px;}
.inside {margin: 10px; position: relative;}
.GOTMLS_li, .GOTMLS_plugin li {list-style: none;}
.GOTMLS_plugin {margin: 5px; background: #cfc; border: 1px solid #0C0; padding: 0 5px; border-radius: 3px;}
.GOTMLS_plugin.known, .GOTMLS_plugin.db_scan, .GOTMLS_plugin.htaccess, .GOTMLS_plugin.timthumb, .GOTMLS_plugin.errors {background: #f99; border: 1px solid #f00;}
.GOTMLS_plugin.potential, .GOTMLS_plugin.wp_core, .GOTMLS_plugin.skipdirs, .GOTMLS_plugin.skipped {background: #ffc; border: 1px solid #fc6;}
.GOTMLS ul li {margin-left: 12px;}
.GOTMLS h2 {margin: 0 0 10px;}
.postbox {margin-right: 10px; line-height: 1.4; font-size: 13px;}
#pastDonations li {list-style: none;}
#quarantine_buttons {position: absolute; right: 0px; top: -54px; margin: 0px; padding: 0px;}
#quarantine_buttons input.button-primary {margin-right: 20px;}
#reclean_buttons {
	color: #a00;
    min-height: 32px;
    border-top: solid 2px black;
    padding-top: 10px;
}
#reclean_buttons input.button-primary {float: right;}
#delete_button {
	background-color: #C33;
	color: #FFF;
	background-image: linear-gradient(to bottom, #C22, #933);
	border-color: #933 #933 #900;
	box-shadow: 0 1px 0 rgba(230, 120, 120, 0.5) inset;
	text-decoration: none; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
	margin-top: 10px;
}
#main-page-title {
	background: url("https://secure.gravatar.com/avatar/5feb789dd3a292d563fea3b885f786d6?s=64") no-repeat scroll 0 0 transparent;
	height: 64px;
	line-height: 58px;
	margin: 10px 0 0 0;
	max-width: 600px;
	padding: 0 110px 0 84px;
}
#main-page-title h1 {
	background: url("https://secure.gravatar.com/avatar/8151cac22b3fc543d099241fd573d176?s=64") no-repeat scroll top right transparent;
	height: 64px;
	line-height: 32px;
	margin: 0;
	padding: 0 84px 0 0;
	display: table-cell;
    text-align: center;
    vertical-align: middle;
}
</style>
<div id="div_file" class="shadowed-box rounded-corners sidebar-box" style="padding: 0; display: none; position: fixed; top: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1].'; left: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0].'; width: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3].'; height: '.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2].'; border: solid #c00; z-index: 112358;"><table style="width: 100%; height: 100%;" cellspacing="0" cellpadding="0"><tr><td style="border-bottom: 1px solid #EEE; height: 32px;" colspan="2">'.GOTMLS_close_button("div_file").'<h3 onmousedown="grabDiv();" onmouseup="releaseDiv();" id="windowTitle" style="cursor: move; border-bottom: 0px none; z-index: 2345677; position: absolute; left: 0px; top: 0px; margin: 0px; padding: 6px; width: 90%; height: 20px;">'.GOTMLS_Loading_LANGUAGE.'</h3></td></tr><tr><td colspan="2" style="height: 100%"><div style="width: 100%; height: 100%; position: relative; padding: 0; margin: 0;" class="inside"><br /><br /><center><img src="'.GOTMLS_images_path.'wait.gif" height=16 width=16 alt="..."> '.GOTMLS_Loading_LANGUAGE.'<br /><br /><input type="button" onclick="showhide(\'GOTMLS_iFrame\', true);" value="'.__("If this is taking too long, click here.",'gotmls').'" class="button-primary" /></center><iframe id="GOTMLS_iFrame" name="GOTMLS_iFrame" style="top: 0px; left: 0px; position: absolute; width: 100%; height: 100%; background-color: #CCC;"></iframe></td></tr><tr><td style="height: 20px;"><iframe id="GOTMLS_statusFrame" name="GOTMLS_statusFrame" style="width: 100%; height: 20px; background-color: #CCC;"></iframe></div></td><td style="height: 20px; width: 20px;"><h3 id="cornerGrab" onmousedown="grabCorner();" onmouseup="releaseCorner();" style="cursor: move; height: 24px; width: 24px; margin: 0; padding: 0; z-index: 2345678; overflow: hidden; position: absolute; right: 0px; bottom: 0px;"><span class="dashicons dashicons-editor-expand"></span>&#8690;</h3></td></tr></table></div>
<script type="text/javascript">
function showhide(id) {
	divx = document.getElementById(id);
	if (divx) {
		if (divx.style.display == "none" || arguments[1]) {
			divx.style.display = "block";
			divx.parentNode.className = (divx.parentNode.className+"close").replace(/close/gi,"");
			return true;
		} else {
			divx.style.display = "none";
			return false;
		}
	}
}
function checkAllFiles(check) {
	var checkboxes = new Array(); 
	checkboxes = document["GOTMLS_Form_clean"].getElementsByTagName("input");
	for (var i=0; i<checkboxes.length; i++)
		if (checkboxes[i].type == "checkbox" && (checkboxes[i].id.substring(0, 6) == "check_" || checkboxes[i].id.substring(0, 24) == "GOTMLS_quarantine_check_"))
			checkboxes[i].checked = check;
}
function setvalAllFiles(val) {
	var checkboxes = document.getElementById("GOTMLS_fixing");
	if (checkboxes)
		checkboxes.value = val;
}
function getWindowWidth(min) {
	if (typeof window.innerWidth != "undefined" && window.innerWidth > min)
		min = window.innerWidth;
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientWidth != "undefined" && document.documentElement.clientWidth > min)
		min = document.documentElement.clientWidth;
	else if (typeof document.getElementsByTagName("body")[0].clientWidth != "undefined" && document.getElementsByTagName("body")[0].clientWidth > min)
		min = document.getElementsByTagName("body")[0].clientWidth;
	return min;
}
function getWindowHeight(min) {
	if (typeof window.innerHeight != "undefined" && window.innerHeight > min)
		min = window.innerHeight;
	else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientHeight != "undefined" && document.documentElement.clientHeight > min)
		min = document.documentElement.clientHeight;
	else if (typeof document.getElementsByTagName("body")[0].clientHeight != "undefined" && document.getElementsByTagName("body")[0].clientHeight > min)
		min = document.getElementsByTagName("body")[0].clientHeight;
	return min;
}
function loadIframe(title) {
	showhide("GOTMLS_iFrame", true);
	showhide("GOTMLS_iFrame");
	document.getElementById("windowTitle").innerHTML = title;
	if (curDiv) {
		windowW = getWindowWidth(200);
		windowH = getWindowHeight(200);
		if (windowW > 200)
			windowW -= 30;
		if (windowH > 200)
			windowH -= 20;
		if (px2num(curDiv.style.width) > windowW) {
			curDiv.style.width = windowW + "px";
			curDiv.style.left = "0px";
		} else if ((px2num(curDiv.style.left) + px2num(curDiv.style.width)) > windowW) {
			curDiv.style.left = (windowW - px2num(curDiv.style.width)) + "px";
		}
		if (px2num(curDiv.style.height) > windowH) {
			curDiv.style.height = windowH + "px";
			curDiv.style.top = "0px";
		} else if ((px2num(curDiv.style.top) + px2num(curDiv.style.height)) > windowH) {
			curDiv.style.top = (windowH - px2num(curDiv.style.height)) + "px";
		}
		if (px2num(curDiv.style.left) < 0)
			curDiv.style.left = "0px";
		if (px2num(curDiv.style.top)< 0)
			curDiv.style.top = "0px";
	}
	showhide("div_file", true);
	if (IE)
		curDiv.scrollIntoView(true);
}
function cancelserver(divid) {
	document.getElementById(divid).innerHTML = "<div class=\'error\'>'. __("No response from server!",'gotmls').'</div>";
}
function checkupdateserver(server, divid) {
	var updatescript = document.createElement("script");
	updatescript.setAttribute("src", server);
	divx = document.getElementById(divid);
	if (divx) {
		divx.appendChild(updatescript);
		if (arguments[2])
			return setTimeout("stopCheckingDefinitions = checkupdateserver(\'"+arguments[2]+"\',\'"+divid+"\')",15000);
		else
			return setTimeout("cancelserver(\'"+divid+"\')",'.($GLOBALS["GOTMLS"]["tmp"]['execution_time']+1).'000+3000);
	}
}
var IE = document.all?true:false;
//if (!IE)	document.addEventListener("mousemove", getMouseXY);
document.onmousemove = getMouseXY;
var offsetX = 0;
var offsetY = 0;
var offsetW = 0;
var offsetH = 0;
var curX = 0;
var curY = 0;
var curDiv;
function getMouseXY(e) {
	if (IE) { // grab the mouse pos if browser is IE
		curX = event.clientX + document.body.scrollLeft;
		curY = event.clientY + document.body.scrollTop;
	} else {  // grab the mouse pos if browser is Not IE
		curX = e.pageX - document.body.scrollLeft;
		curY = e.pageY - document.body.scrollTop;
	}
	if (curX < 0) {curX = 0;}
	if (curY < 0) {curY = 0;}
	if (offsetX && curX > 10) {curDiv.style.left = (curX - offsetX)+"px";}
	if (offsetY && (curY - offsetY) > 0) {curDiv.style.top = (curY - offsetY)+"px";}
	if (offsetW && (curX - offsetW) > 360) {curDiv.style.width = (curX - offsetW)+"px";}
	if (offsetH && (curY - offsetH) > 200) {curDiv.style.height = (curY - offsetH)+"px";}
	return true;
}
function px2num(px) {
	return parseInt(px.substring(0, px.length - 2), 10);
}
function setDiv(DivID) {
	if (curDiv = document.getElementById(DivID)) {
		if (IE)
			curDiv.style.position = "absolute";
		curDiv.style.left = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][0].'";
		curDiv.style.top = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][1].'";
		curDiv.style.height = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][2].'";
		curDiv.style.width = "'.$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"][3].'";
	}
}
function grabDiv() {
	corner = document.getElementById("windowTitle");
	if (corner) {
		corner.style.width="100%";
		corner.style.height="100%";
	}
	offsetX=curX-px2num(curDiv.style.left); 
	offsetY=curY-px2num(curDiv.style.top);
}
function releaseDiv() {
	corner = document.getElementById("windowTitle");
	if (corner) {
		corner.style.width="90%";
		corner.style.height="20px";
	}
	document.getElementById("GOTMLS_statusFrame").src = "'.GOTMLS_admin_url('admin-ajax.php?action=GOTMLS_position&'.$head_nonce.'&GOTMLS_x=').'"+curDiv.style.left+"&GOTMLS_y="+curDiv.style.top;
	offsetX=0; 
	offsetY=0;
}
function grabCorner() {
	corner = document.getElementById("cornerGrab");
	if (corner) {
		corner.style.width="100%";
		corner.style.height="100%";
	}
	offsetW=curX-px2num(curDiv.style.width); 
	offsetH=curY-px2num(curDiv.style.height);
}
function releaseCorner() {
	corner = document.getElementById("cornerGrab");
	if (corner) {
		corner.style.width="20px";
		corner.style.height="20px";
	}
	document.getElementById("GOTMLS_statusFrame").src = "'.GOTMLS_admin_url('admin-ajax.php?action=GOTMLS_position&'.$head_nonce.'&GOTMLS_w=').'"+curDiv.style.width+"&GOTMLS_h="+curDiv.style.height;
	offsetW=0; 
	offsetH=0;
}
function check_for_donation(chk) {
	if ((audl = document.getElementById("autoUpdateDownload")) && audl.src.replace(/^.+\?/,"")=="0")
		if (chk.substr(0, 8) != "Changed " || chk.substr(8, 1) != "0")
			chk += "\\n\\n'.__("Please make a donation for the use of this wonderful feature!",'gotmls').'";
	alert(chk);
}
setDiv("div_file");
</script>';
}

function GOTMLS_get_header($optional_box = "") {
	if (isset($_GET["check_site"]) && $_GET["check_site"])
		$pre_style = '<div id="check_site" style="z-index: 1234567;"><img src="'.GOTMLS_images_path.'checked.gif" onload="showhide(\'inside_ddd6dbd641b9a5909fe4d44da2017cc7\');" height=16 width=16 alt="&#x2714;"> '.__("Tested your site. It appears we didn't break anything",'gotmls').' ;-)</div><script type="text/javascript">if (csw = window.parent.document.getElementById("check_site_warning")) csw.style.backgroundColor=\'#0C0\';</script><li>Please <a target="_blank" href="https://wordpress.org/support/plugin/gotmls/reviews/#wporg-footer">write a "Five-Star" Review</a> on WordPress.org if you like this plugin.</li><style>#footer, #GOTMLS-metabox-container, #GOTMLS-right-sidebar, #admin-page-container, #wpadminbar, #adminmenuback, #adminmenuwrap, #adminmenu, .error, .updated, .notice, .update-nag {display: none !important;} #wpbody-content {padding-bottom: 0;} #wpbody, html.wp-toolbar {padding-top: 0 !important;} #wpcontent, #footer {margin-left: 5px !important;}';
	else
		$pre_style = '<style>#GOTMLS-right-sidebar {float: right; margin-right: 0px;}';
	return GOTMLS_get_styles($pre_style).'<div id="main-page-title"><h1 style="vertical-align: middle;">Anti-Malware from&nbsp;GOTMLS.NET</h1></div>';
}

function GOTMLS_get_quarantine($only = false) {
	global $wpdb, $post;
	if (is_numeric($only))
		return get_post($only, ARRAY_A);
	elseif ($only)
		return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` = 'private'");
	else
		$args = array('posts_per_page' => (isset($_GET['posts_per_page'])&&is_numeric($_GET['posts_per_page'])&&$_GET['posts_per_page']>0?$_GET['posts_per_page']:200), 'orderby' => 'date', 'post_type' => 'GOTMLS_quarantine', "post_status" => "private");
	if (isset($_POST["paged"]))
		$args["paged"] = $_POST["paged"];
	$my_query = new WP_Query($args);
	$Q_Paged = '<form method="POST" name="GOTMLS_Form_page"><input type="hidden" id="GOTMLS_paged" name="paged" value="1"><div style="float: left;">Page:</div>';
	$Q_Page = '
	<form method="POST" action="'.admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."645")).(isset($_SERVER["QUERY_STRING"])&&strlen($_SERVER["QUERY_STRING"])?"&".$_SERVER["QUERY_STRING"]:"").'" target="GOTMLS_iFrame" name="GOTMLS_Form_clean"><input type="hidden" id="GOTMLS_fixing" name="GOTMLS_fixing" value="1"><input type="hidden" name="action" value="GOTMLS_fix">';
	if ($my_query->have_posts()) {
		$Q_Page .= '<p id="quarantine_buttons" style="display: none;"><input id="repair_button" type="submit" value="'.__("Restore selected files",'gotmls').'" class="button-primary" onclick="if (confirm(\''.__("Are you sure you want to overwrite the previously cleaned files with the selected files in the Quarantine?",'gotmls').'\')) { setvalAllFiles(1); loadIframe(\'File Restoration Results\'); } else return false;" /><input id="delete_button" type="submit" class="button-primary" value="'.__("Delete selected files",'gotmls').'" onclick="if (confirm(\''.__("Are you sure you want to permanently delete the selected files in the Quarantine?",'gotmls').'\')) { setvalAllFiles(2); loadIframe(\'File Deletion Results\'); } else return false;" /></p><p><b>'.__("The following items highlighted in yellow had been found to contain malicious code, they have been cleaned and the malicious contents have been removed. A record of the infection has been saved here in the Quarantine for your review and could help with any future investigations. The code is safe here and you do not need to do anything further with these files.",'gotmls').'</b></p>
		<p id="reclean_buttons" style="display: none;"><input id="reclean_button" type="submit" value="'.__("Re-clean re-infected files",'gotmls').'" class="button-primary" onclick="checkAllFiles(false); setvalAllFiles(1); loadIframe(\'Reinfected File Recleaning Results\');" /><b>'.__("The items highlighted in red have been found to be re-infected. The malicious code has returned and needs to be cleaned again.",'gotmls').'</b></p>
		<ul name="found_Quarantine" id="found_Quarantine" class="GOTMLS_plugin known" style="background-color: #ccc; padding: 0;"><h3 style="margin: 8px 12px;">'.($my_query->post_count>1?'<input type="checkbox" onchange="checkAllFiles(this.checked); document.getElementById(\'quarantine_buttons\').style.display = \'block\';"> '.sprintf(__("Check all %d",'gotmls'),$my_query->post_count):"").__(" Items in Quarantine",'gotmls').'<span class="GOTMLS_date">'.__("Quarantined",'gotmls').'</span><span class="GOTMLS_date">'.__("Date Infected",'gotmls').'</span></h3>';
		$root_path = implode(GOTMLS_slash(), array_slice(GOTMLS_explode_dir(__FILE__), 0, (2 + intval($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"])) * -1));
		while ($my_query->have_posts()) {
			$my_query->the_post();
			$gif = 'blocked.gif';
			$threat = 'potential';
			$action = $post->ID.'" id="check_'.$post->ID.'" onchange="document.getElementById(\'quarantine_buttons\').style.display = \'block\';';
			$link = GOTMLS_error_link(__("The current/live file is missing or deleted",'gotmls'), $post->ID, $threat);
			if (is_file($post->post_title)) {
				GOTMLS_scanfile($post->post_title);
				if (count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
					$gif = 'threat.gif" onload="document.getElementById(\'reclean_buttons\').style.display = \'block\';';
					$threat = 'known';
					$action = GOTMLS_encode(realpath($post->post_title)).'" id="ilist_'.$post->ID.'" checked="true';
				}
				$link = GOTMLS_error_link(__("View current/live version",'gotmls'), $post->post_title, $threat);
			} elseif (is_array($postdb = explode(":", $post->post_title.":")) && count($postdb) > 3 && is_numeric($postdb[1])) {
				if ("options" == substr($postdb[0], -7)) {
					if ($opt_row = $wpdb->get_row("SELECT * FROM `$wpdb->options` WHERE `option_id` = ".$postdb[1], ARRAY_A))
						$link = GOTMLS_error_link(__("View Option Record: ",'gotmls').$postdb[1], $postdb[1].'.1', $threat);
					elseif ($opt_row = $wpdb->get_row($SQL = $wpdb->prepare("SELECT * FROM `$wpdb->options` WHERE `option_name` LIKE %s", trim($postdb[2], '"')), ARRAY_A))
						$link = GOTMLS_error_link(__("View Option Record: ",'gotmls').htmlspecialchars($postdb[2]), $opt_row["option_id"].'.1', $threat);
					else
						$link = GOTMLS_error_link(__("View Quarantine Record",'gotmls'), $post->ID, $threat);
				} else {
					$link = '<a target="_blank" href="';
					if ("revision" == $postdb[0])
						$link .= admin_url('revision.php?revision='.$postdb[1])."\" title=\"View this revision";
					else
						$link .= admin_url('post.php?action=edit&post='.$postdb[1])."\" title=\"View current ".$postdb[0];
					$link .= "\" id=\"list_edit_$postdb[1]\" class=\"GOTMLS_plugin $threat\">";
				}
			}
			$Q_Page .= '
			<li id="GOTMLS_quarantine_'.$post->ID.'" class="GOTMLS_quarantine_item" onmouseover="this.style.fontWeight=\'bold\';" onmouseout="this.style.fontWeight=\'normal\';"><span class="GOTMLS_date">'.GOTMLS_error_link(__("View Quarantine Record",'gotmls'), $post->ID, $threat).$post->post_date_gmt.'</a></span><span class="GOTMLS_date">'.$post->post_modified_gmt.'</span><input type="checkbox" name="GOTMLS_fix[]" value="'.$action.'" /><img src="'.GOTMLS_images_path.$gif.'" height=16 width=16 alt="Q">'.$link.str_replace($root_path, "...", $post->post_title)."</a></li>\n";
		}
		$Q_Page .= "\n</ul>";
		for ($p = 1; $p <= $my_query->max_num_pages; $p++) {
			$Q_Paged .= '<input class="GOTMLS_page" type="submit" value="'.$p.'"'.((isset($_POST["paged"]) && $_POST["paged"] == $p) || (!isset($_POST["paged"]) && 1 == $p)?" DISABLED":"").' onclick="document.getElementById(\'GOTMLS_paged\').value = \''.$p.'\';">';
		}
	} else
		$Q_Page .= '<h3>'.__("No Items in Quarantine",'gotmls').'</h3>';
	wp_reset_query();
	$return = "$Q_Paged\n</form><br style=\"clear: left;\" />\n$Q_Page\n</form>\n$Q_Paged\n</form><br style=\"clear: left;\" />\n";
	if (($trashed = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` != 'private'")) > 1)
		$return = '<a href="'.admin_url('admin-ajax.php?action=GOTMLS_empty_trash&'.GOTMLS_set_nonce(__FUNCTION__."720")).'" id="empty_trash_link" style="float: right;" target="GOTMLS_statusFrame">['.sprintf(__("Clear %s Deleted Files from the Trash",'gotmls'), $trashed)."]</a>$return";
	return $return;
}

function GOTMLS_box($bTitle, $bContents, $bType = "postbox") {
	$md5 = md5($bTitle);
	if (isset($GLOBALS["GOTMLS"]["tmp"]["$bType"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["$bType"]))
		$GLOBALS["GOTMLS"]["tmp"]["$bType"]["$md5"] = "$bTitle";
	else
		$GLOBALS["GOTMLS"]["tmp"]["$bType"] = array("$md5"=>"$bTitle");
	return '
	<div id="box_'.$md5.'" class="'.$bType.'"><h3 title="Click to toggle" onclick="if (typeof '.$bType.'_showhide == \'function\'){'.$bType.'_showhide(\'inside_'.$md5.'\');}else{showhide(\'inside_'.$md5.'\');}" style="cursor: pointer;" class="hndle"><span id="title_'.$md5.'">'.$bTitle.'</span></h3>
		<div id="inside_'.$md5.'" class="inside">
'.$bContents.'
		</div>
	</div>';
}

if (isset($_GET["SESSION"]) && is_numeric($_GET["SESSION"]) && preg_match('|(.*?/gotmls\.js\?SESSION=)|', GOTMLS_script_URI, $match)) {
	header("Content-type: text/javascript");
	if (is_file(GOTMLS_plugin_path."safe-load/session.php"))
		require_once(GOTMLS_plugin_path."safe-load/session.php");
	if (isset($_SESSION["GOTMLS_SESSION_TEST"])) 
		die("/* GOTMLS SESSION PASS */\nif('undefined' != typeof stopCheckingSession && stopCheckingSession)\n\tclearTimeout(stopCheckingSession);\nshowhide('GOTMLS_patch_searching', true);\nif (autoUpdateDownloadGIF = document.getElementById('autoUpdateDownload'))\n\tdonationAmount = autoUpdateDownloadGIF.src.replace(/^.+\?/,'');\nif ((autoUpdateDownloadGIF.src == donationAmount) || donationAmount=='0') {\n\tif (patch_searching_div = document.getElementById('GOTMLS_patch_searching')) {\n\t\tif (autoUpdateDownloadGIF.src == donationAmount)\n\t\t\tpatch_searching_div.innerHTML = '<span style=\"color: #F00;\">".__("You must register and donate to use this feature!",'gotmls')."</span>';\n\t\telse\n\t\t\tpatch_searching_div.innerHTML = '<span style=\"color: #F00;\">".__("This feature is available to those who have donated!",'gotmls')."</span>';\n\t}\n} else {\n\tshowhide('GOTMLS_patch_searching');\n\tshowhide('GOTMLS_patch_button', true);\n}\n");
	else {
		$_SESSION["GOTMLS_SESSION_TEST"] = $_GET["SESSION"] + 1;
		if ($_GET["SESSION"] > 0)
			die("/* GOTMLS SESSION FAIL */\nif('undefined' != typeof stopCheckingSession && stopCheckingSession)\n\tclearTimeout(stopCheckingSession);\ndocument.getElementById('GOTMLS_patch_searching').innerHTML = '<div class=\"error\">".__("Your Server could not start a Session!",'gotmls')."</div>';");
		else
			die("/* GOTMLS SESSION TEST */\nif('undefined' != typeof stopCheckingSession && stopCheckingSession)\n\tclearTimeout(stopCheckingSession);\nstopCheckingSession = checkupdateserver('".$match[0].$_SESSION["GOTMLS_SESSION_TEST"]."', 'GOTMLS_patch_searching');");
	}
} elseif ((isset($_SERVER["DOCUMENT_ROOT"]) && ($SCRIPT_FILE = str_replace($_SERVER["DOCUMENT_ROOT"], "", isset($_SERVER["SCRIPT_FILENAME"])?$_SERVER["SCRIPT_FILENAME"]:isset($_SERVER["SCRIPT_NAME"])?$_SERVER["SCRIPT_NAME"]:"")) && strlen($SCRIPT_FILE) > strlen("/".basename(__FILE__)) && substr(__FILE__, -1 * strlen($SCRIPT_FILE)) == substr($SCRIPT_FILE, -1 * strlen(__FILE__)))) {
	if (isset($_GET["page"]) && str_replace('-', '_', $_GET["page"]) == "GOTMLS_View_Quarantine" && isset($_REQUEST["GOTMLS_mt"]) && strlen($_REQUEST["GOTMLS_mt"]) == 32 && isset($GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]])) {
$return = (print_r( array("nonce"=>$GLOBALS["GOTMLS"]["tmp"]["nonce"][$_REQUEST["GOTMLS_mt"]],"mt"=>($_REQUEST["GOTMLS_mt"])),1));
		try {
			$Q_Paged = '<form method="POST" name="GOTMLS_Form_page"><input type="hidden" id="GOTMLS_paged" name="paged" value="1">';//<div style="float: left;">Page:</div>';
			$Q_Page = '<form method="POST" action="?'.(isset($_SERVER["QUERY_STRING"])&&strlen($_SERVER["QUERY_STRING"])?$_SERVER["QUERY_STRING"]:"page=GOTMLS_View_Quarantine&".GOTMLS_set_nonce(__FUNCTION__."592")).'" name="GOTMLS_Form_clean">';
			if (isset($_REQUEST["id"]) && is_numeric($_REQUEST["id"])) {
				$my_query = $wpdb->get_results("SELECT * FROM `{$table_prefix}posts` WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` = 'private' AND `ID` = ".$_REQUEST["id"], ARRAY_A);
				if (is_array($my_query) && count($my_query) && ($Q_post = $my_query[0]) && isset($Q_post["post_type"]) && $Q_post["post_type"] == "GOTMLS_quarantine" && isset($Q_post["post_status"]) && $Q_post["post_status"] == "private") {
					$clean_file = $Q_post["post_title"];
					$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = GOTMLS_decode($Q_post["post_content"]);
					$fa = "";
					if (isset($Q_post["post_excerpt"]) && strlen($Q_post["post_excerpt"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"] = @unserialize(GOTMLS_decode($Q_post["post_excerpt"])))) {
						$f = 1;
						foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found => $threats_name) {
							list($start, $end, $junk) = explode("-", "$threats_found--", 3);
							if (strlen($end) > 0 && is_numeric($start) && is_numeric($end)) {
								if ($start < $end)
									$fa .= ' <a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$start.', '.$end.');">['.$f++.']</a>';
								else
									$fa .= ' <a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$end.', '.$start.');">['.$f++.']</a>';
							} else {
								if (is_numeric($threats_found)) {
									$threats_found = $threats_name;
									$threats_name = $f;
								}
								$fpos = 0;
								$flen = 0;
								$potential_threat = str_replace("\r", "", $threats_found);
								while (($fpos = strpos(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"]), ($potential_threat), $flen + $fpos)) !== false) {
									$flen = strlen($potential_threat);
									$fa .= ' <a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.($fpos).', '.($fpos + $flen).');">['.$f++.']</a>';
								}
							}
						}
					}
					die("\n".'<script type="text/javascript">
function select_text_range(ta_id, start, end) {
	var textBox = document.getElementById(ta_id);
	var scrolledText = "";
	scrolledText = textBox.value.substring(0, end);
	textBox.focus();
	if (textBox.setSelectionRange) {
		scrolledText = textBox.value.substring(end);
		textBox.value = textBox.value.substring(0, end);
		textBox.scrollTop = textBox.scrollHeight;
		textBox.value = textBox.value + scrolledText;
		textBox.setSelectionRange(start, end);
	} else if (textBox.createTextRange) {
		var range = textBox.createTextRange();
		range.collapse(true);
		range.moveStart("character", start);
		range.moveEnd("character", end);
		range.select();
	} else
		alert("The highlighting function does not work in your browser");
}
</script><table style="top: 0px; left: 0px; width: 100%; height: 100%; position: absolute;"><tr><td style="width: 100%"><form style="margin: 0;" method="post" action="?'.GOTMLS_set_nonce(__FUNCTION__."643").'&page=GOTMLS_View_Quarantine" onsubmit="return confirm(\''.__("Are you sure you want to restore this file from the quarantine?",'gotmls').'\');"><input type="hidden" name="id[]" value="'.$Q_post["ID"].'"><input type="submit" value="RESTORE from Quarantine" style="display: none; background-color: #0C0; float: right;"></form><div id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>File Details</b><br />encoding: '.(function_exists("mb_detect_encoding")?mb_detect_encoding($GLOBALS["GOTMLS"]["tmp"]["file_contents"]):"Unknown").'<br />size: '.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).' bytes<br />infected:'.$Q_post["post_modified_gmt"].'<br />quarantined:'.$Q_post["post_date_gmt"].'</div><div style="overflow: auto;"><span onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';">'.__("File Details:",'gotmls').'</span> ('.$fa.' )</div></td></tr><tr><td style="height: 100%"><textarea id="ta_file" style="width: 100%; height: 100%">'.GOTMLS_htmlentities(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"])).'</textarea></td></tr></table>');
				} else
					die('<h3>Item NOT Found in Quarantine</h3>');
			} else {
				$my_query = $wpdb->get_results("SELECT * FROM `{$table_prefix}posts` WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` = 'private' ORDER BY `post_date_gmt` DESC", ARRAY_A);
				if (is_array($my_query) && count($my_query)) {
					$Q_Page .= '<p id="quarantine_buttons" style="display: none;"><input id="repair_button" type="submit" value="Restore selected files" class="button-primary" style="background-color: #0C0;" onclick="return confirm(\'Are you sure you want to overwrite the previously cleaned files with the selected files in the Quarantine?\');" /></p><p><b>The following items have been found to contain malicious code, they have been cleaned, and the original infected file contents have been saved here in the Quarantine. The code is safe here and you do not need to do anything further with these files.</b></p>
					<ul name="found_Quarantine" id="found_Quarantine" class="GOTMLS_plugin known" style="background-color: #ccc; padding: 0;"><h3 style="margin: 8px 12px;">'.(count($my_query)>1?'<input type="checkbox" onchange="checkAllFiles(this.checked); document.getElementById(\'quarantine_buttons\').style.display = \'block\';"> '.sprintf(__("Check all %d",'gotmls'),count($my_query)):"").__(" Items in Quarantine",'gotmls').'<span class="GOTMLS_date">'.__("Quarantined",'gotmls').'</span><span class="GOTMLS_date">'.__("Date Infected",'gotmls').'</span></h3>';
					$root_path = implode(GOTMLS_slash(), array_slice(GOTMLS_explode_dir(__FILE__), 0, (2 + intval($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_level"])) * -1));
					foreach ($my_query as $post_a) {
						$restored = "";
						$image = "blocked";
						if (isset($_REQUEST["id"]) && is_array($_REQUEST["id"]) && in_array($post_a["ID"], $_REQUEST["id"])) {
							$restored = " read-only disabled";
							if (GOTMLS_file_put_contents($post_a["post_title"], GOTMLS_decode($post_a["post_content"]))) {
								$post_a["post_modified_gmt"] = date("Y-m-d H:i:s");
								$image = "checked";
								$wpdb->query("UPDATE `{$table_prefix}posts` SET `post_status` = 'pending' WHERE `post_type` = 'GOTMLS_quarantine' AND `post_status` = 'private' AND `ID` = ".$post_a["ID"]);
							}
						}
						$Q_Page .= '
						<li id="GOTMLS_quarantine_'.$post_a["ID"].'" class="GOTMLS_quarantine_item"><span class="GOTMLS_date">'.$post_a["post_date_gmt"].'</span><span class="GOTMLS_date">'.$post_a["post_modified_gmt"].'</span><input'.$restored.' type="checkbox" name="id[]" value="'.$post_a["ID"].'" id="GOTMLS_quarantine_check_'.$post_a["ID"].'" onchange="document.getElementById(\'quarantine_buttons\').style.display = \'block\';" /><img src="'.$image.'.gif" height=16 width=16 alt="Q"><a class="GOTMLS_plugin '.$restored.$post_a["ping_status"].'" target="_blank" href="?page=GOTMLS_View_Quarantine&id='.$post_a["ID"].'&'.GOTMLS_set_nonce(__FUNCTION__."191").'" title="View Quarantined File">'.str_replace($root_path, "...", $post_a["post_title"])."</a></li>\n";
					}
					$Q_Page .= "\n</ul>";
					for ($p = 1; $p <= 0; $p++) {
						$Q_Paged .= '<input class="GOTMLS_page" type="submit" value="'.$p.'"'.((isset($_POST["paged"]) && $_POST["paged"] == $p) || (!isset($_POST["paged"]) && 1 == $p)?" DISABLED":"").' onclick="document.getElementById(\'GOTMLS_paged\').value = \''.$p.'\';">';
					}
				} else
					$Q_Page .= '<h3>'.__("No Items in Quarantine",'gotmls').'</h3>';
				$return = "$Q_Paged\n</form><br style=\"clear: left;\" />\n$Q_Page\n</form>\n$Q_Paged\n</form><br style=\"clear: left;\" />\n";
				die(GOTMLS_html_tags(array("html" => array("body" => GOTMLS_get_header().GOTMLS_box(__("View Quarantine",'gotmls'), "$return")))));
			}
		} catch (Exception $e) {
			die('Caught exception: '. $e->getMessage(). "\n");
		}
	} else {
		header("Content-type: image/gif");
		$img_src = GOTMLS_local_images_path.'GOTMLS-16x16.gif';
		if (!(file_exists($img_src) && $img_bin = @file_get_contents($img_src)))
			$img_bin = GOTMLS_decode('R=lGODlhEAAQAIABAAAAAP___yH5BAEAAAEALAAAAAAQABAAAAIshB=Qm-eo2HuJNWdrjlFm3S2hKB7kViKaxZmr98YgSo_jzH6tiU=974MADwUAOw2');
		die($img_bin);
	}
} elseif (isset($_GET["no_error_reporting"]))
	@error_reporting(0);

$GOTMLS_image_alt = array("wait"=>"...", "checked"=>"&#x2714;", "blocked"=>"X", "question"=>"?", "threat"=>"!");
$GOTMLS_dir_at_depth = array();
$GOTMLS_dirs_at_depth = array();
$GLOBAL_STRING = array("REQUEST" => "&","SERVER" => "&","FILES" => "&");
if (isset($_REQUEST) && is_array($_REQUEST))
	foreach ($_REQUEST as $req => $val)
		$GLOBAL_STRING["REQUEST"] .= "$req=".(is_array($val)?print_r($val,1):$val)."&";
if (isset($_SERVER) && is_array($_SERVER))
	foreach ($_SERVER as $req => $val)
		$GLOBAL_STRING["SERVER"] .= "$req=".(is_array($val)?print_r($val,1):$val)."&";
if (isset($_FILES) && is_array($_FILES))
	foreach ($_FILES as $req => $fila)
		foreach (array("tmp_name","name") as $val)
			if (isset($fila["$val"]))
				$GLOBAL_STRING["FILES"] .= "$req.$val=".(is_array($fila["$val"])?print_r($fila["$val"],1):$fila["$val"])."&";
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]) && array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"])))
	$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"] = array(
		"RevSlider"=>array("CCIGG", "Revolution Slider Exploit Protection", "This protection is automatically activated because of the widespread attacks on WordPress that have affected so many sites. It is still recommended that you make sure to upgrade any older versions of the Revolution Slider plugin, especially those included in themes that will not update automatically. Even if you don't think you have Revolution Slider on your site it doen't hurt to have this protection enabled.", "SERVER", '/\/admin-ajax\.php/i', "REQUEST", '/\&img=[^\&]*(?<!\.'.implode(')(?<!\.', array_slice($GLOBALS["GOTMLS"]["tmp"]["skip_ext"], 0, 10)).')\&/i'),
		"Traversal"=>array("CCIGG", "Directory Traversal Protection", "This protection is automatically activated because this type of attack is quite common. This protection can prevent hackers from accessing secure files in parent directories (or user's folders outside the site_root).", "REQUEST", '/=[\s\/]*\.\.\//'),
		"UploadPHP"=>array("CCIGG", "Upload PHP File Protection", "This protection is automatically activated because this type of attack is extremely dangerous. This protection can prevent hackers from uploading malicious code via web scripts.", "FILES", '/name=[^\&]*\.php\&/'));
foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"] as $TP => $VA) {
	$V = 3;
	if (is_array($VA) && count($VA) > $V && is_array($VA[$V])) {
		foreach ($VA[$V] as $reg => $arr) {
			$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V++] = $arr;
			$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V++] = $reg;
		}
	}
	if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["firewall"]["$TP"]) && $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["firewall"]["$TP"])) {
		$GLOBALS["GOTMLS"]["detected_attacks"] = "&attack[]=FW_$TP";
		for ($V = 4; isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V]); $V+=2)
			if (!isset($GLOBAL_STRING[$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V-1]]))
				die($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V-1]." [$V] not in <pre>".GOTMLS_htmlspecialchars(print_r($GLOBAL_STRING,1))."</pre>");
			elseif (!preg_match($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V], $GLOBAL_STRING[$GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["firewall"]["$TP"][$V-1]], $matches))
				$GLOBALS["GOTMLS"]["detected_attacks"] = "";
		if ($GLOBALS["GOTMLS"]["detected_attacks"])
			include(dirname(dirname(__FILE__))."/safe-load/index.php");
	}
}
$GLOBALS["GOTMLS"]["detected_attacks"] = "";
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"]) && count($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"]) == 4))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["msg_position"] = $GLOBALS["GOTMLS"]["tmp"]["default"]["msg_position"];
if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"]))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_what"] = 2;
if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"]))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"] = -1;
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"])))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_ext"] = $GLOBALS["GOTMLS"]["tmp"]["skip_ext"];
if (!isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"]))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["check_custom"] = "";
if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"])))
	$GLOBALS["GOTMLS"]["tmp"]["settings_array"]["exclude_dir"] = array();
$GOTMLS_total_percent = 0;

function GOTMLS_admin_notices() {
    if (!is_admin())
		return;
   	elseif (is_file(dirname(dirname(dirname(__FILE__)))."/yuzo-related-post/yuzo_related_post.php"))
		echo '<div class="error">It looks like you have <b>"Related Post" plugin By <i>Lenin Zapata</i></b> installed on your site.<br />This plugin was removed from the WordPress Plugin Repository because it contained a major vulnerability that was responsible for a fairly widespread breach to many WordPress sites that had it installed.<br />It is recommended that it be deactivated and deleted until a fix is released that solves this problem.</div>';
   	elseif ($GLOBALS["GOTMLS"]["tmp"]["HeadersError"])
		echo $GLOBALS["GOTMLS"]["tmp"]["HeadersError"];
}
add_action("admin_notices", "GOTMLS_admin_notices");

function GOTMLS_array_recurse($array1, $array2) {
	foreach ($array2 as $key => $value) {
		if (!isset($array1[$key]) || (isset($array1[$key]) && !is_array($array1[$key])))
			$array1[$key] = array();
		if (is_array($value))
			$value = GOTMLS_array_recurse($array1[$key], $value);
		$array1[$key] = $value;
	}
	return $array1;
}

function GOTMLS_array_replace($array1, $array2) {
	foreach ($array2 as $key => $value)
		$array1[$key] = $value;
	return $array1;
}

function GOTMLS_array_replace_recursive($array1 = array()) {
	$args = func_get_args();
	$array1 = $args[0];
	if (!is_array($array1))
		$array1 = array();
	for ($i = 1; $i < count($args); $i++)
		if (is_array($args[$i]))
			$array1 = GOTMLS_array_recurse($array1, $args[$i]);
	return $array1;
}

function GOTMLS_update_scan_log($scan_log) {
	if (is_array($scan_log)) {
		$GLOBALS["GOTMLS"]["log"] = GOTMLS_array_replace_recursive($GLOBALS["GOTMLS"]["log"], $scan_log);
		if (isset($GLOBALS["GOTMLS"]["log"]["scan"]["percent"]) && is_numeric($GLOBALS["GOTMLS"]["log"]["scan"]["percent"]) && ($GLOBALS["GOTMLS"]["log"]["scan"]["percent"] >= 100))
			$GLOBALS["GOTMLS"]["log"]["scan"]["finish"] = time();
		if (isset($GLOBALS["GOTMLS"]["log"]["scan"]))
			update_option("GOTMLS_scan_log/".(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"0.0.0.0")."/".$GLOBALS["GOTMLS"]["tmp"]["mt"], $GLOBALS["GOTMLS"]["log"]);
	}
}

function GOTMLS_loaded() {
	if (headers_sent($filename, $linenum)) {
		if (!$filename)
			$filename = __("an unknown file",'gotmls');
		if (!is_numeric($linenum))
			$linenum = __("unknown",'gotmls');
		$GLOBALS["GOTMLS"]["tmp"]["HeadersError"] = '<div class="error">'.sprintf(__('<b>Headers already sent</b> in %1$s on line %2$s.<br />This is not a good sign, it may just be a poorly written plugin but Headers should not have been sent at this point.<br />Check the code in the above mentioned file to fix this problem.','gotmls'), $filename, $linenum).'</div>';
	} elseif (isset($_GET["SESSION"]) && !session_id()) {
		@session_start();
		if (session_id() && $_GET["SESSION"] == "GOTMLS_debug" && !isset($_SESSION["GOTMLS_debug"]))
			$_SESSION["GOTMLS_debug"]=array();
	}
}
add_action("plugins_loaded", "GOTMLS_loaded");

if (!function_exists("add_action")) {
	GOTMLS_loaded();
//	GOTMLS_admin_notices();
}

function GOTMLS_get_ext($filename) {
	$nameparts = explode(".", ".$filename");
	return strtolower($nameparts[(count($nameparts)-1)]);
}

function GOTMLS_preg_match_all($threat_definition, $threat_name) {
	if (@preg_match_all($threat_definition, $GLOBALS["GOTMLS"]["tmp"]["file_contents"], $threats_found)) {
		$start = -1;
		if (!@preg_match_all($threat_definition, $GLOBALS["GOTMLS"]["tmp"]["new_contents"], $threat_found)) {
			$new_contents = $GLOBALS["GOTMLS"]["tmp"]["new_contents"];
			$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $GLOBALS["GOTMLS"]["tmp"]["file_contents"];
		} else
			$new_contents = false;
		foreach ($threats_found[0] as $find) {
			$potential_threat = str_replace("\r", "", $find);
			$flen = strlen($potential_threat);
			while (($start = strpos(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"]), $potential_threat, $start+1)) !== false)
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"]["$start-".($flen+$start)] = "$threat_name";
			$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = str_replace($find, "", $GLOBALS["GOTMLS"]["tmp"]["new_contents"]);
		}
		if ($new_contents && strlen($new_contents) < strlen($GLOBALS["GOTMLS"]["tmp"]["new_contents"]))
			$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $new_contents;
		return count($GLOBALS["GOTMLS"]["tmp"]["threats_found"]);
	} else 
		return false;
}

function GOTMLS_check_threat($check_threats, $file='UNKNOWN') {
	$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
	$GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"] = microtime(true);
	if (is_array($check_threats)) {
		$path = str_replace("//", "/", "/".str_replace("\\", "/", substr($file, strlen(ABSPATH))));
		if (substr($file, 0, strlen(ABSPATH)) == ABSPATH && isset($check_threats[GOTMLS_wp_version]["$path"])) {
			if (($check_threats[GOTMLS_wp_version]["$path"] != md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"])."O".strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"])) && ($source = GOTMLS_get_URL("http://core.svn.wordpress.org/tags/".GOTMLS_wp_version."$path")) && ($check_threats[GOTMLS_wp_version]["$path"] == md5($source)."O".strlen($source))) {
				$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $source;
				$len = strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
				if (strlen($source) < $len)
					$len = strlen($source);
				for ($start = 0, $end = 0; ($start == 0 || $end == 0) && $len > 0; $len--){
					if ($start == 0 && substr($source, 0, $len) == substr($GLOBALS["GOTMLS"]["tmp"]["file_contents"], 0, $len))
						$start = $len;
					if ($end == 0 && substr($source, -1 * $len) == substr($GLOBALS["GOTMLS"]["tmp"]["file_contents"], -1 * $len))
						$end = $len;
				}
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"]["$start-".(strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"])-$end)] = "Core File Modified";
			}
		} else {
			foreach ($check_threats as $threat_name=>$threat_definitions) {
				$GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"] = microtime(true);
				if (is_array($threat_definitions) && count($threat_definitions) > 1 && strlen(array_shift($threat_definitions)) == 5 && (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]) && in_array($threat_name, $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["dont_check"]))))
					while ($threat_definition = array_shift($threat_definitions))
						GOTMLS_preg_match_all($threat_definition, $threat_name);
				if (isset($_SESSION["GOTMLS_debug"])) {
					$_SESSION["GOTMLS_debug"]["threat_name"] = $threat_name;
					$file_time = round(microtime(true) - $GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"], 5);
					if (isset($_GET["GOTMLS_debug"]) && is_numeric($_GET["GOTMLS_debug"]) && $file_time > $_GET["GOTMLS_debug"])
						echo "\n//GOTMLS_debug $file_time $threat_name $file\n";
					if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["total"]))
						$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["total"] += $file_time;
					else
						$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["total"] = $file_time;
					if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["count"]))
						$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["count"] ++;
					else
						$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["count"] = 1;
					if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["least"]) || $file_time < $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["least"])
						$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["least"] = $file_time;
					if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["most"]) || $file_time > $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["most"])
						$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_name"]]["most"] = $file_time;
				}
			}
		}
	} elseif (strlen($check_threats) && isset($_GET['eli']) && substr($check_threats, 0, 1) == '/')
		GOTMLS_preg_match_all($check_threats, $check_threats);
	if (isset($_SESSION["GOTMLS_debug"])) {
		$file_time = round(microtime(true) - $GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"], 5);
		if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["total"]))
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["total"] += $file_time;
		else
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["total"] = $file_time;
		if (isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["count"]))
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["count"] ++;
		else
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["count"] = 1;
		if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["least"]) || $file_time < $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["least"])
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["least"] = $file_time;
		if (!isset($_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["most"]) || $file_time > $_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["most"])
			$_SESSION["GOTMLS_debug"][$_SESSION["GOTMLS_debug"]["threat_level"]]["most"] = $file_time;
	}
	return count($GLOBALS["GOTMLS"]["tmp"]["threats_found"]);
}

function GOTMLS_scanfile($file) {
	global $wpdb, $GOTMLS_chmod_file, $GOTMLS_chmod_dir;
	$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="Scanning...";
	$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
	$gt = ">";
	$lt = "<";
	$found = false;
	$threat_link = "";
	$className = "scanned";
	$real_file = realpath($file);
	$clean_file = GOTMLS_encode($real_file);
	if (is_file($real_file) && ($filesize = filesize($real_file)) && ($GLOBALS["GOTMLS"]["tmp"]["file_contents"] = @file_get_contents($real_file))) {
		if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]))
			$whitelist = array_flip($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]);
		else
			$whitelist = array();
		if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"])) {
			foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["whitelist"] as $whitelist_file=>$non_threats) {
				if (is_array($non_threats) && count($non_threats) > 1) {
					if (isset($non_threats[0]))
						unset($non_threats[0]);
					$whitelist = array_merge($whitelist, $non_threats);
				}
			}
		}
		if (isset($whitelist[md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'O'.$filesize]))
			return GOTMLS_return_threat($className, "checked.gif?$className", $file, $threat_link);
		$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $GLOBALS["GOTMLS"]["tmp"]["file_contents"];
		if (isset($GLOBALS["GOTMLS"]["log"]["settings"]["check_custom"]) && strlen($GLOBALS["GOTMLS"]["log"]["settings"]["check_custom"]) && isset($_GET['eli']) && substr($GLOBALS["GOTMLS"]["log"]["settings"]["check_custom"], 0, 1) == '/' && ($found = GOTMLS_check_threat($GLOBALS["GOTMLS"]["log"]["settings"]["check_custom"])))
			$className = "known";
		else {
			$path = str_replace("//", "/", "/".str_replace("\\", "/", substr($file, strlen(ABSPATH))));
			if (isset($_SESSION["GOTMLS_debug"])) {
				$_SESSION["GOTMLS_debug"]["file"] = $file;
				$_SESSION["GOTMLS_debug"]["last"]["total"] = microtime(true);
			}
			if (isset($GLOBALS["GOTMLS"]["tmp"]["threat_levels"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threat_levels"])) {
				foreach ($GLOBALS["GOTMLS"]["tmp"]["threat_levels"] as $threat_level) {
					if ("db_scan" != $threat_level) {
						if (isset($_SESSION["GOTMLS_debug"])) {
							$_SESSION["GOTMLS_debug"]["threat_level"] = $threat_level;
							$_SESSION["GOTMLS_debug"]["last"]["threat_level"] = microtime(true);
						}
						if (in_array($threat_level, $GLOBALS["GOTMLS"]["log"]["settings"]["check"]) && !$found && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"][$threat_level]) && ($threat_level != "wp_core" || (substr($file, 0, strlen(ABSPATH)) == ABSPATH && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"]))) && (!array_key_exists($threat_level, $GLOBALS["GOTMLS"]["tmp"]["threat_files"]) || (substr($file."e", (-1 * strlen($GLOBALS["GOTMLS"]["tmp"]["threat_files"][$threat_level]."e"))) == $GLOBALS["GOTMLS"]["tmp"]["threat_files"][$threat_level]."e")) && ($found = GOTMLS_check_threat($GLOBALS["GOTMLS"]["tmp"]["definitions_array"][$threat_level],$file)))
							$className = $threat_level;
					}
				}
			}
			if (isset($_SESSION["GOTMLS_debug"])) {
				$file_time = round(microtime(true) - $_SESSION["GOTMLS_debug"]["last"]["total"], 5);
				if (isset($_SESSION["GOTMLS_debug"]["total"]["total"]))
					$_SESSION["GOTMLS_debug"]["total"]["total"] += $file_time;
				else
					$_SESSION["GOTMLS_debug"]["total"]["total"] = $file_time;
				if (isset($_SESSION["GOTMLS_debug"]["total"]["count"]))
					$_SESSION["GOTMLS_debug"]["total"]["count"] ++;
				else
					$_SESSION["GOTMLS_debug"]["total"]["count"] = 1;
				if (!isset($_SESSION["GOTMLS_debug"]["total"]["least"]) || $file_time < $_SESSION["GOTMLS_debug"]["total"]["least"])
					$_SESSION["GOTMLS_debug"]["total"]["least"] = $file_time;
				if (!isset($_SESSION["GOTMLS_debug"]["total"]["most"]) || $file_time > $_SESSION["GOTMLS_debug"]["total"]["most"])
					$_SESSION["GOTMLS_debug"]["total"]["most"] = $file_time;
			}
		}
	} else {
		$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = (is_file($real_file)?(is_readable($real_file)?(filesize($real_file)?__("Failed to read file contents!",'gotmls'):__("Empty file!",'gotmls')):(isset($_GET["eli"])?(@chmod($real_file, $GOTMLS_chmod_file)?__("Fixed file permissions! (try again)",'gotmls'):__("File permissions read-only!",'gotmls')):__("File not readable!",'gotmls'))):__("File does not exist!",'gotmls'));
//		$threat_link = GOTMLS_error_link($GLOBALS["GOTMLS"]["tmp"]["file_contents"], $real_file);
		$className = "errors";
	}
	if (count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
		$threat_link = $lt.'a target="GOTMLS_iFrame" href="'.admin_url('admin-ajax.php?action=GOTMLS_scan&'.GOTMLS_set_nonce(__FUNCTION__."687").'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"].'&GOTMLS_scan='.$clean_file.preg_replace('/\&(GOTMLS_scan|mt|GOTMLS_mt|action)=/', '&last_\1=', isset($_SERVER["QUERY_STRING"])&&strlen($_SERVER["QUERY_STRING"])?"&".$_SERVER["QUERY_STRING"]:"")).'" id="list_'.$clean_file.'" onclick="loadIframe(\''.str_replace("\"", "&quot;", $lt.'div style="float: left; white-space: nowrap;"'.$gt.__("Examine File",'gotmls').' ... '.$lt.'/div'.$gt.$lt.'div style="overflow: hidden; position: relative; height: 20px;"'.$gt.$lt.'div style="position: absolute; right: 0px; text-align: right; width: 9000px;"'.$gt.GOTMLS_htmlspecialchars(GOTMLS_strip4java($file), ENT_NOQUOTES)).$lt.'/div'.$gt.$lt.'/div'.$gt.'\');" class="GOTMLS_plugin"'.$gt;
		if ($className == "errors") {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="errors";
			$threat_link = GOTMLS_error_link($GLOBALS["GOTMLS"]["tmp"]["file_contents"], $file);
			$imageFile = "/blocked";
		} elseif ($className != "potential") {
			if (isset($_POST["GOTMLS_fix"]) && is_array($_POST["GOTMLS_fix"]) && in_array($clean_file, $_POST["GOTMLS_fix"])) {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="GOTMLS_fix";
				if (GOTMLS_get_nonce()) {
					if ($className == "timthumb") {
						if (($source = GOTMLS_get_URL("https://storage.googleapis.com/google-code-archive-downloads/v2/code.google.com/timthumb/timthumb.php")) && strlen($source) > 500)
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $source;
						else
							$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = "";
					} elseif ($className == 'wp_core') {
						$path = str_replace("//", "/", "/".str_replace("\\", "/", substr($file, strlen(ABSPATH))));
						if (substr($file, 0, strlen(ABSPATH)) == ABSPATH && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"]) && ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"] != md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"])."O".strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"])) && ($source = GOTMLS_get_URL("http://core.svn.wordpress.org/tags/".GOTMLS_wp_version."$path")) && ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["wp_core"][GOTMLS_wp_version]["$path"] == md5($source)."O".strlen($source)))
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = $source;
						else
							$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = "";
					} else {
						$GOTMLS_no_contents = trim(preg_replace('/\/\*.*?\*\/\s*/s', "", $GLOBALS["GOTMLS"]["tmp"]["new_contents"]));
						$GOTMLS_no_contents = trim(preg_replace('/\n\s*\/\/.*/', "", $GOTMLS_no_contents));
						$GOTMLS_no_contents = trim(preg_replace('/'.$lt.'\?(php)?\s*(\?'.$gt.'|$)/is', "", $GOTMLS_no_contents));
						if (strlen($GOTMLS_no_contents))
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = trim(preg_replace('/'.$lt.'\?(php)?\s*(\?'.$gt.'|$)/is', "", $GLOBALS["GOTMLS"]["tmp"]["new_contents"]));
						else
							$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = "";
					}
					if (strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]) > 0 && (($Q_post = GOTMLS_write_quarantine($file, $className)) !== false) && ((strlen($GLOBALS["GOTMLS"]["tmp"]["new_contents"])==0 && isset($_GET["eli"]) && ($_GET["eli"] == "delete") && @unlink($file)) || (($Write_File = GOTMLS_file_put_contents($file, $GLOBALS["GOTMLS"]["tmp"]["new_contents"])) !== false))) {
						echo __("Success!",'gotmls');
						return "/*--{$gt}*"."/\nfixedFile('$clean_file');\n/*{$lt}!--*"."/";
					} else {
						echo __("Failed:",'gotmls').' '.(strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"])?((is_writable(dirname($file)) && is_writable($file))?(($Q_post===false)?__("failed to quarantine!",'gotmls')." (".$wpdb->last_error.")":((isset($Write_File)&&$Write_File)?"Q=$Q_post: ".__("reason unknown!",'gotmls'):"Q=$Q_post: ".__("failed to write!",'gotmls'))):__("file not writable!",'gotmls')):__("no file contents!",'gotmls'));
						if (isset($_GET["eli"]))
							echo 'uid='.getmyuid().'('.get_current_user().'),gid='.getmygid().($lt.'br'.$gt.$lt.'pre'.$gt.'file_stat'.print_r(stat($file), true));
						return "/*--{$gt}*"."/\nfailedFile('$clean_file');\n/*{$lt}!--*"."/";
					}
				} else {
					echo GOTMLS_Invalid_Nonce(__("Failed: ",'gotmls'));
					return "/*--{$gt}*"."/\nfailedFile('$clean_file');\n/*{$lt}!--*"."/";
				}
			}
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]=isset($_POST["GOTMLS_fix"])?"GOTMLS_fix=".GOTMLS_htmlspecialchars(print_r($_POST["GOTMLS_fix"],1)):"!potential";
			$threat_link = $lt.'input type="checkbox" name="GOTMLS_fix[]" value="'.$clean_file.'" id="check_'.$clean_file.(($className != "wp_core||ifitis")?'" checked="'.$className:'').'" /'.$gt.$threat_link;
			$imageFile = "threat";
		} elseif (isset($_POST["GOTMLS_fix"]) && is_array($_POST["GOTMLS_fix"]) && in_array($clean_file, $_POST["GOTMLS_fix"])) {
			echo __("Already Fixed!",'gotmls');
			return "/*-->*"."/\nfixedFile('$clean_file');\n/*<!--*"."/";
		} else
			$imageFile = "question";
		return GOTMLS_return_threat($className, $imageFile, $file, str_replace("GOTMLS_plugin", "GOTMLS_plugin $className", $threat_link));
	} elseif (isset($_POST["GOTMLS_fix"]) && is_array($_POST["GOTMLS_fix"]) && in_array($clean_file, $_POST["GOTMLS_fix"])) {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="Already Fixed";
		echo __("Already Fixed!",'gotmls');
		return "/*--{$gt}*"."/\nfixedFile('$clean_file');\n/*{$lt}!--*"."/";
	} else {
$GLOBALS["GOTMLS"]["tmp"]["debug_fix"]="no threat";
		return GOTMLS_return_threat($className, ($className=="scanned"?"checked":"blocked").".gif?$className", $file, $threat_link);
	}
}

function GOTMLS_db_scan($id = 0) {
	global $wpdb;
	if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"]) && count($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"])) {
		if ($id) {
			$encoded_id = GOTMLS_encode($id);
			$ids = explode(".", $id.'.');
			if (count($ids) > 2 && 'tbl'.$ids[1] == 'tbl1' && is_numeric($ids[0]) && ($Q_post = $wpdb->get_row("SELECT * FROM `$wpdb->options` WHERE `option_id` = ".$ids[0], ARRAY_A))) {
				$path = 'Option ID: '.$Q_post["option_id"];
				$clean_file = $Q_post["option_name"];
				$fa = "";
				$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = $Q_post["option_value"];
				$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = ($Q_post["option_value"]);
				$found = 0;
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
				foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"] as $scan_sql => $scan_regex) {
					$GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"] = microtime(true);
					$threat_name = array_shift($scan_regex);
					while ($threat_definition = array_shift($scan_regex))
						$found += GOTMLS_preg_match_all($threat_definition, $threat_name);
				}
				if (isset($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
					$f = 1;
					foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found => $threats_name) {
						list($start, $end, $junk) = explode("-", "$threats_found--", 3);
						if ($start > $end)
							$fa .= 'ERROR['.($f++).']: Threat_size{'.$threats_found.'} Content_size{'.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'}';
						else
							$fa .= ' <a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$start.', '.$end.');">['.$f++.']</a>';
					}
				} else
					$fa = " No Threats Found";
				if (isset($_REQUEST["GOTMLS_fix"]) && is_array($_REQUEST["GOTMLS_fix"]) && in_array($encoded_id, $_REQUEST["GOTMLS_fix"]) && isset($_REQUEST["GOTMLS_fixing"]) && $_REQUEST["GOTMLS_fixing"] > 0) {
					GOTMLS_write_quarantine($Q_post, "db_scan");
					if ($_REQUEST["GOTMLS_fixing"] > 1) {
						echo "<li>Removing $path ... ";
						if ($wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_id` = ".$Q_post["option_id"])) {
							echo __("Done!",'gotmls');
							$li_js .= "/*-->*"."/\nDeletedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Failed to delete!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scan_log(array("scan" => array("finish" => time(), "type" => "Removal of Option")));
					} else {
						echo "<li>Fixing $path ... ";
						if ($wpdb->update($wpdb->options, array("option_value" => $GLOBALS["GOTMLS"]["tmp"]["new_contents"]), array('option_id' => $Q_post["option_id"]))) {
							echo __("Success!",'gotmls');
							$li_js .= "/*-->*"."/\nfixedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Update Failed!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scan_log(array("scan" => array("finish" => time(), "type" => "Removal from Option")));
					}
					return $li_js;
				} else {
					return admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."853")).'" onsubmit="return confirm(\''.__("Are you sure you want to delete this option?",'gotmls').'\');"><input type="hidden" name="GOTMLS_fixing" value="2"><input type="hidden" name="action" value="GOTMLS_fix"><input type="submit" value="Delete this Option" style="float: right;"><input type="hidden" name="GOTMLS_fix[]" value="'.$encoded_id.'"></form><div id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>Record Details</b><br />encoding: '.(function_exists("mb_detect_encoding")?mb_detect_encoding($GLOBALS["GOTMLS"]["tmp"]["file_contents"]):"Unknown").'<br />size: '.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).' bytes</div><div style="overflow: auto;"><span onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';">'.__("Record Details:",'gotmls').'</span> ('.$fa.' )</div></td></tr><tr><td style="height: 100%"><textarea id="ta_file" style="width: 100%; height: 100%">'.GOTMLS_htmlentities(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"])).'</textarea></td></tr></table>';
				}
			} elseif (($Q_post = GOTMLS_get_quarantine($ids[0])) && isset($Q_post["post_content"])) {
				$path = $Q_post["post_type"].' ID: '.$Q_post["ID"];
				$clean_file = $Q_post["post_title"];
				$fa = "";
				$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = $Q_post["post_content"];
				$GLOBALS["GOTMLS"]["tmp"]["new_contents"] = ($Q_post["post_content"]);
				$found = 0;
				$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
				foreach ($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"] as $scan_sql => $scan_regex) {
					$GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"] = microtime(true);
					$threat_name = array_shift($scan_regex);
					while ($threat_definition = array_shift($scan_regex))
						$found += GOTMLS_preg_match_all($threat_definition, $threat_name);
				}
				if (isset($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && count($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
					$f = 1;
					foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $threats_found => $threats_name) {
						list($start, $end, $junk) = explode("-", "$threats_found--", 3);
						if ($start > $end)
							$fa .= 'ERROR['.($f++).']: Threat_size{'.$threats_found.'} Content_size{'.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).'}';
						else
							$fa .= ' <a title="'.GOTMLS_htmlspecialchars($threats_name).'" href="javascript:select_text_range(\'ta_file\', '.$start.', '.$end.');">['.$f++.']</a>';
					}
				} else
					$fa = " No Threats Found";
				if (isset($_REQUEST["GOTMLS_fix"]) && is_array($_REQUEST["GOTMLS_fix"]) && in_array($encoded_id, $_REQUEST["GOTMLS_fix"]) && isset($_REQUEST["GOTMLS_fixing"]) && $_REQUEST["GOTMLS_fixing"] > 0) {
					if ($_REQUEST["GOTMLS_fixing"] > 1) {
						echo "<li>Removing $path ... ";
						$Q_post["post_status"] = "trash";
						if (wp_update_post($Q_post)) {
							echo __("Done!",'gotmls');
							$li_js .= "/*-->*"."/\nDeletedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Failed to delete!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scan_log(array("scan" => array("finish" => time(), "type" => "Removal of Revision")));
					} else {
						echo "<li>Fixing $path ... ";
						GOTMLS_write_quarantine($Q_post, "db_scan");
						$Q_post["post_content"] = $GLOBALS["GOTMLS"]["tmp"]["new_contents"];
						if (wp_update_post($Q_post)) {
							echo __("Success!",'gotmls');
							$li_js .= "/*-->*"."/\nfixedFile('$encoded_id');\n/*<!--*"."/";
						} else {
							echo __("Update Failed!",'gotmls');
							$li_js .= "/*-->*"."/\nfailedFile('$encoded_id');\n/*<!--*"."/";
						}
						GOTMLS_update_scan_log(array("scan" => array("finish" => time(), "type" => "Removal from Content")));
					}
					return $li_js;
				} else {
					return admin_url('admin-ajax.php?'.GOTMLS_set_nonce(__FUNCTION__."905")).($Q_post["post_type"]=="revision"?'" onsubmit="return confirm(\''.__("Are you sure you want to delete this revision?",'gotmls').'\');"><input type="hidden" name="GOTMLS_fixing" value="2"><input type="hidden" name="action" value="GOTMLS_fix"><input type="submit" value="Delete this revision" style="float: right;"><input type="hidden" name="GOTMLS_fix[]" value="'.$encoded_id:"").'"></form><div id="fileperms" class="shadowed-box rounded-corners" style="display: none; position: absolute; left: 8px; top: 29px; background-color: #ccc; border: medium solid #C00; box-shadow: -3px 3px 3px #666; border-radius: 10px; padding: 10px;"><b>Record Details</b><br />encoding: '.(function_exists("mb_detect_encoding")?mb_detect_encoding($GLOBALS["GOTMLS"]["tmp"]["file_contents"]):"Unknown").'<br />size: '.strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]).' bytes<br />last_modified:'.$Q_post["post_modified_gmt"].'<br />post_type:'.$Q_post["post_type"].'<br />author:'.$Q_post["post_author"].'<br />status:'.$Q_post["post_status"].'</div><div style="overflow: auto;"><span onmouseover="document.getElementById(\'fileperms\').style.display=\'block\';" onmouseout="document.getElementById(\'fileperms\').style.display=\'none\';">'.__("Record Details:",'gotmls').'</span> ('.$fa.' )</div></td></tr><tr><td style="height: 100%"><textarea id="ta_file" style="width: 100%; height: 100%">'.GOTMLS_htmlentities(str_replace("\r", "", $GLOBALS["GOTMLS"]["tmp"]["file_contents"])).'</textarea></td></tr></table>';
				}
			} else
				die(GOTMLS_html_tags(array("html" => array("body" => __("This record no longer exists.",'gotmls')."<br />\n<script type=\"text/javascript\">\nwindow.parent.showhide('GOTMLS_iFrame', true);\n</script>"))));
		} else {
			$threats_found = array();
			$li_js = "";
			if (!isset($_REQUEST["eli"]))
				$and = " AND `post_status` != 'trash'";
			if (isset($_REQUEST["limit"]) && is_numeric($_REQUEST["limit"]))
				$and = " LIMIT ".$_REQUEST["limit"];
			if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"])) {
				if (isset($_GET["GOTMLS_scan"]) && strlen($_GET["GOTMLS_scan"]) > 8 && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][substr($_GET["GOTMLS_scan"], 8)])) {
					$scan_replace = str_replace("db_scan", "Database for ", GOTMLS_htmlspecialchars($_GET["GOTMLS_scan"]));
					$db_scan_a = array(substr($_GET["GOTMLS_scan"], 8) => $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][substr($_GET["GOTMLS_scan"], 8)]);
				} elseif (isset($_GET["GOTMLS_only_file"]) && strlen($_GET["GOTMLS_only_file"]) && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][GOTMLS_decode($_GET["GOTMLS_only_file"])])) {
					$scan_replace = str_replace("db_scan", "Database only ".(isset($_GET["limit"]) && is_numeric($_GET["limit"])) ? $_GET["limit"] : ""." for ", GOTMLS_htmlspecialchars("db_scan".GOTMLS_decode($_GET["GOTMLS_only_file"])));
					$_GET["GOTMLS_scan"] = "db_scan=".GOTMLS_decode($_GET["GOTMLS_only_file"]);
					$db_scan_a = array(GOTMLS_decode($_GET["GOTMLS_only_file"]) => $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"][GOTMLS_decode($_GET["GOTMLS_only_file"])]);
				} else {
					$scan_replace = str_replace("db_scan", "Database", GOTMLS_htmlspecialchars($_GET["GOTMLS_scan"]));
					$db_scan_a = $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["db_scan"];
				}
				echo "/*<!--*"."/".GOTMLS_update_status(sprintf(__("Scanning %s",'gotmls'), $scan_replace));
				GOTMLS_flush();
				$li_js .= "/*<!--*"."/".GOTMLS_return_threat("dir", "checked", $_GET["GOTMLS_scan"]).GOTMLS_update_status(sprintf(__("Scanned %s",'gotmls'), $scan_replace));
			} else {
				echo "/*<!--*"."/".GOTMLS_update_status(sprintf(__("No Definitions for DB Injections!",'gotmls')));
				GOTMLS_flush();
				$li_js .= GOTMLS_return_threat("error", "question", $_GET["GOTMLS_scan"]);
				$db_scan_a = $_GET["GOTMLS_scan"];
			}
			if (isset($db_scan_a) && is_array($db_scan_a)) {
				echo "\n//memory_limit=".@ini_get("memory_limit")."\n";
				foreach ($db_scan_a as $scan_sql => $scan_regex) {
					$SQL = preg_replace('/\{[a-f0-9]{64}\}/', '%', $wpdb->prepare("SELECT * FROM `$wpdb->posts` WHERE `post_content` LIKE %s $and", $scan_sql));
					$threat_name = array_shift($scan_regex);
					if (($found_row = $wpdb->get_results($SQL, ARRAY_A)) && is_array($found_row) && count($found_row)) {
						$val = count($found_row);
						if (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
							echo GOTMLS_return_threat("db_scan", "question", (print_r(array("scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("$val Rows", 0));//debug
						foreach ($found_row as $frow) {
							$encoded_id = GOTMLS_encode($frow["ID"].'.0');
							$found = 0;
							if ($frow["post_type"] != "revision" || isset($_REQUEST["eli"])) {
								$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = $frow["post_content"];
								$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
								$GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"] = microtime(true);
								foreach ($scan_regex as $threat_definition)
									$found += GOTMLS_preg_match_all($threat_definition, $threat_name);
								if ($found && !isset($threats_found['row_id_'.$encoded_id])) {
									echo str_replace($frow["ID"].'</a>', '</a><a target="_blank" title="Open '.$frow["post_type"].'" href="'.admin_url(($frow["post_type"]=="revision")?'revision.php?revision='.$frow["ID"].'">View Revision: ':'post.php?action=edit&post='.$frow["ID"].'">Edit '.$frow["post_type"].': ').$frow["ID"].'</a>', GOTMLS_return_threat("db_scan", "threat", "$found $threat_name(\"".str_replace('%', '*', trim($scan_sql, "%")).'") in '.$frow["post_type"]."(".(($frow["post_status"]=='inherit')?$frow["post_parent"]:$frow["post_status"]).'):"'.GOTMLS_htmlspecialchars($frow["post_title"]).'":'.$frow["ID"], '<input type="checkbox" name="GOTMLS_fix[]" id="check_'.$encoded_id.'" value="'.$encoded_id.'" checked="true">'.GOTMLS_error_link(__("View DB Injection",'gotmls'), $frow["ID"].'.0', "db_scan")));
									$threats_found['row_id_'.$encoded_id] = $threat_name;
								} elseif (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
									echo GOTMLS_return_threat("db_scan", "question", (print_r(array("post_id"=>$frow["ID"], "scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("No preg_match", 0));//debug
							}
						}
					}
					if (($found_row = $wpdb->get_results(preg_replace('/\{[a-f0-9]{64}\}/', '%', $wpdb->prepare("SELECT * FROM `$wpdb->options` WHERE `option_value` LIKE %s", $scan_sql)), ARRAY_A)) && is_array($found_row) && count($found_row)) {
						$val = count($found_row);
						if (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
							echo GOTMLS_return_threat("db_scan", "question", (print_r(array("scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("$val Rows", 0));//debug
						foreach ($found_row as $frow) {
							$encoded_id = GOTMLS_encode($frow["option_id"].'.1');
							$found = 0;
							$opt_val = maybe_unserialize($frow["option_value"]);
							if (is_array($opt_val)) {
								$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
								$GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"] = microtime(true);
								foreach ($scan_regex as $threat_definition)
									foreach ($opt_val as $GLOBALS["GOTMLS"]["tmp"]["file_contents"])
										$found += GOTMLS_preg_match_all($threat_definition, $threat_name);
								if ($found && !isset($threats_found['row_id_'.$encoded_id])) {
									echo GOTMLS_return_threat("db_scan", "threat", "$found $threat_name(\"".str_replace('%', '*', trim($scan_sql, "%")).'") in '."$wpdb->options:".GOTMLS_htmlspecialchars($frow["option_name"]).'":'.$frow["option_id"].'.1', '<input type="checkbox" name="GOTMLS_fix[]" id="check_'.$encoded_id.'" value="'.$encoded_id.'" checked="true">'.GOTMLS_error_link(__("View DB Injection",'gotmls'), $frow["option_id"].'.1', "db_scan"));
									$threats_found['row_id_'.$encoded_id] = $threat_name;
								} elseif (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
									echo GOTMLS_return_threat("db_scan", "question", (print_r(array("post_id"=>$frow["ID"], "scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("No preg_match", 0));//debug
							} else {
								$GLOBALS["GOTMLS"]["tmp"]["file_contents"] = $opt_val;
								$GLOBALS["GOTMLS"]["tmp"]["threats_found"] = array();
								$GLOBALS["GOTMLS"]["log"]["scan"]["last_threat"] = microtime(true);
								foreach ($scan_regex as $threat_definition)
									$found += GOTMLS_preg_match_all($threat_definition, $threat_name);
								if ($found && !isset($threats_found['row_id_'.$encoded_id])) {
									echo GOTMLS_return_threat("db_scan", "threat", "$found $threat_name(\"".str_replace('%', '*', trim($scan_sql, "%")).'") in '."$wpdb->options:".GOTMLS_htmlspecialchars($frow["option_name"]).'":'.$frow["option_id"].'.1', '<input type="checkbox" name="GOTMLS_fix[]" id="check_'.$encoded_id.'" value="'.$encoded_id.'" checked="true">'.GOTMLS_error_link(__("View DB Injection",'gotmls'), $frow["option_id"].'.1', "db_scan"));
									$threats_found['row_id_'.$encoded_id] = $threat_name;
								} elseif (isset($_REQUEST["eli"]) && ($_REQUEST["eli"] == "debug"))
									echo GOTMLS_return_threat("db_scan", "question", (print_r(array("post_id"=>$frow["ID"], "scan_regex:"=>$scan_regex,"SQL:"=>$SQL),1)), GOTMLS_error_link("No preg_match", 0));//debug
							}
						}
					}
				}
			}
			return 	"$li_js/*-->*"."/\nscanNextDir(-1);\n/*<!--*"."/";
		}
	}
}

function GOTMLS_remove_dots($dir) {
	if ($dir != "." && $dir != "..")
		return $dir;
}

function GOTMLS_getfiles($dir) {
	$files = false;
	if (is_dir($dir)) {
		if (function_exists("scandir"))
			$files = @scandir($dir);
		if (is_array($files))
			$files = array_filter($files, "GOTMLS_remove_dots");
		elseif ($handle = @opendir($dir)) {
			$files = array();
			while (false !== ($entry = readdir($handle)))
				if ($entry != "." && $entry != "..")
					$files[] = "$entry";
			closedir($handle);
		} else
			$files = GOTMLS_read_error($dir);
	}
	return $files;
}

function GOTMLS_decodeBase64($encoded_string) {
	if (function_exists("base64_decode"))
		$unencoded_string = base64_decode($encoded_string);
	elseif (function_exists("mb_convert_encoding"))
		$unencoded_string = mb_convert_encoding($encoded_string, "UTF-8", "BASE64");
	else
		return "Cannot decode: '$encoded_string'";
	return "'".str_replace("'", "\\'", str_replace("\\", "\\\\", $unencoded_string))."'";
}

function GOTMLS_decodeHex($encoded_string) {
	if (strtolower(substr($encoded_string, 0, 2)) == "\\x")
		$dec_string = hexdec($encoded_string);
	else
		$dec_string = octdec($encoded_string);
	return chr($dec_string);
}

function GOTMLS_return_threat($className, $imageFile, $fileName, $link = "") {
	global $GOTMLS_image_alt;
	$fileNameJS = GOTMLS_strip4java(str_replace("db_scan", "Database", str_replace("db_scan=", "Database Query ", isset($GLOBALS["GOTMLS"]["log"]["scan"]["dir"])?str_replace(dirname($GLOBALS["GOTMLS"]["log"]["scan"]["dir"]), "...", $fileName):$fileName)));
	$fileName64 = GOTMLS_encode($fileName);
	$li_js = "/*-->*"."/";
	if ($className != "scanned")
		$li_js .= "\n$className++;\ndivx=document.getElementById('found_$className');\nif (divx) {\n\tvar newli = document.createElement('li');\n\tnewli.innerHTML='<img src=\"".GOTMLS_strip4java(GOTMLS_images_path.$imageFile).".gif\" height=16 width=16 alt=\"".$GOTMLS_image_alt[$imageFile]."\" style=\"float: left;\" id=\"$imageFile"."_$fileName64\">".GOTMLS_strip4java($link, true).$fileNameJS.($link?"</a>';\n\tdivx.display='block":"")."';\n\tdivx.appendChild(newli);\n}";
	if ($className == "errors")
		$li_js .= "\ndivx=document.getElementById('wait_$fileName64');\nif (divx) {\n\tdivx.src='".GOTMLS_images_path."blocked.gif';\n\tdirerrors++;\n}";
	elseif (is_file($fileName))
	 	$li_js .= "\nscanned++;\n";
	if ($className == "dir")
		$li_js .= "\ndivx=document.getElementById('wait_$fileName64');\nif (divx)\n\tdivx.src='".GOTMLS_images_path."checked.gif';";
	return $li_js."\n/*<!--*"."/";
}

function GOTMLS_slash($dir = __FILE__) {
	if (substr($dir.'  ', 1, 1) == ':' || substr($dir.'  ', 0, 1) == "\\")
		return "\\";
	else
		return  '/';
}

function GOTMLS_trailingslashit($dir = "") {
	if (substr(' '.$dir, -1) != GOTMLS_slash($dir))
		$dir .= GOTMLS_slash($dir);
	return $dir;
}

function GOTMLS_explode_dir($dir, $pre = '') {
	if (strlen($pre))
		$dir = GOTMLS_slash($dir).$pre.$dir;
	return explode(GOTMLS_slash($dir), $dir);
}

function GOTMLS_html_tags($tags, $inner = array()) {
	$html = "";
	$gt = ">";
	if (!is_array($tags))
		return $html;
	foreach ($tags as $tag => $contents) {
		$html .= ($tag=="html"?"<!DOCTYPE html$gt":"")."<$tag".(isset($inner[$tag])?" ".$inner[$tag]:"").$gt;
		if (is_array($contents))
			$html .= GOTMLS_html_tags($contents, $inner);
		else
			$html .= $contents;
		$html .= "</$tag$gt";
	}
	return $html;
}

function GOTMLS_write_quarantine($file, $className) {
	global $wpdb;
	$insert = array("post_author"=>GOTMLS_get_current_user_id(), "post_content"=>GOTMLS_encode($GLOBALS["GOTMLS"]["tmp"]["file_contents"]), "post_mime_type"=>md5($GLOBALS["GOTMLS"]["tmp"]["file_contents"]), "ping_status"=>$className, "post_status"=>"private", "post_type"=>"GOTMLS_quarantine", "post_content_filtered"=>GOTMLS_encode($GLOBALS["GOTMLS"]["tmp"]["new_contents"]), "guid"=>GOTMLS_Version);//! comment_status post_password post_name to_ping post_parent menu_order";
	if (isset($file["ID"]) && is_numeric($file["ID"])) {
		$insert["post_modified"] = $file["post_modified"];
		$insert["post_modified_gmt"] = $file["post_modified_gmt"];
		$insert["comment_count"] = strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
		$file = $file["post_type"].':'.$file["ID"].':"'.$file["post_title"].'"';
	} elseif (isset($file["option_id"]) && is_numeric($file["option_id"])) {
		$insert["post_modified"] = date("Y-m-d H:i:s");
		$insert["post_modified_gmt"] = date("Y-m-d H:i:s");
		$insert["comment_count"] = strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
		$file = $wpdb->options.':'.$file["option_id"].':"'.$file["option_name"].'"';
	}
	$insert["post_title"] = $file;
	$insert["post_date"] = date("Y-m-d H:i:s");
	$insert["post_date_gmt"] = $insert["post_date"];
	if (is_file($file)) {
		if (@filemtime($file))
			$insert["post_modified"] = date("Y-m-d H:i:s", @filemtime($file));
		else
			$insert["post_modified"] = $insert["post_date"];
		if (@filectime($file))
			$insert["post_modified_gmt"] = date("Y-m-d H:i:s", @filectime($file));
		else
			$insert["post_modified_gmt"] = $insert["post_date"];
		if (!($insert["comment_count"] = @filesize($file)))
			$insert["comment_count"] = strlen($GLOBALS["GOTMLS"]["tmp"]["file_contents"]);
	}
	if (isset($GLOBALS["GOTMLS"]["tmp"]["threats_found"]) && is_array($GLOBALS["GOTMLS"]["tmp"]["threats_found"])) {
		$insert["post_excerpt"] = GOTMLS_encode(@serialize($GLOBALS["GOTMLS"]["tmp"]["threats_found"]));
		$pinged = array();
		foreach ($GLOBALS["GOTMLS"]["tmp"]["threats_found"] as $loc => $threat_name) {
			if (isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][0]) && isset($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][1]) && strlen($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][0]) == 5 && strlen($GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][1]))
				$ping = $GLOBALS["GOTMLS"]["tmp"]["definitions_array"]["$className"]["$threat_name"][1];
			else
				$ping = $threat_name;
			if (isset($pinged[$ping]))
				$pinged[$ping]++;
			else
				$pinged[$ping] = 1;
		}
		$insert["pinged"] = GOTMLS_encode(@serialize($pinged));
	}
	if ($return = $wpdb->insert($wpdb->posts, $insert))
		return $return;
	else
		die(print_r(array('return'=>($return===false)?"FALSE":$return, 'last_error'=>$wpdb->last_error, 'insert'=>$insert),1));
}

function GOTMLS_get_current_user_id() {
	$return = 1;
	if (($current_user = @wp_get_current_user()) && (@$current_user->ID > 1))
		$return = $current_user->ID;
	return $return;
}

function GOTMLS_update_status($status, $percent = -1) {
	if (!(isset($GLOBALS["GOTMLS"]["log"]["scan"]["start"]) && is_numeric($GLOBALS["GOTMLS"]["log"]["scan"]["start"])))
		$GLOBALS["GOTMLS"]["log"]["scan"]["start"] = time();
	$microtime = ceil(time()-$GLOBALS["GOTMLS"]["log"]["scan"]["start"]);
	GOTMLS_update_scan_log(array("scan" => array("microtime" => $microtime, "percent" => $percent)));
	return "/*-->*"."/\nupdate_status('".GOTMLS_strip4java($status)."', $microtime, $percent);\n/*<!--*"."/";
}

function GOTMLS_flush($tag = "") {
	$output = "";
	if (($output = @ob_get_contents()) && strlen(trim($output)) > 18) {
		@ob_clean();
		if (!(isset($_GET["eli"]) && $_GET["eli"] == "debug"))
			$output = preg_replace('/\/\*<\!--\*\/.*?\/\*-->\*\//s', "", "$output/*-->*"."/");
		echo "$output\n//flushed(".strlen(trim($output)).")\n";
		if ($tag)
			echo "\n</$tag>\n";
		if (@ob_get_length())
			@ob_flush();
		if ($tag)
			echo "<$tag>\n";
		echo "/*<!--*"."/";
	}
}

function GOTMLS_readdir($dir, $current_depth = 1) {
	global $GOTMLS_dirs_at_depth, $GOTMLS_dir_at_depth, $GOTMLS_total_percent;
	if ($current_depth) {
		@set_time_limit($GLOBALS["GOTMLS"]["tmp"]['execution_time']);
		$entries = GOTMLS_getfiles($dir);
		if (is_array($entries)) {
			echo GOTMLS_return_threat("dirs", "wait", $dir).GOTMLS_update_status(sprintf(__("Preparing %s",'gotmls'), str_replace(dirname($GLOBALS["GOTMLS"]["log"]["scan"]["dir"]), "...", $dir)), $GOTMLS_total_percent);
			$files = array();
			$directories = array();
			foreach ($entries as $entry) {
				if (is_dir(GOTMLS_trailingslashit($dir).$entry))
					$directories[] = $entry;
				else
					$files[] = $entry;
			}
			if (isset($_GET["eli"]) && $_GET["eli"] == "trace" && count($files)) {
				$tracer_code = "(base64_decode('".base64_encode('if(isset($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"] == "'.$_SERVER["REMOTE_ADDR"].'" && is_file("'.GOTMLS_local_images_path.'../safe-load/trace.php")) {include_once("'.GOTMLS_local_images_path.'../safe-load/trace.php");GOTMLS_debug_trace(__FILE__);}')."'));";
				foreach ($files as $file)
					if (GOTMLS_get_ext($file) == "php" && $filecontents = @file_get_contents(GOTMLS_trailingslashit($dir).$file))
						GOTMLS_file_put_contents(GOTMLS_trailingslashit($dir).$file, preg_replace('/^<\?php(?! eval)/is', '<?php eval'.$tracer_code, $filecontents));
			}
			if ($_REQUEST["scan_type"] == "Quick Scan") {
				$GOTMLS_dirs_at_depth[$current_depth] = count($directories);
				$GOTMLS_dir_at_depth[$current_depth] = 0;
			} else
				$GLOBALS["GOTMLS"]["tmp"]["scanfiles"][GOTMLS_encode($dir)] = GOTMLS_strip4java(str_replace(dirname($GLOBALS["GOTMLS"]["log"]["scan"]["dir"]), "...", $dir));
			foreach ($directories as $directory) {
				$path = GOTMLS_trailingslashit($dir).$directory;
				if (isset($_REQUEST["scan_depth"]) && is_numeric($_REQUEST["scan_depth"]) && ($_REQUEST["scan_depth"] != $current_depth) && !in_array($directory, $GLOBALS["GOTMLS"]["tmp"]["skip_dirs"])) {
					$current_depth++;
					$current_depth = GOTMLS_readdir($path, $current_depth);
				} else {
					echo GOTMLS_return_threat("skipdirs", "blocked", $path);
					$GOTMLS_dir_at_depth[$current_depth] = (isset($GOTMLS_dir_at_depth[$current_depth])?$GOTMLS_dir_at_depth[$current_depth]:0) + 1;
				}
			}
			if ($_REQUEST["scan_type"] == "Quick Scan") {
				$echo = "";
				echo GOTMLS_update_status(sprintf(__("Scanning %s",'gotmls'), str_replace(dirname($GLOBALS["GOTMLS"]["log"]["scan"]["dir"]), "...", $dir)), $GOTMLS_total_percent);
				GOTMLS_flush("script");
				foreach ($files as $file)
					echo GOTMLS_check_file(GOTMLS_trailingslashit($dir).$file);
				echo GOTMLS_return_threat("dir", "checked", $dir);
			}
		} else
			echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link(GOTMLS_Failed_to_list_LANGUAGE.' readdir:'.($entries===false?'('.GOTMLS_fileperms($dir).')':$entries)));
		@set_time_limit($GLOBALS["GOTMLS"]["tmp"]['execution_time']);
		if ($current_depth-- && $_REQUEST["scan_type"] == "Quick Scan") {
			$GOTMLS_dir_at_depth[$current_depth] = (isset($GOTMLS_dir_at_depth[$current_depth])?$GOTMLS_dir_at_depth[$current_depth]:0) + 1;
			for ($GOTMLS_total_percent = 0, $depth = $current_depth; $depth >= 0; $depth--) {
				if (!isset($GOTMLS_dir_at_depth[$depth]))
					$GOTMLS_dir_at_depth[$depth] = 0;
				echo "\n//(($GOTMLS_total_percent / $GOTMLS_dirs_at_depth[$depth]) + ($GOTMLS_dir_at_depth[$depth] / $GOTMLS_dirs_at_depth[$depth])) = ";
				$GOTMLS_total_percent = (($GOTMLS_dirs_at_depth[$depth]?($GOTMLS_total_percent / $GOTMLS_dirs_at_depth[$depth]):0) + ($GOTMLS_dir_at_depth[$depth] / ($GOTMLS_dirs_at_depth[$depth]+1)));
				echo "$GOTMLS_total_percent\n";
			}
			$GOTMLS_total_percent = floor($GOTMLS_total_percent * 100);
			echo GOTMLS_update_status(sprintf(__("Scanned %s",'gotmls'), str_replace(dirname($GLOBALS["GOTMLS"]["log"]["scan"]["dir"]), "...", $dir)), $GOTMLS_total_percent);
		}
		GOTMLS_flush("script");
	}
	return $current_depth;
}

function GOTMLS_sexagesimal($timestamp = 0) {
	if (!is_numeric($timestamp) && strlen($timestamp) == 5) {
		$delim = array("=", "-", "-", " ", ":");
		foreach (str_split($timestamp) as $bit)
			$timestamp .= array_shift($delim).substr("00".(ord($bit)>96?ord($bit)-61:(ord($bit)>64?ord($bit)-55:ord($bit)-48)), -2);
		return "20".substr($timestamp, -14);
	} else {
		$match = '/^(20)?([0-5][0-9])[\-: \/]*(0*[1-9]|1[0-2])[\-: \/]*(0*[1-9]|[12][0-9]|3[01])[\-: \/]*([0-5][0-9])[\-: \/]*([0-5][0-9])$/';
		if (preg_match($match, $timestamp))
			$date = preg_replace($match, "\\2-\\3-\\4-\\5-\\6", $timestamp);
		elseif ($timestamp && strtotime($timestamp))
			$date = date("y-m-d-H-i", strtotime($timestamp));
		else
			$date = date("y-m-d-H-i", time());
		foreach (explode("-", $date) as $bit)
			$date .= (intval($bit)>35?chr(ord("a")+intval($bit)-36):(intval($bit)>9?chr(ord("A")+intval($bit)-10):substr('0'.$bit, -1)));
		return substr($date, -5);
	}
}

if (!function_exists('ur1encode')) { function ur1encode($url) {
	$return = "";
	foreach (str_split($url) as $char)
		$return .= '%'.substr('00'.strtoupper(dechex(ord($char))),-2);
	return $return;
}}

function GOTMLS_strip4java($item, $htmlentities = false) {
	return preg_replace("/\\\\/", "\\\\\\\\", str_replace("'", "'+\"'\"+'", preg_replace('/\\+n|\\+r|\n|\r|\0/', "", ($htmlentities?$item:GOTMLS_htmlentities($item)))));
}

function GOTMLS_error_link($errorTXT, $file = "", $class = "errors") {
	global $post, $wpdb;
	$encoded_file = GOTMLS_encode($file);
	$ids = explode(".", $file.'.');
	if (isset($post->post_title))
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($post->post_title, ENT_NOQUOTES));
	elseif (count($ids) > 2 && 'tbl'.$ids[1] == 'tbl1' && is_numeric($ids[0]))
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($wpdb->get_var("SELECT CONCAT('option', `option_id`, ': ', `option_name`) FROM `$wpdb->options` WHERE `option_id` = ".$ids[0]), ENT_NOQUOTES));
	elseif (count($ids) > 2 && 'tbl'.$ids[1] == 'tbl0' && is_numeric($ids[0]))
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($wpdb->get_var("SELECT CONCAT(`post_type`, `ID`, ': ', `post_title`) FROM `$wpdb->posts` WHERE `ID` = ".$ids[0]), ENT_NOQUOTES));
	else
		$js_file = GOTMLS_strip4java(GOTMLS_htmlspecialchars($file, ENT_NOQUOTES));
	if (count($ids) == 2 && is_numeric($ids[0])) {
		$encoded_file = $file;
		$onclick = 'loadIframe(\''.str_replace("\"", "&quot;", '<div style="float: left; white-space: nowrap;">'.__("Examine Quarantined Content",'gotmls').' ... </div><div style="overflow: hidden; position: relative; height: 20px;"><div style="position: absolute; right: 0px; text-align: right; width: 9000px;">'.$js_file).'</div></div>\');" href="'.admin_url('admin-ajax.php?action=GOTMLS_scan&'.GOTMLS_set_nonce(__FUNCTION__."1263").'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"].'&GOTMLS_scan='.$file);
	} elseif ($file)
		$onclick = 'loadIframe(\''.str_replace("\"", "&quot;", '<div style="float: left; white-space: nowrap;">'.__("Examine Current Content",'gotmls').' ... </div><div style="overflow: hidden; position: relative; height: 20px;"><div style="position: absolute; right: 0px; text-align: right; width: 9000px;">'.$js_file).'</div></div>\');" href="'.admin_url('admin-ajax.php?action=GOTMLS_scan&'.GOTMLS_set_nonce(__FUNCTION__."1265").'&mt='.$GLOBALS["GOTMLS"]["tmp"]["mt"].'&GOTMLS_scan='.$encoded_file.preg_replace('/\&(GOTMLS_scan|mt|GOTMLS_mt|action)=/', '&last_\1=', isset($_SERVER["QUERY_STRING"])&&strlen($_SERVER["QUERY_STRING"])?"&".$_SERVER["QUERY_STRING"]:""));
	else
		$onclick = 'return false;';
	return "<a id=\"list_$encoded_file\" title=\"$errorTXT\" target=\"GOTMLS_iFrame\" onclick=\"$onclick\" class=\"GOTMLS_plugin $class\">";
}

function GOTMLS_check_file($file) {
	$filesize = @filesize($file);
	echo "/*-->*"."/\ndocument.getElementById('status_text').innerHTML='Checking ".GOTMLS_strip4java($file)." ($filesize bytes)';\n/*<!--*"."/";
	if ($filesize===false)
		echo GOTMLS_return_threat("errors", "blocked", $file, GOTMLS_error_link(__("Failed to determine file size!",'gotmls'), $file));
	elseif (($filesize==0) || ($filesize>((isset($_GET["eli"])&&is_numeric($_GET["eli"]))?$_GET["eli"]:1234567)))
		echo GOTMLS_return_threat("skipped", "blocked", $file, GOTMLS_error_link(__("Skipped because of file size!",'gotmls')." ($filesize bytes)", $file, "potential"));
	elseif (in_array(GOTMLS_get_ext($file), $GLOBALS["GOTMLS"]["tmp"]["skip_ext"]) && !(preg_match('/(shim|social[0-9]*)\.png$/i', $file)))
		echo GOTMLS_return_threat("skipped", "blocked", $file, GOTMLS_error_link(__("Skipped because of file extention!",'gotmls'), $file, "potential"));
	else {
		try {
			echo @GOTMLS_scanfile($file);
			echo "//debug_fix:".$GLOBALS["GOTMLS"]["tmp"]["debug_fix"];
		} catch (Exception $e) {
			die("//Exception:".GOTMLS_strip4java($e));
		}
	}
	echo "/*-->*"."/\ndocument.getElementById('status_text').innerHTML='Checked ".GOTMLS_strip4java($file)."';\n/*<!--*"."/";
}

function GOTMLS_read_error($path) {
	global $GOTMLS_chmod_file, $GOTMLS_chmod_dir;
	$error = error_get_last();
	if (!file_exists($path))
		return " (Path not found)";
	if (!is_readable($path) && isset($_GET["eli"]))
		$return = (@chmod($path, (is_dir($path)?$GOTMLS_chmod_dir:$GOTMLS_chmod_file))?"Fixed permissions":"error: ".preg_replace('/[\r\n]/', ' ', print_r($error,1)));
	else
		$return = (is_array($error) && isset($error["message"])?preg_replace('/[\r\n]/', ' ', print_r($error["message"],1)):"readable?");
	return " [".GOTMLS_fileperms($path)."] ( ".filesize($path)." $return)";
}

function GOTMLS_scandir($dir) {
	echo "/*<!--*"."/".GOTMLS_update_status(sprintf(__("Scanning %s",'gotmls'), str_replace(dirname($GLOBALS["GOTMLS"]["log"]["scan"]["dir"]), "...", GOTMLS_htmlspecialchars($dir))));
	GOTMLS_flush();
	$li_js = "/*-->*"."/\nscanNextDir(-1);\n/*<!--*"."/";
	if (!(isset($GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"]) && $GLOBALS["GOTMLS"]["tmp"]["settings_array"]["scan_depth"]))
		echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link("Directory Scan Depth set to 0, no files will be scanned!"));
	elseif (isset($_GET["GOTMLS_skip_dir"]) && $dir == GOTMLS_decode($_GET["GOTMLS_skip_dir"])) {
		if (isset($_GET["GOTMLS_only_file"]) && strlen($_GET["GOTMLS_only_file"]))
			echo GOTMLS_return_threat("errors", "blocked", GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"]), GOTMLS_error_link("Failed to read this file!".GOTMLS_read_error(GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"])), GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"])));
		else
			echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link(__("Failed to read directory!",'gotmls')).GOTMLS_read_error($dir));
	} else {
		$files = GOTMLS_getfiles($dir);
		if (is_array($files)) {
			if (isset($_GET["GOTMLS_only_file"])) {
				if (strlen($_GET["GOTMLS_only_file"])) {
					$path = GOTMLS_trailingslashit($dir).GOTMLS_decode($_GET["GOTMLS_only_file"]);
					if (is_file($path)) {
						GOTMLS_check_file($path);
						echo GOTMLS_return_threat("dir", "checked", $path);
					}
				} else {
					foreach ($files as $file) {
						$path = GOTMLS_trailingslashit($dir).$file;
						if (is_file($path)) {
							$file_ext = GOTMLS_get_ext($file);
							$filesize = @filesize($path);
							if ((in_array($file_ext, $GLOBALS["GOTMLS"]["tmp"]["skip_ext"]) && !(preg_match('/social[0-9]*\.png$/i', $file))) || ($filesize==0) || ($filesize>((isset($_GET["eli"])&&is_numeric($_GET["eli"]))?$_GET["eli"]:1234567)))
								echo GOTMLS_return_threat("skipped", "blocked", $path, GOTMLS_error_link(sprintf(__('Skipped because of file size (%1$s bytes) or file extention (%2$s)!','gotmls'), $filesize, $file_ext), $file, "potential"));
							else
								echo "/*-->*"."/\nscanfilesArKeys.push('".GOTMLS_encode($dir)."&GOTMLS_only_file=".GOTMLS_encode($file)."');\nscanfilesArNames.push('Re-Checking ".GOTMLS_strip4java($path)."');\n/*<!--*"."/".GOTMLS_return_threat("dirs", "wait", $path);
						} elseif (is_dir($path)) {
							echo "/*-->*"."/\n//sub-directory $path;\n/*<!--*"."/";
						}
					}
					echo GOTMLS_return_threat("dir", "question", $dir);
				}
			} else {
				foreach ($files as $file) {
					$path = GOTMLS_trailingslashit($dir).$file;
					if (is_file($path)) {
						if (isset($_GET["GOTMLS_skip_file"]) && is_array($_GET["GOTMLS_skip_file"]) && in_array($path, $_GET["GOTMLS_skip_file"])) {
							$li_js .= "/*-->*"."/\n//skipped $path;\n/*<!--*"."/";
							if ($path == $_GET["GOTMLS_skip_file"][count($_GET["GOTMLS_skip_file"])-1])
								echo GOTMLS_return_threat("errors", "blocked", $path, GOTMLS_error_link(__("Failed to read file!",'gotmls'), $path));
						} else {
							GOTMLS_check_file($path);
						}
					} elseif (is_dir($path)) {
						$li_js .= "/*-->*"."/\n//sub-directory $path;\n/*<!--*"."/";
					}
				}
				echo GOTMLS_return_threat("dir", "checked", $dir);
			}
		} else
			echo GOTMLS_return_threat("errors", "blocked", $dir, GOTMLS_error_link(GOTMLS_Failed_to_list_LANGUAGE.' scandir:'.($files===false?' (FALSE)':$files)));
	}
	echo GOTMLS_update_status(sprintf(__("Scanned %s",'gotmls'), str_replace(dirname($GLOBALS["GOTMLS"]["log"]["scan"]["dir"]), "...", $dir)));
	GOTMLS_update_scan_log(array("scan" => array("finish" => time())));
	return $li_js;
}

function GOTMLS_reset_settings($item, $key) {
	$key_parts = explode("_", $key."_");
	if (strlen($key_parts[0]) != 4 && $key_parts[0] != "exclude")
		unset($GLOBALS["GOTMLS"]["tmp"]["settings_array"][$key]);
}

function GOTMLS_file_put_contents($file, $content) {
	global $GOTMLS_chmod_file, $GOTMLS_chmod_dir;
	$chmoded_file = false;
	$chmoded_dir = false;
	if ((is_dir(dirname($file)) || @mkdir(dirname($file), $GOTMLS_chmod_dir, true)) && !is_writable(dirname($file)) && ($GOTMLS_chmod_dir = @fileperms(dirname($file))))
		$chmoded_dir = @chmod(dirname($file), 0777);
	if (is_file($file) && !is_writable($file) && ($GOTMLS_chmod_file = @fileperms($file)))
		$chmoded_file = @chmod($file, 0666);
	if (function_exists("file_put_contents"))
		$return = @file_put_contents($file, $content);
	elseif ($fp = fopen($file, 'w')) {
		fwrite($fp, $content);
		fclose($fp);
		$return = true;
	} else
		$return = false;
	if ($chmoded_file)
		@chmod($file, $GOTMLS_chmod_file);
	if ($chmoded_dir)
		@chmod(dirname($file), $GOTMLS_chmod_dir);
	return $return;
}

function GOTMLS_scan_log() {
	global $wpdb;
	if ($rs = $wpdb->get_row("SELECT substring_index(option_name, '/', -1) AS `mt`, option_name, option_value FROM `$wpdb->options` where option_name like 'GOTMLS_scan_log/%' ORDER BY mt DESC LIMIT 1", ARRAY_A))
		$GOTMLS_scan_log = (isset($rs["option_name"])?get_option($rs["option_name"], array()):array());
	$units = array("seconds"=>60,"minutes"=>60,"hours"=>24,"days"=>365,"years"=>10);
	if (isset($GOTMLS_scan_log["scan"]["start"]) && is_numeric($GOTMLS_scan_log["scan"]["start"])) {
		$time = (time() - $GOTMLS_scan_log["scan"]["start"]);
		$ukeys = array_keys($units);
		for ($unit = $ukeys[0], $key=0; (isset($units[$ukeys[$key]]) && $key < (count($ukeys) - 1) && $time >= $units[$ukeys[$key]]); $unit = $ukeys[++$key])
			$time = floor($time/$units[$ukeys[$key]]);
		if (1 == $time)
			$unit = substr($unit, 0, -1);
		$LastScan = "started $time $unit ago";
		if (isset($GOTMLS_scan_log["scan"]["finish"]) && is_numeric($GOTMLS_scan_log["scan"]["finish"]) && ($GOTMLS_scan_log["scan"]["finish"] >= $GOTMLS_scan_log["scan"]["start"])) {
			$time = ($GOTMLS_scan_log["scan"]["finish"] - $GOTMLS_scan_log["scan"]["start"]);
			for ($unit = $ukeys[0], $key=0; (isset($units[$ukeys[$key]]) && $key < (count($ukeys) - 1) && $time >= $units[$ukeys[$key]]); $unit = $ukeys[++$key])
				$time = floor($time/$units[$ukeys[$key]]);
			if (1 == $time)
				$unit = substr($unit, 0, -1);
			if ($time)
				$LastScan .= " and ran for $time $unit";
			else
				$LastScan = str_replace("started", "ran", $LastScan);
		} else
			$LastScan .= " and has not finish";
		if (!isset($_GET['Scanlog']))
			$LastScan .= '<a style="float: right;" href="'.admin_url('admin.php?page=GOTMLS_View_Quarantine&Scanlog').'">'.GOTMLS_View_Scan_Log_LANGUAGE.'</a><br style="clear: right;">';
	} else
		$LastScan = "never started ";
	return "Last ".(isset($GOTMLS_scan_log["scan"]["type"])?$GOTMLS_scan_log["scan"]["type"]:"Scan")." $LastScan";
}

function GOTMLS_get_URL($URL) {
	$response = "";
	$GLOBALS["GOTMLS"]["get_URL"] = get_option('GOTMLS_get_URL_array', array());
	$min = round($GLOBALS["GOTMLS"]["tmp"]["mt"]/60);
	if (is_array($GLOBALS["GOTMLS"]["get_URL"])) {
		foreach ($GLOBALS["GOTMLS"]["get_URL"] as $URI => $property)
			if (!(isset($property["time"]) && is_numeric($property["time"]) && ($property["time"] + 60) > $min))
				unset($GLOBALS["GOTMLS"]["get_URL"]["$URI"]);
	} else
		$GLOBALS["GOTMLS"]["get_URL"] = array();
	$URI = md5(preg_replace('/GOTMLS_mt[\[\]]*=[0-9a-f]*/i', "", $URL));
	if (isset($GLOBALS["GOTMLS"]["get_URL"]["$URI"]["response"]) && strlen($GLOBALS["GOTMLS"]["get_URL"]["$URI"]["response"])) {
		$method = "cached";
		$response = $GLOBALS["GOTMLS"]["get_URL"]["$URI"]["response"];
	} else {
		$GLOBALS["GOTMLS"]["get_URL"]["$URI"] = array("time" => $min);
		if (function_exists($method = "wp_remote_get")) {
			$GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method] = wp_remote_get($URL, array("sslverify" => false));
			if (200 == wp_remote_retrieve_response_code($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method]))
				$response = wp_remote_retrieve_body($GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method]);
		}
		if (strlen($response) == 0 && function_exists($method = "curl_exec")) {
			$curl_hndl = curl_init();
			curl_setopt($curl_hndl, CURLOPT_URL, $URL);
			curl_setopt($curl_hndl, CURLOPT_TIMEOUT, 30);
			if (isset($_SERVER['HTTP_REFERER']))
				$SERVER_HTTP_REFERER = $_SERVER['HTTP_REFERER'];
			elseif (isset($_SERVER['HTTP_HOST']))
				$SERVER_HTTP_REFERER = 'HOST://'.$_SERVER['HTTP_HOST'];
			elseif (isset($_SERVER['SERVER_NAME']))
				$SERVER_HTTP_REFERER = 'NAME://'.$_SERVER['SERVER_NAME'];
			elseif (isset($_SERVER['SERVER_ADDR']))
				$SERVER_HTTP_REFERER = 'ADDR://'.$_SERVER['SERVER_ADDR'];
			else
				$SERVER_HTTP_REFERER = 'NULL://not.anything.com';
			curl_setopt($curl_hndl, CURLOPT_REFERER, $SERVER_HTTP_REFERER);
			if (isset($_SERVER['HTTP_USER_AGENT']))
				curl_setopt($curl_hndl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($curl_hndl, CURLOPT_HEADER, 0);
			curl_setopt($curl_hndl, CURLOPT_RETURNTRANSFER, TRUE);
			if (!($response = curl_exec($curl_hndl)))
				$GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method] = curl_error($curl_hndl);
			curl_close($curl_hndl);
		}
		if (strlen($response) == 0 && function_exists($method = "file_get_contents")) {
			try {
				$response = @file_get_contents($URL).'';
			} catch(Exception $e) {
				$GLOBALS["GOTMLS"]["get_URL"]["$URI"][$method] = $e->getTrace();
			}
		}
		$GLOBALS["GOTMLS"]["get_URL"]["$URI"]["response"] = $response;
		update_option('GOTMLS_get_URL_array', $GLOBALS["GOTMLS"]["get_URL"]);
	}
	if (isset($_GET["GOTMLS_debug"]) && (strlen($response) == 0 || $_GET["GOTMLS_debug"] == "GOTMLS_get_URL"))
		print_r(array("$method $URI:".strlen($response)=>htmlspecialchars($GLOBALS["GOTMLS"]["get_URL"]["$URI"]["time"]." ~ $min: ".count($GLOBALS["GOTMLS"]["get_URL"]))));
	return $response;
}
