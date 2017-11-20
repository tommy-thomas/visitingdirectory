<?php
/**
 * Created by PhpStorm.
 * User: tommy-thomas
 * Date: 11/20/17
 * Time: 3:35 PM
 */

namespace UChicago\AdvisoryCommittee;



class CommitteeMemberFactory
{
    public $info;
    public $addresses;
    public $name;
    public $telephone;
    public $degree;
    public $employment;

    public function __construct( $json_payload = null)
    {
        if( !is_null($json_payload)){

            $ref = new \ReflectionClass($this);
            $props = $ref->getProperties();
            foreach ( $props as $key => $prop ){
                $this->{$prop} = isset($json_payload->{$prop->getName()}) ? $json_payload->{$prop->getName()} : null;
            }
           var_dump($this);

        }

    }
}