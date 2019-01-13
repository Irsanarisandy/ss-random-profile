<?php

namespace App\Factories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use SilverStripe\Core\Injector\Factory;

class ClientFactory implements Factory
{
    private const maxRetries = 3;

    public function create($service, array $params = [])
    {
        $handlerStack = HandlerStack::create(new CurlHandler());

        $handlerStack->push(
            Middleware::retry(
                function ($retries, $request, $response, $exception) {
                    if ($retries > self::maxRetries) {
                        return false;
                    }
                    if ($exception instanceof ConnectException) {
                        return true;
                    }
                    if ($response && $response->getStatusCode() >= 500) {
                        return true;
                    }
                    return false;
                },
                function ($retries) {
                    return (int) 2 ** ($retries - 1) * 10;  // in milliseconds
                }
            )
        );

        $client = new Client(
            array_merge(
                [
                    'handler' => $handlerStack,
                    'timeout' => 3,
                    'connect_timeout' => 3,
                ],
                $params
            )
        );

        return $client;
    }
}
