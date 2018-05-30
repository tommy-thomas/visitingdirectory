<?php

namespace UChicago\AdvisoryCouncil;

/**
 *
 * Application class
 * @author tommyt
 *
 */

use Twig_Loader_Filesystem;
use Twig_Environment;

class Application extends \WS_Application
{
    private $twig;
    private $charset;
    private $templatesPath;
    private $sessionTimeout = 3600;

    /*
     * Whitelist for autorized social auth services.
     */
    private $social_auth_whitelist = array('facebook.com', 'google.com', 'yahoo.com');
    /*
     * Whitelist for u of c user groups.
     */
    private $group_white_list = array('uc:org:nsit:webservices:members', 'uc:org:ard:griffinusers');
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
    public function __construct($requireSession = true)
    {
        parent::__construct($requireSession, $this->sessionTimeout);
        $this->charset = "utf-8";
        $this->templatesPath = __DIR__ . "/../templates";
    }

    public function template($templateFile)
    {
        if (!$this->twig) {
            $loader = new Twig_Loader_Filesystem($this->templatesPath);
            $this->twig = new Twig_Environment($loader, [
                "charset" => $this->charset
            ]);
            // Add global template vars
            $this->twig->addGlobal("title", "Advisory Councils");
            if ($this->isLoggedIn()) {
                $this->twig->addGlobal("user", $this->getUser());
            }
        }
        return $this->twig->loadTemplate($templateFile);
    }

    public function ardUrl()
    {
        //uat once it's working: "https://ardapi-uat2015.uchicago.edu/api/"
        return $this->isProd() ? "https://ardapi.uchicago.edu/api/" : "https://ardapi.uchicago.edu/api/";
    }

    public function login(User $user)
    {
        $_SESSION['user'] = $user;
    }

    public function logout()
    {
        session_destroy();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user']);
    }

    public function requireLogIn()
    {
        if (!$this->isLoggedIn()) {
            header("Location: index.php");
        }
    }

    public function setUser($user = "")
    {
        $_SESSION['user'] = new User();
        $_SESSION['user']->setUserName($user);
    }

    public function getUser()
    {
        return isset($_SESSION['user']) ? $_SESSION['user']->getUserName() : "";
    }


    /**
     * @param $i
     * @return array|mixed
     */
    public function getErrorMessage($i)
    {
        $error_message = array(
            0 => "You are not authorized to log into this site.",
            1 => "You are not authorized to authenticate from this service."
        );
        return isset($error_message[$i]) ? $error_message[$i] : array();
    }

    /**
     * Return url including http(s).
     */
    public function domain()
    {
        $parts = parse_url($this->base());
        $url = $parts['scheme'] . '://' . $parts['host'];
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
        if (!isset($_SESSION['email'])) {
            $is_valid_service = false;
            if (isset($_SERVER) && isset($_SERVER['Shib-Identity-Provider'])) {
                if ($this->userIsFromShibb()) {
                    $is_valid_service = true;
                } elseif ($this->userIsFromSocialAuth()) {
                    list($name, $domain) = explode("@", $_SERVER['PHP_AUTH_USER']);
                    if (in_array($domain, $this->social_auth_whitelist)) {
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
        if (isset($_SERVER['ucisMemberOf'])) {
            $groups = explode(";", $_SERVER['ucisMemberOf']);
        }
        return count(array_intersect($this->group_white_list, $groups)) > 0 ? true : false;
    }

    /**
     * Is user using Shibb to authenticate?
     */
    public function userIsFromShibb()
    {
        return (isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SHIBB_AUTH_PROVIDER));
    }

    /**
     * Is user using social auth gateway to authenticate?
     */
    public function userIsFromSocialAuth()
    {
        return (isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SOCIAL_AUTH_GATEWAY) && !is_null($_SERVER['PHP_AUTH_USER']));
    }

    /**
     * If session email variable is set , user is authorized.
     */
    public function isAuthorized()
    {
        return (isset($_SESSION['email']) && isset($_SESSION['bearer_token']));
    }

    /**
     * Handle any exception in the application.
     * @param object The thrown Exception object
     */
    public static function handleExceptions($e)
    {
        $exceptionMessage = 'Caught EXCEPTION ' . __FILE__ . ' @ ' . __LINE__ . ':' . $e->getMessage();
        throw new Exception ($exceptionMessage);
    }
}

?>