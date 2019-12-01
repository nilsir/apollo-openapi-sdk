<?php
/**
 * Created by IntelliJ IDEA.
 * User: bella
 * Date: 2019-11-28
 * Time: 14:39
 */

namespace OrderHandler\ApolloOpenApi\Kernel;

use GuzzleHttp\Client;
use OrderHandler\ApolloOpenApi\Configure;
use RuntimeException;

class HttpClient
{
    protected $config;

    public function __construct(Configure $config)
    {
        $this->config = $config;
    }


    protected function httpGet(string $url, $params = '')
    {
        return $this->request($url, 'GET', $params);
    }

    protected function httpPost(string $url, $data)
    {
        //todo json_encode $data
        return $this->request($url, 'POST', $data);
    }

    protected function httpPut(string $url, string $data)
    {
        return $this->request($url, 'PUT', $data);
    }


    protected function httpDelete(string $url, $params = '')
    {
        return $this->request($url, 'DELETE', $params);
    }


    private function request($url, $method, string $request_options)
    {

        $method = strtoupper($method);


        try {
            $client = new Client;

            $response = $client->request($method, $url, [
                'headers' => [
                    'Content-Type' => 'application/json;charset=utf-8',
                    'Authorization' => $this->config->getAuthorization()

                ],
                'body' => $request_options

            ]);


            if ($response->getStatusCode() != 200) {
                throw new RuntimeException($response->getStatusCode());
            }
            $rbody = $response->getBody();
            $resJson = (string)$rbody;

            $res =  $resJson ? \GuzzleHttp\json_decode($resJson, 1) : '';

            return $res;

        } catch (RuntimeException $e) {

            return $e->getMessage();
        }


    }



//    protected function fixJsonIssue(array $options): array
//    {
//        if (isset($options['json']) && is_array($options['json'])) {
//            $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);
//
//            if (empty($options['json'])) {
//                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_FORCE_OBJECT);
//            } else {
//                $options['body'] = \GuzzleHttp\json_encode($options['json'], JSON_UNESCAPED_UNICODE);
//            }
//
//            unset($options['json']);
//        }
//
//        return $options;
//    }
}