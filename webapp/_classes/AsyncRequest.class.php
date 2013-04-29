<?php
/**
 * Class to mimic async http request using ssl.
 */
class AsyncRequest
{	
	public function __construct( $domain ,$path )
	{
		$errno = "";
		$errmsg = "";
		try {
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

?>