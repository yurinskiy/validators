<?php

/*
 * This file is part of the YurinskiyValidators package.
 *
 * (c) Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yurinskiy\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validates that a value is a valid Individual insurance account number (SNILS).
 * Individual insurance account number (SNILS) is a number issued and used by the Pension Fund of the Russian Federation
 * to residents of Russia for the purpose of tracking their social security accounts.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @author Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Snils extends Constraint
{
    public const INVALID_DIGITS_ERROR = '3c9dc6f3-de39-4001-8e08-3a6c923ac05e';
    public const INVALID_LENGTH_ERROR = '6bf25f8d-6cba-4999-8507-0ad8d1805900';
    public const INVALID_CONTROL_NUMBER_ERROR = '17fe7972-8516-498c-8e99-0f1056f8c742';

    protected const ERROR_NAMES = [
        self::INVALID_DIGITS_ERROR => 'INVALID_CHARACTERS_ERROR',
        self::INVALID_LENGTH_ERROR => 'INVALID_LENGTH_ERROR',
        self::INVALID_CONTROL_NUMBER_ERROR => 'INVALID_CONTROL_NUMBER_ERROR',
    ];

    public string $messageDigits = 'This is not a valid SNILS.';
    public string $messageLength = 'This is not a valid SNILS. It should have 11 digits.';
    public string $messageControlNumber = 'This is not a valid SNILS. Wrong control number.';

    public function __construct(
        ?string $messageDigits = null,
        ?string $messageLength = null,
        ?string $messageControlNumber = null,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);

        $this->messageDigits = $messageDigits ?? $this->messageDigits;
        $this->messageLength = $messageLength ?? $this->messageLength;
        $this->messageControlNumber = $messageControlNumber ?? $this->messageControlNumber;
    }
}
