<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 5/30/18
 * Time: 9:47 AM
 */

namespace UChicago\AdvisoryCouncil;


use WS\SharedPHP\WS_Memcache;

class CLIMemcache extends  WS_Memcache
{
    /**
     * Use memcache?
     * @var boolean
     */
    private $useMemcache = true;
    /**
     * The memcache object
     * @var object
     */
    private $memcache = NULL;
    /**
     * Get Memcache object and add the server, in necessary.
     * @return object - the memcache object.
     */
    public function getMemcacheForCLI($environment)	{
        if($this->memcache == NULL) {
            $this->memcache = new  CLIMemcache;
            if( $environment == "prod")
            {

                $this->memcache->addServer('memcachedev01.uchicago.edu', 11211);
//                $this->memcache->addServer('memcache01.uchicago.edu', 11211);
//                $this->memcache->addServer('memcache02.uchicago.edu', 11211);
//                $this->memcache->addServer('memcache03.uchicago.edu', 11211);
//                $this->memcache->addServer('memcache04.uchicago.edu', 11211);
            }
            else
            {
                $this->memcache->addServer('memcachedev01.uchicago.edu', 11211);
            }
        }
        return $this->memcache;
    }
}