<?php

namespace UChicago\AdvisoryCommittee;
/**
 * 
 * Application class
 * @author tommyt
 *
 */
use WS\SharedPHP\WS_Application;

class Application extends WS_Application
{
	/*
	 * App
	 */
	static private $app;
	/*
	 * Session timeout variable.
	 */
	private $maxtime = 3600;
	/*
	 * Whitelist for autorized social auth services.
	 */
	private $social_auth_whitelist = array('facebook.com' , 'google.com' , 'yahoo.com' );
	/*
	 * Whitelist for u of c user groups.
	 */
	private $group_white_list = array('uc:org:nsit:webservices:members','uc:org:ard:griffinusers');
	/*
	 * Valid Shibb provider
	 */
	const SHIBB_AUTH_PROVIDER = "urn:mace:incommon:uchicago.edu";
	/*
	 * Social auth gateway.
	 */
	const SOCIAL_AUTH_GATEWAY = "https://social-auth-gateway.uchicago.edu/simplesaml/saml2/idp/metadata.php";	
	
	/**
	 * Public constructor.
	 */
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
	/**
	 * Return error message.
	 * @param $i
	 */
	public function get_error_message($i)
	{
		$error_message = array(
			0 => "You are not authorized to log into this site.",
			1 => "You are not authorized to authenticate from this service."
		);
		return isset( $error_message[$i]) ? $error_message[$i] : array(); 
	}
	/**
	 * Return url including http(s).
	 */
	public function domain()
	{
		$parts = parse_url( self::$app->base() );
		$url = $parts['scheme'].'://'.$parts['host'];
		return $url;
	}
	/**
	 * Return if authorization being attempted from Shibb?
	 */
	public function isShibbAuth()
	{
		return isset($_SERVER['Shib-Session-ID']);
	}
	/**
	 * 
	 * Return if authorization being attempted a valid service.
	 */
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
					list($name,$domain) = explode("@", $_SERVER['PHP_AUT`H_USER']);
					if( in_array($domain, $this->social_auth_whitelist))
					{
						$is_valid_service = true;
					}
				}
			}
		}		
		return $is_valid_service;
	}
	/**
	 * Checks to see if group in $_SERVER['ucisMemberOf']
	 * returned from Shibb is in $group_white_list.
	 */
	public function isValidGroup()
	{	
		$groups = array();
		if( isset( $_SERVER['ucisMemberOf']) )
		{
			$groups = explode(";",  $_SERVER['ucisMemberOf']);
		}
		return count(array_intersect($this->group_white_list, $groups)) > 0 ? true : false;
	}
	/**
	 * Is user using Shibb to authenticate?
	 */
	public function userIsFromShibb()
	{
		return ( isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SHIBB_AUTH_PROVIDER));
	}
	/**
	 * Is user using social auth gateway to authenticate?
	 */
	public function userIsFromSocialAuth()
	{
		return ( isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SOCIAL_AUTH_GATEWAY) && !is_null($_SERVER['PHP_AUTH_USER']));
	}
	/**
	 * If session email variable is set , user is authorized.
	 */	
	public function isAuthorized()
	{
		return ( isset($_SESSION['email']) && isset($_SESSION['authtoken']) );
	}
		/**
	 * Handle any exception in the application.
	 * @param object The thrown Exception object
	 */
	public static function handleExceptions($e)
	{
		$exceptionMessage = 'Caught EXCEPTION ' . __FILE__ . ' @ '. __LINE__ . ':' . $e->getMessage();
		throw new Exception ($exceptionMessage);
	}
}
?>