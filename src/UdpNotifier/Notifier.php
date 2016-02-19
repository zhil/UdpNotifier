<?php
namespace UdpNotifier;

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

    public function cmd($cmd,$data = [])
    {
        // TODO: send notifier version?
        $data["secret"] = $this->secret;
        $data["cmd"] = $cmd;
        $this->send(json_encode($data));
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
        if($this->socket) {
            return $this->socket;
        }
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        $msg = "Ping !";
        $len = strlen($msg);

        $time = microtime(true);
        for($i = 1;$i<=10000 ;$i++) {

        }
        echo("Done in ".(microtime(true) - $time));
    }

    private function close()
    {
        if($this->socket) {
            socket_close($this->socket);
        }
    }

    private function __destruct()
    {
        $this->close();
    }
}