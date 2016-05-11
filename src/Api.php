<?php

namespace Formandsystem\Api;

use Formandsystem\Api\Interfaces\Cache as CacheInterface;
use GuzzleHttp\Client as Guzzle;

class Api{

    public function __construct($config, CacheInterface $cache)
    {
        // merge config data
        $this->config = array_merge([
            'url'           => 'http://api.formandsystem.app',
            'version'       => '1',
            'client_id'     => NULL,
            'client_secret' => NULL,
            'cache'         => true,
            'scopes'        => ['content.get']
        ], $config);
        // get cache implementation
        $this->cache = $cache;
        // setup client
        $this->client = new Guzzle([
            'base_uri' => $this->config['url'],
            'exceptions' => false,
        ]);
    }
    /**
     * send a get request to the specified endpoint
     *
     * @method get
     *
     * @param  string $endpoint e.g. /pages
     *
     * @return array
     */
    public function get($endpoint){
        // prepare endpoint string
        $endpoint = '/'.trim($endpoint, '/');
        // send request
        $response = $this->parseResponse($this->client->get($endpoint, [
            'headers' => array_merge([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$this->access_token($this->config['scopes'])
            ], []),
        ]));
        // return response
        return $response;
    }

    public function post(){

    }
    public function patch(){

    }
    public function put(){

    }
    public function delete(){

    }
    /**
     * get and cache an access token
     *
     * @method access_token
     *
     * @param  array       $scopes
     *
     * @return string
     */
    protected function access_token($scopes){
        // check if token is cached
        if ( ! $this->cache->has('access_token'.$this->config['client_id']) || count(array_diff($scopes, $this->cache->get('access_token'.$this->config['client_id'])['scopes'])) > 0 ){
            // get access token
            $response = $this->parseResponse(
                $this->client->post('/tokens', [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'form_params' => [
                        'grant_type'    => 'client_credentials',
                        'client_id'     => $this->config['client_id'],
                        'client_secret' => $this->config['client_secret'],
                        'scope'        => implode(',',array_map('trim',$scopes)),
                    ]
                ])
            );
            // cache access token
            if(isset($response['data'])){
                // convert timestamp to DateTime
                $date = new \DateTime("@".$response['data']['attributes']['expires_in']);
                // cache token
                $this->cache->put('access_token'.$this->config['client_id'], [
                    'token' => $response['data']['id'],
                    'scopes' => $scopes,
                ], $date);
            }
        }
        // return token from cache
        return $this->cache->get('access_token'.$this->config['client_id'])['token'];
    }
    /**
     * parse the response from guzzle
     *
     * @method parseResponse
     *
     * @param  GuzzleResponse        $response
     *
     * @return array
     */
    protected function parseResponse($response){
        // decode response
        $json = json_decode($response->getBody(), true);
        // check for data
        if(isset($json['data'])){
            return $json;
        }
        // check for error
        if(isset($json['error'])){
            return $json['error'];
        }
    }
}
