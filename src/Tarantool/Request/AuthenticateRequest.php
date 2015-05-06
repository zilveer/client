<?php

namespace Tarantool\Request;

use Tarantool\Iproto;

class AuthenticateRequest extends Request
{
    private $salt;
    private $username;
    private $password;

    public function __construct($salt, $username, $password)
    {
        $this->salt = $salt;
        $this->username = $username;
        $this->password = $password;
    }

    public function getType()
    {
        return self::TYPE_AUTHENTICATE;
    }

    public function getBody()
    {
        $hash1 = sha1($this->password, true);
        $hash2 = sha1($hash1, true);
        $scramble = sha1($this->salt.$hash2, true);
        $scramble = self::strxor($hash1, $scramble);

        return [
            Iproto::TUPLE => ['chap-sha1', $scramble],
            Iproto::USER_NAME => $this->username,
        ];
    }

    private static function strxor($rhs, $lhs)
    {
        $result = '';

        for ($i = 0; $i < strlen($rhs); $i++) {
            $result .= chr(ord($rhs[$i]) ^ ord($lhs[$i]));
        }

        return $result;
    }
}