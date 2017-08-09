<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/8/9
 * Time: 12:40
 */

namespace Leo108\SDK\Middleware;

use Psr\Http\Message\RequestInterface;

class TokenMiddleware implements MiddlewareInterface
{
    /**
     * @var bool|callable
     */
    protected $isRequireToken;
    /**
     * @var null|callable
     */
    protected $attachToken;

    /**
     * TokenMiddleware constructor.
     * @param bool|callable $isRequireToken
     * @param null|callable $attachToken
     */
    public function __construct($isRequireToken, callable $attachToken = null)
    {
        $this->isRequireToken = $isRequireToken;
        $this->attachToken    = $attachToken;
    }

    public function __invoke()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if (is_bool($this->isRequireToken)) {
                    $requireToken = $this->isRequireToken;
                } else {
                    $requireToken = call_user_func($this->isRequireToken, $request);
                }

                if ($requireToken) {
                    $request = call_user_func($this->attachToken, $request);
                }

                return $handler($request, $options);
            };
        };
    }
}
