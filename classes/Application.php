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
    const DB_PATH = "/data/aliasedphp/visitingdirectory/db/committee_data.db";

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
            $this->twig->addGlobal("WebAppName", "Advisory Councils");
            $this->twig->addGlobal("CacheBuster", "20240515");
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
