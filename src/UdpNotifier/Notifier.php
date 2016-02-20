<?php
namespace Zhil\UdpNotifier;

use React\Datagram\Socket;

class Notifier
{
    private $udpServerIp;
    private $udpServerPort;
    private $secret;

    private $socket;

    public function __construct($udpServerIp,$udpServerPort,$secret)
    {
        $this->udpServerIp = $udpServerIp;
        $this->udpServerPort = $udpServerPort;
        $this->secret = $secret;
    }

    public function notification($name,$data = [])
    {
        // TODO: send notifier version?
        $this->send(json_encode(["secret"=>$this->secret,"name"=>$name,"data"=>$data]));
    }

    public function ping()
    {
        // TODO: ping feature could be implemented using https://github.com/reactphp/datagram
    }

    private function send($msg)
    {
        return socket_sendto($this->getSocket(), $msg, strlen($msg), 0, $this->udpServerIp, $this->udpServerPort);
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