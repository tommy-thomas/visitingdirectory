<?php
class Application extends WS_Application
{
	static private $app;
	
	private $maxtime = 3600;	
	
	private $social_auth_whitelist = array('facebook.com' , 'google.com' , 'yahoo.com' );
	
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
				if( $_SERVER['Shib-Identity-Provider'] == self::SHIBB_AUTH_PROVIDER )
				{
					$is_valid_service = true;
				}
				elseif( ($_SERVER['Shib-Identity-Provider'] == self::SOCIAL_AUTH_GATEWAY) && !is_null($_SERVER['PHP_AUTH_USER']))
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
	
	public function isAuthorized()
	{
		if( isset($_SESSION['email']) )
		{ 
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>