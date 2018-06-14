<?php

namespace UChicago\AdvisoryCouncil;

/**
 *
 * Application class
 * @author tommyt
 *
 */

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Twig_Loader_Filesystem;
use Twig_Environment;

class Application extends \WS\SharedPHP\WS_Application
{
    private $twig;
    private $charset;
    private $templatesPath;
    private $sessionTimeout = 3600;

    /*
     * Whitelist for u of c user groups.
     */
    const GROUPER_WHITE_LIST = array('uc:org:nsit:webservices:members', 'uc:org:ard:griffinusers');
    /*
     * Valid Shibb provider
     */
    const SHIBB_IDP = "urn:mace:incommon:uchicago.edu";
    /*
     * Social auth gateway.
     */
    const SOCIAL_AUTH_IDP = array('https://google.cirrusidentity.com/gateway','https://yahoo.cirrusidentity.com/gateway','https://facebook.cirrusidentity.com/gateway');

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
        return $this->isProd() ? "https://ardapi.uchicago.edu/api/" : "https://ardapi-uat2015.uchicago.edu/api/";
    }

    public function environment()
    {
        return $this->isProd() ? "prod" : "dev";
    }

    public function domain()
    {
        return "https://" . $_SERVER['HTTP_HOST'];
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

    public function apiCreds()
    {
        require ('/data/aliasedphp/visitingdirectory/authorization.php');
        return array("username" => $username,  "password" => $password);
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
     * Checks to see if group in $_SERVER['ucisMemberOf']
     * returned from Shibb is in GROUPER_WHITE_LIST.
     */
    public function isValidGroup()
    {
        $groups = array();
        if (isset($_SERVER['ucisMemberOf'])) {
            $groups = explode(";", $_SERVER['ucisMemberOf']);
        }
        return count(array_intersect(self::GROUPER_WHITE_LIST, $groups)) > 0 ? true : false;
    }

    public function isValidSocialAuth(Client $client , $email, $bearer_token)
    {

        $response = $client->request('GET',
            "report/VC?email_address=" . $email,
            [
                'headers' => ['Authorization' => $bearer_token]
            ]
        );

        if ($response->getStatusCode() == "200") {
            $results = json_decode($response->getBody())->results;
            foreach ($results as $key => $r) {
                if (isset($r->ID_NUMBER)
                    && isset($r->TMS_RECORD_STATUS_CODE)
                    && isset($r->TMS_EMAIL_STATUS_CODE)
                    && $r->TMS_RECORD_STATUS_CODE == "Active"
                    && $r->TMS_EMAIL_STATUS_CODE == "Active") {
                    return true;
                }
            }
        }

        return false;
    }

    public function isValid()
    {
        return isset($_SESSION['bearer_token']);
    }

    /**
     * Is user using Shibb to authenticate?
     */
    public function userIsFromShibb()
    {
        return (isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SHIBB_IDP));
    }

    /**
     * Is user using social auth gateway to authenticate?
     */
    public function userIsFromSocialAuth()
    {
        return (isset($_SERVER['Shib-Identity-Provider']) && in_array( $_SERVER['Shib-Identity-Provider'] , self::SOCIAL_AUTH_IDP));
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