<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/8/13
 * Time: 10:51
 */

namespace Leo108\SDK\Middleware;

use Psr\Http\Message\RequestInterface;

class LogMiddleware implements MiddlewareInterface
{
    /**
     * @var callable|null
     */
    protected $requestLogger;
    /**
     * @var callable|null
     */
    protected $responseLogger;

    /**
     * LogMiddleware constructor.
     * @param callable|null $requestLogger
     * @param callable|null $responseLogger
     */
    public function __construct($requestLogger, $responseLogger)
    {
        $this->requestLogger  = $requestLogger;
        $this->responseLogger = $responseLogger;
    }

    public function __invoke()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (is_callable($this->requestLogger)) {
                    call_user_func($this->requestLogger, $request, $options);
                }
                $response = $handler($request, $options);
                if (is_callable($this->responseLogger)) {
                    call_user_func($this->responseLogger, $request, $options, $response);
                }

                return $response;
            };
        };
    }
}
