<?php

namespace Tarantool\Request;

abstract class Request
{
    const TYPE_OK = 0;
    const TYPE_SELECT = 1;
    const TYPE_INSERT = 2;
    const TYPE_REPLACE = 3;
    const TYPE_UPDATE = 4;
    const TYPE_DELETE = 5;
    const TYPE_CALL = 6;
    const TYPE_AUTHENTICATE = 7;
    const TYPE_EVALUATE = 8;
    const TYPE_PING = 64;
    const TYPE_JOIN = 65;
    const TYPE_SUBSCRIBE = 66;
    const TYPE_ERROR = 32768;

    protected $sync = 0;

    public function getSync()
    {
        return $this->sync;
    }

    abstract public function getType();
    abstract public function getBody();
}