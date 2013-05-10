<?php
/*
 * key = md5(ws592013)
 * dc9c6663511c522e5369538a44159693
 */
if( $_GET['key'] == md5('ws592013') )
{
	set_time_limit(0);
	ignore_user_abort(1);
	require('../_classes/autoload.php');
	/**
	 * The Application object.
	 */
	$app = Application::app();
	/*
	 * Get Griffin authtoken.
	 */
	$curl = new cURL(null);
	$collection = GriffinCollection::instance($app , $curl );
	$curl->authenticate( $collection->getLoginUrl() );
	$authtoken = $curl->__toString();
	/*
	 * Set base url.
	 */
	$base_url = "";
	if( $app->isDev() )
	{
		$domain = "visitingdirectorydev.uchicago.edu";
	} 
	elseif($app->isStage())
	{
		$domain = "visitingdirectorystage.uchicago.edu";
	}
	elseif($app->isProd())
	{
		$domain = "visitingdirectory.uchicago.edu";
	}
	/*
	 * Path with authtoken and payload variable that lets cache script know which payload to set.
	 */
	$all_data_payload_path = "/cache/set_cache.php?payload=alldata&authtoken=".$authtoken;
	$first_payload_path = "/cache/set_cache.php?payload=one&authtoken=".$authtoken;
	$second_payload_path = "/cache/set_cache.php?payload=two&authtoken=".$authtoken;
	$third_payload_path = "/cache/set_cache.php?payload=three&authtoken=".$authtoken;
	/*
	 * Set main payload with all of the member data.
	 */
	$errno = "";
	$errmsg = "";
	$fp = fsockopen('ssl://'.$domain, 443, $errno, $errmsg);
	$out = "GET ".$all_data_payload_path." HTTP/1.1\r\n";
	$out .= "Host:".$domain."\r\n";
	$out .= "Connection: Close\r\n\r\n";
	stream_set_blocking($fp, false);
	stream_set_timeout($fp, 86400);
	print fread($fp, 86400);
	fclose($fp);
	
	sleep(5);
	/*
	 * Set committees one medium sized script execution at a time.
	 */
	$errno = "";
	$errmsg = "";
	$fp = fsockopen('ssl://'.$domain, 443, $errno, $errmsg);
	$out = "GET ".$first_payload_path." HTTP/1.1\r\n";
	$out .= "Host:".$domain."\r\n";
	$out .= "Connection: Close\r\n\r\n";
	stream_set_blocking($fp, false);
	stream_set_timeout($fp, 86400);
	print fread($fp, 86400);
	fclose($fp);
	
	sleep(1);
	
	$errno = "";
	$errmsg = "";
	$fp = fsockopen('ssl://'.$domain, 443, $errno, $errmsg);
	$out = "GET ".$second_payload_path." HTTP/1.1\r\n";
	$out .= "Host:".$domain."\r\n";
	$out .= "Connection: Close\r\n\r\n";
	stream_set_blocking($fp, false);
	stream_set_timeout($fp, 86400);
	print fread($fp, 8192);
	fclose($fp);
	
	sleep(1);
	
	$errno = "";
	$errmsg = "";
	$fp = fsockopen('ssl://'.$domain, 443, $errno, $errmsg);
	$out = "GET ".$third_payload_path." HTTP/1.1\r\n";
	$out .= "Host:".$domain."\r\n";
	$out .= "Connection: Close\r\n\r\n";
	stream_set_blocking($fp, false);
	stream_set_timeout($fp, 86400);
	print fread($fp, 86400);
	fclose($fp);	
}
?>