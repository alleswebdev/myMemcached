<?php

namespace Alleswebdev\Tools;

Class Memcached
{

    protected $socket = null;
    protected $address = null;
    protected $port = null;

    public function connect(string $host = 'localhost', int $port = 11211)
    {
        $this->address = gethostbyname($host);
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->port = $port;
        if (!$this->socket) {
            echo "error socket create: " . socket_strerror(socket_last_error()) . "\n";
            return false;
        }

        if (!socket_connect($this->socket, $this->address, $this->port)) {
            echo "error socket connect " . socket_strerror(socket_last_error($this->socket,)) . "\n";
            return false;
        }

        return true;
    }

    public function close()
    {
        socket_close($this->socket);
        return true;
    }

    public function sendCommand(string $data, int $returnSize = 2048)
    {
        $data = trim($data) . '\r\n';
        if (!socket_write($this->socket, $data, strlen($data))){
            echo "error write or zero byte was written " . socket_strerror(socket_last_error($this->socket,)) . "\n";
            return false;
        }

        return (string) socket_read($this->socket, $returnSize);
    }
}
