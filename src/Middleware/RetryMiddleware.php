<?php
/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/8/9
 * Time: 17:19
 */

namespace Leo108\SDK\Middleware;

use GuzzleHttp\Middleware;

class RetryMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    protected $decider;
    /**
     * @var callable|null
     */
    protected $delay;

    /**
     * RetryMiddleware constructor.
     * @param callable      $decider
     * @param callable|null $delay
     */
    public function __construct(callable $decider, callable $delay = null)
    {
        $this->decider = $decider;
        $this->delay   = $delay;
    }

    public function __invoke()
    {
        return Middleware::retry($this->decider, $this->delay);
    }
}
