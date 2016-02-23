<?php
namespace Zhil\UdpNotifier;

use React\Datagram\Socket;

class Listener
{
    private $udpServerIp;
    private $udpServerPort;
    private $secret;

    private $simpleCrypto;

    private $handlerCallable;
    private $invalidRequestHandlerCallable;

    public function __construct($udpServerIp,$udpServerPort,$secret)
    {
        $this->udpServerIp = $udpServerIp;
        $this->udpServerPort = $udpServerPort;
        $this->secret = $secret;
        $this->simpleCrypto = new SimpleCrypto($this->secret);
    }

    public function receive($msg, $address, Socket $server)
    {
        $decrypted = $this->simpleCrypto->decrypt($msg);
        if((substr($decrypted,0,1) != "{") && is_callable($this->invalidRequestHandlerCallable)) {
            call_user_func($this->invalidRequestHandlerCallable, $decrypted, $address);
        } else {
            call_user_func($this->handlerCallable, $decrypted, $address);
        }
    }

    public function run($handlerCallable,$invalidRequestHandlerCallable = null)
    {
        $this->handlerCallable = $handlerCallable;
        $this->invalidRequestHandlerCallable = $invalidRequestHandlerCallable;

        $loop = \React\EventLoop\Factory::create();
        $factory = new \React\Datagram\Factory($loop);
        $listener = $this;
        $factory->createServer($this->udpServerIp.':'.$this->udpServerPort)->then(function (\React\Datagram\Socket $server) use ($listener) {
            $server->on('message',[$listener,"receive"]);
        });
        $loop->run();
    }
}