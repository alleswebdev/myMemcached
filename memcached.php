<?php

namespace Alleswebdev\Tools;

/**
 * Class Memcached
 * @package Alleswebdev\Tools
 */
Class Memcached
{
    protected $socket = null;
    protected $address = null;
    protected $port = null;
    protected $buf = '';

    /**
     * @param string $host
     * @param int $port
     * @return bool
     */
    public function connect(string $host = "localhost", int $port = 11211)
    {
        $this->address = gethostbyname($host);
        $this->port = $port;

        $this->socket = @fsockopen($host, $port);
        stream_set_timeout($this->socket, 5);
        if (!$this->socket) {
            echo "cant connect to server";
            return false;
        }

        return true;
    }

    public function close()
    {
        fclose($this->socket);
    }

    /**
     * @param string $data
     * @return bool|string
     */
    public function sendCommand(string $data)
    {
        $data = trim($data);
        $data .= "\r\n";
        $this->buf = '';
        if (!fwrite($this->socket, $data)) {
            echo "error write or zero byte was written\n";
            return false;
        }

        $this->buf = fgets($this->socket);

        return trim($this->buf);
    }

    /**
     * @return bool|string
     */
    public function getVersion()
    {
        return $this->sendCommand("version");
    }

    /**
     * @param string $key
     */
    public function add(string $key)
    {
        $this->sendCommand(1);
    }

    /**
     * @param string $key
     */
    public function set(string $key)
    {
        $this->sendCommand(1);
    }

    /**
     * @param string $key
     */
    public function delete(string $key)
    {
        $this->sendCommand(1);
    }

    /**
     * @param string $server
     * @param int $port
     * @param string $command
     * @return bool|string
     */
    public static function sendCommandEx(string $command, string $server = "localhost", int $port = 11211)
    {
        $socket = @fsockopen($server, $port);
        stream_set_timeout($socket, 5);
        if (!$socket) {
            echo "Cant connect to:" . $server . ':' . $port;
            return false;
        }

        fwrite($socket, $command . "\r\n");

        $buf = fgets($socket, 256);
        fclose($socket);

        return trim($buf);
    }
}
