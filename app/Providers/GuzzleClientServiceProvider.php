<?php

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class GuzzleClientServiceProvider extends ServiceProvider
{
    private Logger $logger;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('GuzzleClient', function () {
            $messageFormats = [
                'REQUEST: METHOD: {method} - URL: {uri} - HTTP/{version} - HEADERS: {req_headers} - BODY: {req_body}',
                'RESPONSE: STATUS: {code} - BODY: {res_body}'
            ];
            $stack = $this->setLoggingHandler($messageFormats);

            return function ($config) use ($stack) {
                return new Client(array_merge($config, ['handler' => $stack]));
            };
        });
    }

    /**
     * Setup a Logger
     *
     * @return Logger
     */
    private function getLogger(): Logger
    {
        if (property_exists($this, 'logger')) {
            $this->logger = with(new Logger('guzzle-log'))->pushHandler(new RotatingFileHandler(storage_path('logs/guzzle-log.log')));
        }

        return $this->logger;
    }

    /**
     * Setup a Middleware
     *
     * @param string $messageFormat
     * @return callable
     */
    private function setGuzzleMiddleware(string $messageFormat): callable
    {
        return Middleware::log($this->getLogger(), new MessageFormatter($messageFormat));
    }

    /**
     * Setup a Logging Handler Stack
     *
     * @param array $messageFormats
     * @return HandlerStack
     */
    private function setLoggingHandler(array $messageFormats): HandlerStack
    {
        $stack = HandlerStack::create();
        collect($messageFormats)->each(function ($messageFormat) use ($stack) {
            $stack->unshift($this->setGuzzleMiddleware($messageFormat));
        });

        return $stack;
    }

}
