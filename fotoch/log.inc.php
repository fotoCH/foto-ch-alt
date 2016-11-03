<?php

function getstatus(){
	if ($_COOKIE['nolog'])
		return 9;
	return 0;
}

function isbot() {
	// Get current User-Agent
	$current = strtolower( $_SERVER['HTTP_USER_AGENT'] );

	// Array of known bot lowercase strings
	// Example: 'googlebot' will match 'Googlebot/2.1 (+http://www.googlebot.com/bot.html)'
	$bots = array(
	// general web bots
                'googlebot', 'yahoo! slurp',
                'dotbot', 'yeti', 'http://help.naver.com/robots/', 'scoutjet', 
                'http://yandex.com/bots', 'linkedinbot', 'mj12bot', 'http://www.80legs.com/spider.html',
                'exabot', 'msnbot', 'yacybot', 'www.oneriot.com', 'http://flipboard.com/', 
                'baiduspider', 'mxbot', 'bingbot','sitebot','ipsentry','ezooms.bot',

	// twitter specific bots
                'http://thinglabs.com', 'js-kit url resolver', 'twitterbot', 'njuicebot', 'postrank.com',
                'tweetmemebot', 'longurl api', 'paperlibot', 'http://postpo.st/crawlers',
	);

	// Check if the current UA string contains a know bot string
	$is_bot = ( str_replace( $bots, '', $current ) != $current );

	return $is_bot;
}

function log_session(){
	global $sqli;
	$sid = session_id();
	$ua=mysqli_real_escape_string($sqli, $_SERVER['HTTP_USER_AGENT']);
	$req=mysqli_real_escape_string($sqli, $_SERVER['REQUEST_URI']);
	$isbot=isbot()?1:0;
	if (getstatus()==9){
		return;
	}
	$sql="INSERT INTO log_sessions (`session_id`, `start`, `last`,`useragent`,`firstpage`,`isbot`) VALUES ('".$sid."',NOW(),NOW(),'".$ua."','".$req."',$isbot)";
	//mysqli_query($sqli, $sql);
	if (!mysqli_error($sqli)){
		include_once("./php-user-agent/phpUserAgent.php");
		$userAgent = new phpUserAgent();

		$name=mysqli_real_escape_string($sqli, $userAgent->getBrowserName());    // firefox
		$version=mysqli_real_escape_string($sqli, $userAgent->getBrowserVersion());   // 3.6
		$os=mysqli_real_escape_string($sqli, $userAgent->getOperatingSystem());  // linux
		$engine=mysqli_real_escape_string($sqli, $userAgent->getEngine());           // gecko
		$sql="UPDATE log_sessions SET `browser`='".$name."', `version`='".$version."',`os`='".$os."',`engine`='".$engine."'  WHERE `session_id`='".$sid."'";
		//echo $sql;
		//mysqli_query($sqli, $sql);
	}
	$sql="UPDATE log_sessions SET `last`=NOW(), `count`=`count`+1, `seconds`=TIMESTAMPDIFF(SECOND,`start`,`last`)  WHERE `session_id`='".$sid."'";
	//echo $sql;
	//mysqli_query($sqli, $sql);
}

function log_setLevel(){
	global $sqli;
	$sid = session_id();
	$sql="UPDATE log_sessions SET `level`=".$_SESSION['usr_level']."  WHERE `session_id`='".$sid."'";
	//echo $sql;
	//mysqli_query($sqli, $sql);
}

function log_page($kategorie,$search,$action,$lang,$level,$url){
	global $sqli;
	if (getstatus()==9){
		return;
	}
	
	$action=mysqli_real_escape_string($sqli, $action);
	$url=mysqli_real_escape_string($sqli, $url);
	$isbot=isbot()?1:0;
	$sql="INSERT INTO log_pages (`kategorie`,`search`,`action`,`lang`,`level`,`url`,`isbot`) VALUES ('$kategorie',$search,'$action','$lang','$level','$url',$isbot)";
	//mysqli_query($sqli, $sql);
}
//$sql = "SELECT COUNT(id),useragent,level FROM `log_sessions` WHERE 1 GROUP BY useragent,level ORDER BY level,useragent LIMIT 0, 30 ";

?>