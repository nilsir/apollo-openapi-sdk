<?php
/**
 * Created by IntelliJ IDEA.
 * User: bella
 * Date: 2019-12-01
 * Time: 14:52
 */

namespace OrderHandler\ApolloOpenApi\Kernel;


use InvalidArgumentException;

class BasicApi
{

    private $init_params = [
        'portal_address' => '',
    ];

    private $httpClient;

    private $config;

    private $logger;
    public function __construct()
    {
        $this->httpClient = new HttpClient();
        $this->config = Register::get('config');
        $this->logger = $this->config->getLogger();
    }

    protected function send($method, $url_params, $url, $requestBody = '')
    {



        $url_params = $this->checkParams($url, $url_params);

        $url = Str::urlMerge($url_params, $url);

        $this->logger->info("call apollo api {$url}, with params " . $requestBody);

        switch ($method) {
            case 'GET':
                $response =  $this->httpClient->httpGet($url);
                break;
            case 'POST':
                $response =  $this->httpClient->httpPost($url, $requestBody);
                break;
            case 'PUT':
                $response =  $this->httpClient->httpPut($url, $requestBody);
                break;
            case 'DELETE':
                $response =  $this->httpClient->httpDelete($url);
                break;
            default:
                throw new InvalidArgumentException('Unknown method');
                break;
        }

        $this->logger->info("call apollo api {$url}, with responseContent " . json_encode($response));

        return $response;

    }

    /**
     * 通过传入的url地址获取必须替换的url参数，对比传入的url参数数组判断传参是否正确
     *
     * @param string $url 请求的API地址
     * @param array $data 需要填写的URL参数数组
     * @return array
     *
     */
    private function checkParams($url, array $data = [])
    {

        $this->init_params['portal_address'] = $this->config->getPortalAddress();
        $params = array_merge($this->init_params, $data);

        $required = Str::paramsFilter($url);

        foreach ($required as $item) {
            if (!in_array($item, array_keys($params), true)) {
                throw new InvalidArgumentException(sprintf('Attribute "%s" can not be empty!', $item));
            }
        }

        $params = array_merge(array_flip($required), $params);

        foreach ($params as $key => $param) {
            if (empty($param)) throw new InvalidArgumentException(sprintf('Attribute "%s" can not be empty!', $key));
        }

        return $params;
    }

}