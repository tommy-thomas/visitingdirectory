<?php
// http://us.php.net/manual/en/function.curl-multi-exec.php#109377
class cURL extends WS_cURL{
	/*
	 * Client code may have need to dump this for debugging.
	 */
	protected $_requestinfo;
	/*
	 * Array of curl handles from curl multi handle.
	 */
	private $nodes = array();
	/*
	 * Handle for curl multi handle.
	 */
	private $master_handle;
	/*
	 * Returned curl code after curl multi execute.
	 */
	private $mrc;
	/*
	 * Flag indicating if curl handle is still running.
	 */
	private $active = null;
	/**
	 * Constructor.
	 */
	public function __construct($url=null)
	{
		define('CREDS_FILE_PATH', dirname(__FILE__)."/../restricted/authorization.php");
		parent::__construct($url);
		require_once (CREDS_FILE_PATH);
		$this->auth_name = $USERNAME;
		$this->auth_pass = $PASSWORD;		
	}
	/**
	 * Load the master handle with multiple curl handles.
	 * @param $url
	 * @param $list
	 */
	public function createCurlMultiple( $url , $list = array() )
	{
		$this->master_handle = curl_multi_init();
		$this->nodes = array();
		try {
			if( !empty($list) )
			{
				foreach ( $list as $i )
				{						
					$tmp_handle = $this->createHandle( sprintf( $url , $i ) );
					$this->nodes[] = $tmp_handle;
					curl_multi_add_handle($this->master_handle, $tmp_handle );
				}
				do {
					$this->mrc = curl_multi_exec($this->master_handle, $this->active);
				} while( $this->mrc == CURLM_CALL_MULTI_PERFORM );

				while( $this->active && $this->mrc == CURLM_OK )
				{
					if( curl_multi_select($this->master_handle) != -1 )
					{
						do
						{
							$this->mrc = curl_multi_exec( $this->master_handle, $this->active );
						} while( $this->mrc == CURLM_CALL_MULTI_PERFORM );
					}
				}
			}
				
		} catch (Exception $e) {
			Application::handleExceptions($e);
		}
	}
	/**
	 * @return array of curl handles.
	 */
	public function getNodes()
	{
		return $this->nodes;
	}
	/**
	 * Create and return curl handle.
	 * @param $url
	 */
	public function createHandle( $url = null )
	{
		if( !is_null($url) )
		{
			$this->_url = $url;

			/*
			 * initialize curl session
			 */
			$s = curl_init();

			curl_setopt($s,CURLOPT_URL,$this->_url);
			curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:'));
			curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout);
			curl_setopt($s, CURLOPT_CONNECTTIMEOUT ,0);
			curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects);
			curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($s, CURLOPT_VERBOSE, 1);
			curl_setopt($s, CURLOPT_SSL_VERIFYPEER, FALSE);

			if($this->authentication == 1)
			{
				curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
			}
			 
			if($this->_post)
			{
				curl_setopt($s,CURLOPT_POST,true);
				curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields);
			}

			if($this->_includeHeader)
			{
				curl_setopt($s,CURLOPT_HEADER,true);
			}

			if($this->_noBody)
			{
				curl_setopt($s,CURLOPT_NOBODY,true);
			}
			curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent);
			curl_setopt($s,CURLOPT_REFERER,$this->_referer);
			 
			return $s;
		}
	}
	/**
	 * Clear old handles from the master handle. 
	 */
	public function clear()
	{
		if( count($this->nodes) > 0 )
		{
			foreach ($this->nodes as $n)
			{
				curl_multi_remove_handle($this->master_handle, $n );
			}
			curl_multi_close($this->master_handle);
		}
	}
	/**
	 * Create login url with username and password in opts array;
	 * @param $login_url
	 */
	public function authenticate( $login_url )
	{
		try {
			$opts = array(
				'username' => $this->auth_name,
				'password' => $this->auth_pass
			);
			$this->setPost($opts);
			$this->createCurl( $login_url );
		} catch (Exception $e) {
			Application::handleExceptions($e);
		}
		
	}
	/**
	 * Convert instance response xml to simple xml object.
	 */
	public function asSimpleXML()
	{
		return simplexml_load_string( $this->_webpage );
	}
	/**
	 * Get http status.
	 */
	public function getStatus()
	{
		return !is_null($this->_status) ? $this->_status : "";
	}
}
?>