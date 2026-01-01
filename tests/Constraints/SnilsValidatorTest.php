<?php

namespace Yurinskiy\Validator\Tests\Constraints;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;
use Yurinskiy\Validator\Constraints\Snils;
use Yurinskiy\Validator\Constraints\SnilsValidator;
use Yurinskiy\Validator\Tests\Constraints\Fixtures\StringableValue;

class SnilsValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new SnilsValidator();
    }

    public function testNullIsValid(): void
    {
        $this->validator->validate(null, new Snils());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid(): void
    {
        $this->validator->validate('', new Snils());

        $this->assertNoViolation();
    }

    public function testInvalidConstraint(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate('neko', new NotNull());
    }

    public function testExpectsStringCompatibleType(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new class {}, new Snils());
    }

    /**
     * @dataProvider getValidNumbers
     */
    public function testValidNumbers(string|\Stringable $number): void
    {
        $this->validator->validate($number, new Snils());

        $this->assertNoViolation();
    }

    /**
     * @dataProvider getInvalidNumbers
     */
    public function testInvalidNumbers(string|\Stringable $number, string $message, string $code): void
    {
        $this->validator->validate($number, new Snils($message, $message, $message));

        $this->buildViolation($message)
            ->setParameter('{{ value }}', \is_string($number) ? '"'.$number.'"' : $number)
            ->setCode($code)
            ->assertRaised();
    }

    /**
     * @return array<array<string|\Stringable>>
     */
    public static function getValidNumbers(): array
    {
        return [
            ['880-384-583 42'],
            ['118-076-854-69'],
            ['27556897848'],
            [new StringableValue('54730462081')],
        ];
    }

    /**
     * @return array<string, array<string|\Stringable>>
     */
    public static function getInvalidNumbers(): array
    {
        return [
            'WRONG_LENGTH' => ['5119231234', 'Некорректный СНИЛС "{{ value }}".', Snils::INVALID_LENGTH_ERROR],
            'NO_DIGITS' => ['2476979O638', 'Некорректный СНИЛС "{{ value }}".', Snils::INVALID_DIGITS_ERROR],
            'JUST_INVALID' => ['51192312347', 'Некорректный СНИЛС "{{ value }}".', Snils::INVALID_CONTROL_NUMBER_ERROR],
            'ONE_CHAR_ERROR' => ['24769790638', 'Некорректный СНИЛС "{{ value }}".', Snils::INVALID_CONTROL_NUMBER_ERROR],
        ];
    }
}
