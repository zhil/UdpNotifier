<?php
namespace Zhil\UdpNotifier;

use React\Datagram\Socket;

class Notifier
{
    private $udpServerIp;
    private $udpServerPort;
    private $secret;

    private $socket;

    private $simpleCrypto;

    public function __construct($udpServerIp,$udpServerPort,$secret)
    {
        $this->udpServerIp = $udpServerIp;
        $this->udpServerPort = $udpServerPort;
        $this->secret = $secret;
        $this->simpleCrypto = new SimpleCrypto($this->secret);
    }

    public function notification($name,$data = [])
    {
        $this->send(json_encode(["secret"=>$this->secret,"name"=>$name,"data"=>$data]));
    }

    public function ping()
    {
        // TODO: ping feature could be implemented using https://github.com/reactphp/datagram
    }

    private function send($msg)
    {
        $encrypted = $this->simpleCrypto->encrypt($msg);
        return socket_sendto($this->getSocket(), $encrypted, strlen($encrypted), 0, $this->udpServerIp, $this->udpServerPort);
    }

    private function getSocket()
    {
        if(!$this->socket) {
            $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        }
        return $this->socket;
    }

    private function close()
    {
        if($this->socket) {
            socket_close($this->socket);
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}