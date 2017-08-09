<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/7/6
 * Time: 17:09
 */

namespace Leo108\SDK;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Leo108\SDK\Middleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApi
{
    /**
     * @var SDK
     */
    protected $sdk;

    /**
     * AbstractApi constructor.
     *
     * @param SDK $sdk
     */
    public function __construct(SDK $sdk)
    {
        $this->sdk = $sdk;
    }

    /**
     * send a Get api request
     *
     * @param string $api     api endpoint
     * @param array  $queries api queries
     * @param array  $options extra options provided to guzzle
     *
     * @return ResponseInterface
     */
    protected function apiGet($api, array $queries = [], array $options = [])
    {
        $options[RequestOptions::QUERY] = $queries;

        return $this->apiRequest('GET', $this->getFullApiUrl($api), $options);
    }

    /**
     * send a Post api request
     *
     * @param string $api     api endpoint
     * @param array  $params  api params
     * @param array  $options extra options provided to guzzle
     *
     * @return ResponseInterface
     */
    protected function apiPost($api, array $params = [], array $options = [])
    {
        $options[RequestOptions::FORM_PARAMS] = $params;

        return $this->apiRequest('POST', $this->getFullApiUrl($api), $options);
    }

    /**
     * Upload files
     *
     * @param string $api     api endpoint
     * @param array  $files   key => value format, key is the request parameter name,
     *                        value can be the content of the file or
     *                        the return value from fopen or a Psr\Http\Message\StreamInterface object
     * @param array  $options extra options provided to guzzle
     *
     * @return ResponseInterface
     */
    protected function apiUpload($api, array $files, array $options = [])
    {
        $options[RequestOptions::MULTIPART] = [];
        foreach ($files as $name => $content) {
            $options[RequestOptions::MULTIPART][] = [
                'name'    => $name,
                'content' => $content,
            ];
        }

        return $this->apiRequest('POST', $this->getFullApiUrl($api), $options);
    }

    /**
     * send a Post api request using application/json
     *
     * @param string $api     api endpoint
     * @param array  $param   api params
     * @param array  $options extra options provided to guzzle
     *
     * @return ResponseInterface
     */
    protected function apiJson($api, array $param, array $options = [])
    {
        $options[RequestOptions::JSON] = $param;

        return $this->apiRequest('POST', $this->getFullApiUrl($api), $options);
    }

    /**
     * send http request
     *
     * @param string $method  request method
     * @param string $url     target url
     * @param array  $options options provided to guzzle
     *
     * @return ResponseInterface
     */
    protected function apiRequest($method, $url, $options = [])
    {
        $options['handler'] = $this->createHandler();

        return $this->sdk->getHttpClient()->request($method, $url, $options);
    }

    /**
     * override this method to add middleware to guzzle request
     * @see http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html
     *
     * @return array
     */
    protected function getHttpMiddleware()
    {
        return [];
    }

    /**
     * @return HandlerStack
     */
    private function createHandler()
    {
        $stack      = HandlerStack::create();
        $middleware = $this->getHttpMiddleware();
        foreach ($middleware as $item) {
            if ($item instanceof MiddlewareInterface) {
                $stack->push($item());
            } else {
                $stack->push($item);
            }
        }

        return $stack;
    }

    /**
     * @param string $api
     *
     * @return string
     */
    protected function getFullApiUrl($api)
    {
        return $api;
    }
}
