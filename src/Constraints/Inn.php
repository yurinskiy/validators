<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yurinskiy\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Validates that a value is a valid Taxpayer Personal Identification Number - INN (Russian analog for TIN).
 * The INN refers for all types of taxes in the Russian Federation.
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Inn extends Constraint
{
    public const INDIVIDUALS = 'individuals';
    public const BUSINESSES = 'businesses';
    public const FOREIGN_ORGANIZATIONS = 'foreign_organizations';

    public const TYPES = [
        self::INDIVIDUALS,
        self::BUSINESSES,
        self::FOREIGN_ORGANIZATIONS,
    ];

    public const INVALID_DIGITS_ERROR = '3c9dc6f3-de39-4001-8e08-3a6c923ac05e';
    public const INVALID_LENGTH_ERROR = '6bf25f8d-6cba-4999-8507-0ad8d1805900';
    public const INVALID_STRUCTURE_FOR_FOREIGN_ORGANIZATIONS_ERROR = '901bf2ca-c1bd-417d-828e-53036a6a6841';
    public const INVALID_CONTROL_NUMBER_ERROR = '17fe7972-8516-498c-8e99-0f1056f8c742';

    protected const ERROR_NAMES = [
        self::INVALID_DIGITS_ERROR => 'INVALID_CHARACTERS_ERROR',
        self::INVALID_LENGTH_ERROR => 'INVALID_LENGTH_ERROR',
        self::INVALID_STRUCTURE_FOR_FOREIGN_ORGANIZATIONS_ERROR => 'INVALID_STRUCTURE_FOR_FOREIGN_ORGANIZATIONS_ERROR',
        self::INVALID_CONTROL_NUMBER_ERROR => 'INVALID_CONTROL_NUMBER_ERROR',
    ];

    public ?string $type = null;

    public string $messageDigits = 'This is not a valid INN.';
    public string $messageLength;
    public string $messageControlNumber = 'This is not a valid INN. Wrong control number.';
    public string $messageStructureForForeign = 'This is not a valid INN for foreign organizations. It should start with "9099".';

    public function __construct(
        ?string $type = null,
        ?string $messageLength = null,
        ?string $messageDigits = null,
        ?string $messageStructureForForeign = null,
        ?string $messageControlNumber = null,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        $this->type = $type ?? $this->type;

        if ($this->type && ! \in_array($this->type, self::TYPES, true)) {
            throw new ConstraintDefinitionException(\sprintf('The option "type" must be one of "%s".', implode('", "', self::TYPES)));
        }

        $this->messageLength = $messageLength ?? match ($this->type) {
            self::INDIVIDUALS => 'This is not a valid INN for individuals. It should have twelve-digit code.',
            self::BUSINESSES => 'This is not a valid INN for businesses. It should have ten-digit code.',
            self::FOREIGN_ORGANIZATIONS => 'This is not a valid INN for foreign organizations. It should have ten-digit code.',
            default => 'This is not a valid INN. It should have 10 or 12 digits.',
        };
        $this->messageDigits = $messageDigits ?? $this->messageDigits;
        $this->messageStructureForForeign = $messageStructureForForeign ?? $this->messageStructureForForeign;
        $this->messageControlNumber = $messageControlNumber ?? $this->messageControlNumber;
    }
}
