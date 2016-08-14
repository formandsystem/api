<?php

namespace Formandsystem\Api;

use Formandsystem\Api\Interfaces\Cache as CacheInterface;
use GuzzleHttp;

class Api
{
    public function __construct(array $config, CacheInterface $cache, GuzzleHttp\Client $guzzleClient)
    {
        // merge config data
        $this->config = new Config($config);
        // get cache implementation
        $this->cache = $cache;
        // get cache implementation
        $this->client = $guzzleClient;
    }

    /**
     * send a get request to the specified endpoint.
     *
     * @method get
     *
     * @param string $endpoint e.g. /pages
     *
     * @return array
     */
    public function get($endpoint, $headers = [])
    {
        // prepare endpoint string
        $endpoint = $this->prepareEndpoint($endpoint);
        // send request
        $response = $this->parseResponse($this->client->get($endpoint, [
            'headers' => array_merge([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->access_token($this->config->scopes),
            ], $headers),
        ]));
        // return response
        return $response;
    }

    /**
     * send a post request to the specified endpoint.
     *
     * @method post
     *
     * @param string $endpoint [description]
     * @param array  $body     [description]
     *
     * @return response object
     */
    public function post($endpoint, $body, $headers = [])
    {
        // prepare endpoint string
        $endpoint = $this->prepareEndpoint($endpoint);
        // send request
        $response = $this->parseResponse($this->client->post($endpoint, [
            'headers' => array_merge([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->access_token($this->config->scopes),
            ], $headers),
            'body' => json_encode([
                'data' => $body,
            ]),
        ]));
        // return response
        return $response;
    }

    /**
     * send a patch request to the specified endpoint.
     *
     * @method patch
     *
     * @param string $endpoint [description]
     * @param array  $body     [description]
     *
     * @return response object
     */
    public function patch($endpoint, $body, $headers = [])
    {
        // prepare endpoint string
        $endpoint = $this->prepareEndpoint($endpoint);
        // send request
        $response = $this->parseResponse($this->client->patch($endpoint, [
            'headers' => array_merge([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->access_token($this->config['scopes']),
            ], $headers),
            'body' => json_encode([
                'data' => $body,
            ]),
        ]));
        // return response
        return $response;
    }

    /**
     * send a put request to the specified endpoint.
     *
     * @method put
     *
     * @param string $endpoint [description]
     * @param array  $body     [description]
     *
     * @return response object
     */
    public function put($endpoint, $body = false, $headers = [], $json = true)
    {
        if ($json === true) {
            $body = json_encode([
                'data' => $body,
            ]);
        }
        // prepare endpoint string
        $endpoint = $this->prepareEndpoint($endpoint);
        // send request
        $response = $this->parseResponse($this->client->put($endpoint, [
            'headers' => array_merge([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$this->access_token($this->config['scopes']),
            ], $headers),
            'body' => $body,
        ]));
        // return response
        return $response;
    }

    /**
     * send a delete request to the specified endpoint.
     *
     * @method delete
     *
     * @param string $endpoint [description]
     * @param array  $body     [description]
     *
     * @return response object
     */
    public function delete($endpoint, $body = false)
    {
        // prepare endpoint string
        $endpoint = $this->prepareEndpoint($endpoint);
        // prepare data
        $delete_data['headers'] = array_merge([
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer '.$this->access_token($this->config['scopes']),
        ], []);
        // add body if set
        if ($body !== false) {
            $delete_data['body'] = json_encode([
                'data' => $body,
            ]);
        }
        // send request
        $response = $this->parseResponse($this->client->delete($endpoint, $delete_data));
        // return response
        return $response;
    }

    /**
     * get and cache an access token.
     *
     * @method access_token
     *
     * @param array $scopes
     *
     * @return string
     */
    protected function access_token($scopes)
    {
        // check if token is cached
        if ($this->cache->has('access_token'.$this->config->client_id) && count(array_diff($scopes, $this->cache->get('access_token'.$this->config->client_id)['scopes'])) === 0) {
            // return token from cache
            return $this->cache->get('access_token'.$this->config->client_id)['token'];
        }
        // get access token
        $response = $this->parseResponse(
            $this->client->post($this->prepareEndpoint('/tokens'), [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->config->client_id,
                    'client_secret' => $this->config->client_secret,
                    'scope'         => implode(',', array_map('trim', $scopes)),
                ],
            ])
        );
        // cache access token
        if (isset($response['data'])) {
            // convert timestamp to DateTime
            $date = new \DateTime('@'.$response['data']['attributes']['expires_in']);
            // cache token
            $this->cache->put('access_token'.$this->config->client_id, [
                'token'  => $response['data']['id'],
                'scopes' => $scopes,
            ], $date);
        }
        // return token from cache
        return $response['data']['id'];
    }

    /**
     * parse the response from guzzle.
     *
     * @method parseResponse
     *
     * @param GuzzleResponse $response
     *
     * @return array
     */
    protected function parseResponse($response)
    {
        // decode response
        $json = json_decode($response->getBody(), true);
        // check for error
        if (isset($json['error'])) {
            throw new \ErrorException($json['error']['message'], $json['error']['status_code']);
        }
        // else return data
        return $json;
    }

    /**
     * preapre an enpoint url for api requests.
     *
     * @method prepareEndopint
     *
     * @param string $endpoint
     *
     * @return string
     */
    protected function prepareEndpoint($endpoint)
    {
        return rtrim($this->config->url, '/').'/'.ltrim($endpoint, '/');
    }
}
