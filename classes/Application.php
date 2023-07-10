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
use Twig\Loader\FilesystemLoader as FilesystemLoader;
use Twig\Environment as Environment;
use WS\SharedPHP\WS_Application;

class Application extends WS_Application
{
    private $twig;
    private $charset;
    private $templatesPath;
    private $sessionTimeout = 3600;
    const API_URL = "https://itsapi.uchicago.edu/system/ascend/v1/api/query/";
    const APP_SEC_HEADER_KEY = "AppSecScan";
    const APP_SEC_HEADER_VALUE = "05c4b923ef378fe66b04519e87d4ab3e";

    /*
     * Whitelist for u of c user groups.
     */
    const GROUPER_WHITE_LIST = array('uc:applications:web-services:visitingdirectorydev','uc:org:nsit:webservices:members', 'uc:org:ard:griffinusers');
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
            $loader = new FilesystemLoader($this->templatesPath);
            $this->twig = new Environment($loader, [
                "charset" => $this->charset
            ]);
            // Add global template vars
            $this->twig->addGlobal("title", "Advisory Councils");
            if ($this->isLoggedIn()) {
                $this->twig->addGlobal("user", $this->getUser());
            }
        }
        return $this->twig->load($templateFile);
    }

    public function apiUrl()
    {
        return self::API_URL;
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


    public function isAppSecScan(){
        return $this->isDev() && (isset($_SERVER[self::APP_SEC_HEADER_KEY]) && $_SERVER[self::APP_SEC_HEADER_KEY] == self::APP_SEC_HEADER_VALUE);
    }

    /**
     * @return bool
     */
    public function userIsFromShibb()
    {
        return (isset($_SERVER['Shib-Identity-Provider']) && ($_SERVER['Shib-Identity-Provider'] == self::SHIBB_IDP));
    }

    /**
     * @return bool
     */
    public function userIsFromSocialAuth()
    {
        return (isset($_SERVER['Shib-Identity-Provider']) && in_array( $_SERVER['Shib-Identity-Provider'] , self::SOCIAL_AUTH_IDP));
    }

    public function isValidSocialAuth(Client $client , $email)
    {
        $headers_array = [
            'headers' => [
                'client_id' => CLIENT_ID,
                'client_secret' => CLIENT_SECRET
            ]
        ];
        $response = $client->request('GET', $this->apiUrl() . "contact?q=Email='".$email."'", $headers_array);

        if ($response->getStatusCode() == "200") {
            return json_decode($response->getBody())->totalSize > 0;
        }
        return false;
    }

    /**
     * If session email variable is set , user is authorized.
     * * * original w/ token check * * *
     *  public function isAuthorized()
         {
             return (isset($_SESSION['email']) && isset($_SESSION['bearer_token']));
         }
     */
    public function isAuthorized()
    {
        return (isset($_SESSION['email']) );
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