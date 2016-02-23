<?php
namespace Zhil\UdpNotifier;

use React\Datagram\Socket;

class SimpleCrypto
{
    const METHOD = 'aes-256-ctr';
    const NONCE = 'ITS_NOT_IMPORTAN'; // 16 bytes string should be here
    private $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function encrypt($data)
    {
        return openssl_encrypt($data,$this::METHOD,$this->password,false,$this::NONCE);
    }
    public function decrypt($data)
    {
        return openssl_decrypt($data,$this::METHOD,$this->password,false,$this::NONCE);
    }
}