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
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @see https://ru.wikipedia.org/wiki/Идентификационный_номер_налогоплательщика#Вычисление_контрольных_цифр
 */
class InnValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $constraint instanceof Inn) {
            throw new UnexpectedTypeException($constraint, Inn::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (! \is_scalar($value) && ! $value instanceof \Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;
        $length = mb_strlen($value);

        // the inn must be either 10 or 12 digits long
        if (
            ! \in_array($length, [10, 12], true)
            || ($constraint->type === Inn::INDIVIDUALS && $length !== 12)
            || ($length !== 10)
        ) {
            $this->context->buildViolation($constraint->messageLength)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Inn::INVALID_LENGTH_ERROR)
                ->addViolation()
            ;

            return;
        }

        // must contain digit values only
        if (! ctype_digit($value)) {
            $this->context->buildViolation($constraint->messageDigits)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Inn::INVALID_DIGITS_ERROR)
                ->addViolation()
            ;

            return;
        }

        // must contain digit values only
        if ($constraint->type === Inn::FOREIGN_ORGANIZATIONS && ! str_starts_with('9099', $value)) {
            $this->context->buildViolation($constraint->messageStructureForForeign)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Inn::INVALID_STRUCTURE_FOR_FOREIGN_ORGANIZATIONS_ERROR)
                ->addViolation()
            ;

            return;
        }

        // must contain the correct control number
        if (! self::checkControlNumber($value)) {
            $this->context->buildViolation($constraint->messageControlNumber)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Inn::INVALID_CONTROL_NUMBER_ERROR)
                ->addViolation()
            ;
        }
    }

    private static function checkControlNumber(string $inn): bool
    {
        $length = mb_strlen($inn);

        if (! \in_array($length, [10, 12], true)) {
            return false;
        }

        $callable = static function (string $value, array $weights, string $control): bool {
            $result = 0;

            foreach ($weights as $index => $weight) {
                $result += $value[$index] * $weight;
            }
            $result %= 11;
            $result %= 10;

            return $control === (string) $result;
        };

        return match ($length) {
            10 => $callable($inn, [2, 4, 10, 3, 5, 9, 4, 6, 8], $inn[9]),
            12 => $callable($inn, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8], $inn[10])
                && $callable($inn, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8], $inn[11]),
        };
    }
}
