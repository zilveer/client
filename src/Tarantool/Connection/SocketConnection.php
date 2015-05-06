<?php

namespace Tarantool\Connection;

use Tarantool\Exception\ConnectionException;
use Tarantool\Iproto;

class SocketConnection implements Connection
{
    const DEFAULT_HOST = 'localhost';
    const DEFAULT_PORT = 3301;

    private $host;
    private $port;
    private $socket;

    /**
     * @param string|null  $host Default to 'localhost'.
     * @param int|null     $port Default to 3301.
     */
    public function __construct($host = null, $port = null)
    {
        $this->host = null === $host ? self::DEFAULT_HOST : $host;
        $this->port = null === $port ? self::DEFAULT_PORT : $port;
    }

    public function connect()
    {
        $this->disconnect();

        if (false === $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) {
            throw new ConnectionException(sprintf(
                'Unable to create socket: %s.', socket_strerror(socket_last_error())
            ));
        }

        if (false === @socket_connect($socket, $this->host, $this->port)) {
            throw new ConnectionException(sprintf(
                'Unable to connect: %s.', socket_strerror(socket_last_error($socket))
            ));
        }

        $this->socket = $socket;

        $greeting = socket_read($socket, Iproto::GREETING_SIZE);

        return substr(base64_decode(substr($greeting, 64, 44)), 0, 20);
    }

    public function disconnect()
    {
        if ($this->socket) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }

    public function isConnected()
    {
        return null !== $this->socket;
    }

    public function send($data)
    {
        if (!$this->socket) {
            $this->connect();
        }

        $count = socket_write($this->socket, $data, strlen($data));

        $length = socket_read($this->socket, 5);
        $length = unpack('Ctype/Nlength', $length);

        $data = socket_read($this->socket, $length['length']);

        return $data;
    }
}