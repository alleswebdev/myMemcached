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

        while (!feof($this->socket)) {
            $this->buf .= fgets($this->socket, 256);
            if (strpos($this->buf, "END\r\n") !== false) {
                break;
            }
            if (strpos($this->buf, "DELETED\r\n") !== false || strpos($this->buf, "NOT_FOUND\r\n") !== false) {
                break;
            }
            if (strpos($this->buf, "OK\r\n") !== false) {
                break;
            }
            if (strpos($this->buf, "STORED\r\n") !== false) {
                break;
            }
            if (strpos($this->buf, "VERSION ") !== false) {
                break;
            }
            if (strpos($this->buf, "NOT_FOUND") !== false) {
                break;
            }
            if (strpos($this->buf, "DELETED") !== false) {
                break;
            }
        }

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
     * @return bool|string
     */
    public function stat()
    {
        return $this->sendCommand("stats");
    }

    /**
     * @param string $key
     * @param string $value
     * @param int $exp
     * @return bool
     */
    public function set(string $key, string $value, int $exp = 3600)
    {
        $command = "set " . $key . " 0 " . $exp . ' ' . strlen($value) . "\r\n";
        $command .= $value;
        return "STORED" === $this->sendCommand($command);
    }

    /**
     * @param string $key
     * @return array|bool
     */
    public function get(string $key)
    {
        $command = "get " . $key;
        $output = $this->sendCommand($command);
        if (strpos($output, "VALUE ") === false) {
            echo "key not found" . PHP_EOL;
            return false;
        }
        return $this->parseKey($output);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key)
    {
        return "DELETED" === $this->sendCommand("delete " . $key);
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

    /**
     * @param string $data
     * @return array|bool
     */
    protected function parseKey(string $data)
    {
        $expArr = explode("\r\n", $data);
        $args = explode(" ", $expArr[0]);
        if ($expArr[2] !== "END") {
            echo "something wrong" . PHP_EOL;
            return false;
        }

        return [
            "key" => $args[1],
            "flag" => $args[2],
            "size" => $args[3],
            "value" => $expArr[1],
        ];
    }
}
