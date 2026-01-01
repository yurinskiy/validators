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
 * @see https://ru.wikipedia.org/wiki/Контрольное_число#Страховой_номер_индивидуального_лицевого_счёта_(Россия)
 *
 * @author Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 */
class SnilsValidator extends ConstraintValidator
{
    private const CORRECT_LENGTH = 11;

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Snils) {
            throw new UnexpectedTypeException($constraint, Snils::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) preg_replace('/[-\s]/', '', (string) $value);
        $length = mb_strlen($value);

        if (self::CORRECT_LENGTH !== $length) {
            $this->context->buildViolation($constraint->messageLength)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Snils::INVALID_LENGTH_ERROR)
                ->addViolation()
            ;

            return;
        }

        // must contain digit values only
        if (!ctype_digit($value)) {
            $this->context->buildViolation($constraint->messageDigits)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Snils::INVALID_DIGITS_ERROR)
                ->addViolation()
            ;

            return;
        }

        // must contain the correct control number
        if (!self::checkControlNumber($value)) {
            $this->context->buildViolation($constraint->messageControlNumber)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Snils::INVALID_CONTROL_NUMBER_ERROR)
                ->addViolation()
            ;
        }
    }

    private static function checkControlNumber(string $value): bool
    {
        /** @var int[] $digits */
        $digits = str_split($value);

        if (self::CORRECT_LENGTH !== \count($digits)) {
            return false;
        }

        $result = 0;
        $actual = implode('', \array_slice($digits, -2, 2));

        $weights = [9, 8, 7, 6, 5, 4, 3, 2, 1];
        foreach ($weights as $index => $weight) {
            $result += $digits[$index] * (int) $weight;
        }
        $result %= 101;
        $result %= 100;

        return $actual === (string) $result;
    }
}
