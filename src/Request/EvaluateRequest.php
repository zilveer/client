<?php

/**
 * This file is part of the Tarantool Client package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tarantool\Client\Request;

use Tarantool\Client\Keys;
use Tarantool\Client\RequestTypes;

final class EvaluateRequest implements Request
{
    private $expr;
    private $args;

    public function __construct(string $expr, array $args = [])
    {
        $this->expr = $expr;
        $this->args = $args;
    }

    public function getType() : int
    {
        return RequestTypes::EVALUATE;
    }

    public function getBody() : array
    {
        return [
            Keys::EXPR => $this->expr,
            Keys::TUPLE => $this->args,
        ];
    }
}
