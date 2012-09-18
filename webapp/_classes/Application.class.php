<?php
class Application extends WS_Application
{
	static private $app;
	
	private $maxtime = 3600;	
	
	private $social_auth_whitelist = array('facebook.com' , 'google.com' , 'yahoo.com' );
	
	private $group_white_list = array('uc:org:nsit:webservices:members','uc:org:ard:griffinusers');
	
	const SHIBB_AUTH_PROVIDER = "urn:mace:incommon:uchicago.edu";
	
	const SOCIAL_AUTH_GATEWAY = "https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/metadata.php";	
	
	public function __construct()
	{
		parent::__construct(1, $this->maxtime);
		self::$app = $this;			
	}
	
	/**
	 * Get this Application object. If none exists, make it.
	 * @return Application object
	 */
	public static function app()
	{
		if(!self::$app)
		{
			self::$app = new Application();
		}
		return self::$app;
	}
	
	public function get_error_message($i)
	{
		$error_message = array(
			0 => "You are not authorized to log into this site.",
			1 => "You are not authorized to authenticate from this service."
		);
		return isset( $error_message[$i]) ? $error_message[$i] : array(); 
	}
	
	public function domain()
	{
		$parts = parse_url( self::$app->base() );
		$url = $parts['scheme'].'://'.$parts['host'];
		return $url;
	}
	
	public function isShibbAuth()
	{
		if( isset($_SERVER['Shib-Session-ID']) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function isValidService()
	{
		$is_valid_service = false;
		if( !isset( $_SESSION['email'] ))
		{
			$is_valid_service = false;
			if( isset($_SERVER) && isset($_SERVER['Shib-Identity-Provider']))
			{
				if( $this->userIsFromShibb() )
				{
					$is_valid_service = true;
				}
				elseif( $this->userIsFromSocialAuth() )
				{
					list($name,$domain) = explode("@", $_SERVER['PHP_AUTH_USER']);
					if( in_array($domain, $this->social_auth_whitelist))
					{
						$is_valid_service = true;
					}
				}
			}
		}		
		return $is_valid_service;
	}
	
	public function isValidGroup()
	{	
		$groups = array();
		if( isset( $_SERVER['ucisMemberOf']) )
		{
			$groups = explode(";",  $_SERVER['ucisMemberOf']);
		}
		$result = array_intersect($this->group_white_list, $groups);
		return count($result) > 0 ? true : false;
	}
	
	public function userIsFromShibb()
	{
		return ( isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SHIBB_AUTH_PROVIDER));
	}
	
	public function userIsFromSocialAuth()
	{
		return ( isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SOCIAL_AUTH_GATEWAY) && !is_null($_SERVER['PHP_AUTH_USER']));
	}
	
	public function isAuthorized()
	{
		return isset($_SESSION['email']);
	}
}
?>