<?php
namespace Zhil\UdpNotifier;

use Silex\Application;
use Silex\ServiceProviderInterface;


class UdpNotifierServerProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        // TODO: move code to abstract class vis closure handlers
        // TODO: receive settings
        $app['zhil.udp_notifier.server'] = $app->protect(function ($handler) use ($app) {
            $loop = \React\EventLoop\Factory::create();
            $factory = new \React\Datagram\Factory($loop);
            $factory->createServer('localhost:1234')->then(function (\React\Datagram\Socket $server) use ($app,$handler) {
                $server->on('message', function($message, $address, $server) use ($app,$handler) {
//                    $server->send('hello ' . $address . '! echo: ' . $message, $address);
//                    echo 'client ' . $address . ': ' . $message . PHP_EOL;
                    // TODO: add secret checking, ban feature
                    // TODO: add encoding
                    if($json = json_decode($message)) {
                        $app[$handler]($json );
                    }
                });
            });
            $loop->run();
            ;
        });
    }

    public function boot(Application $app)
    {
        $xdebugPoint = "cant find where is it called?";
    }
}