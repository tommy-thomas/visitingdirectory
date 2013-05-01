<?php
/**
 * Class to mimic async http request using ssl.
 */
class AsyncRequest
{	
	public function __construct( $domain ,$path )
	{			
		if( $this->isValid($domain, $path) )
		{
			try {
				$errno = "";
				$errmsg = "";
				$fp = fsockopen('ssl://'.$domain, 443, $errno, $errmsg);
				$out = "GET ".$path." HTTP/1.1\r\n";
			    $out .= "Host:".$domain."\r\n";
			    $out .= "Connection: Close\r\n\r\n";
			    stream_set_blocking($fp, false);
				stream_set_timeout($fp, 86400);
			    fwrite($fp, $out);
			    fclose($fp);
			} catch (Exception $e) {
				Application::handleExceptions($errno.": ".$errmsg);
			}
		}
	}
	
	private function isValid( $domain=null, $path=null )
	{
		$whitelist = array('visitingdirectorydev.uchicago.edu','visitingdirectorystage.uchicago.edu','visitingdirectory.uchicago.edu');
		$pos = strripos($path, 'cache/?authtoken=');
		return ( in_array($domain , $whitelist) && $pos !== false ) ? true : false;
	}
}
?>