<?php

namespace Formandsystem\Api;

use InvalidArgumentException;

class Config
{
    /*
     * config items
     */
    protected $items = [
        'url'           => NULL,
        'version'       => NULL,
        'client_id'     => NULL,
        'client_secret' => NULL,
        'cache'         => true,
        'scopes'        => ['content.get'],
    ];
    /*
     * required config options
     */
    protected $required_items = [
        'url',
        'version',
        'client_id',
        'client_secret',
    ];
    /*
     * construct config DTO
     */
    public function __construct(Array $config){
        $this->setConfig(array_change_key_case($config, CASE_LOWER));
    }
    /*
     * Getter for values
     */
    public function __get($key)
    {
        if( isset($this->items[$key]) ){
            return $this->items[$key];
        }
    }
    /*
     * return all config items
     */
    public function toArray()
    {
        return $this->items;
    }
    /**
     * set config from array
     * @param Array $config [description]
     */
    protected function setConfig(Array $config){
        // validate required keys are present
        if(count(array_diff($this->required_items, array_keys($config))) !== 0){
            throw new InvalidArgumentException("Misconfiguration: You need to provide the following configuration values: ".implode($this->required_items,', '));
        }
        // add and validate individual keys
        foreach ($config as $key => $value) {
            $this->validate($key, $value);
            $this->items[$key] = $value;
        }
    }
    /**
     * validate given config value
     * @param  string $key   [description]
     * @param  string $value [description]
     */
    protected function validate($key,$value)
    {
        // validate nothing is null
        if($value === NULL){
            throw new InvalidArgumentException("Misconfiguration: $key may not be set to 'NULL'.");
        }
        // make sure version number is an integer
        if($key === 'version' && !is_int($value)){
            throw new InvalidArgumentException("Misconfiguration: Version must be an integer.");
        }
        // make sure 'url','client_id','client_secret' are strings
        if(in_array($key, ['url','client_id','client_secret']) && !is_string($value)){
            throw new InvalidArgumentException("Misconfiguration: $key must be a string.");
        }
        // make sure cache is boolean
        if($key === 'cache' && !is_bool($value)){
            throw new InvalidArgumentException("Misconfiguration: $key must be a boolean.");
        }
        // make sure scopes are arreay
        if($key === 'scopes' && !is_array($value)){
            throw new InvalidArgumentException("Misconfiguration: $key must be an array.");
        }
    }
}
