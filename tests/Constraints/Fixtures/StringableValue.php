<?php

/*
 * This file is part of the YurinskiyValidators package.
 *
 * (c) Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yurinskiy\Validator\Tests\Constraints\Fixtures;

class StringableValue
{
    public function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
