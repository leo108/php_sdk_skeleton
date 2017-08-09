<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/7/6
 * Time: 17:06
 */

namespace Leo108\SDK;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use ReflectionClass;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;

abstract class SDK
{
    /**
     * @var AbstractApi[]
     */
    protected $apiInstances = [];

    /**
     * @var Client
     */
    protected $httpClient = null;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SDK constructor.
     *
     * @param array           $config
     * @param ClientInterface $httpClient
     * @param LoggerInterface $logger
     */
    public function __construct(array $config = [], ClientInterface $httpClient = null, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->setLogger($logger);
        $this->setHttpClient($httpClient);
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();

        return $this;
    }

    /**
     * @param ClientInterface $httpClient
     *
     * @return $this
     */
    public function setHttpClient(ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new Client([RequestOptions::HTTP_ERRORS => false]);

        return $this;
    }


    /**
     * @param null|string $key
     * @param mixed       $default
     * @return mixed
     */
    public function getConfig($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        return Utils::get($this->config, $key, $default);
    }

    /**
     * @param string $name
     *
     * @return AbstractApi|null
     */
    public function __get($name)
    {
        $apiMap = $this->getApiMap();
        if (!isset($apiMap[$name])) {
            return null;
        }

        if (!isset($this->apiInstances[$name])) {
            $this->apiInstances[$name] = (new ReflectionClass($apiMap[$name]))->newInstanceArgs([$this]);
        }

        return $this->apiInstances[$name];
    }

    /**
     * override this method to register api
     *
     * @return array
     */
    abstract protected function getApiMap();
}
