<?php

/*
 * This file is part of the YurinskiyValidators package.
 *
 * (c) Yuriy Yurinskiy <yuriyyurinskiy@yandex.ru>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Yurinskiy\Validator\Tests\Constraints;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;
use Yurinskiy\Validator\Constraints\Inn;

/**
 * @internal
 */
final class InnTest extends TestCase
{
    public function testAttributes(): void
    {
        $metadata = new ClassMetadata(InnDummy::class);
        $loader = $this->createAnnotationLoader();

        self::assertTrue($loader->loadClassMetadata($metadata));

        [$aConstraint] = $metadata->getPropertyMetadata('a')[0]->getConstraints();
        self::assertInstanceOf(Inn::class, $aConstraint);
        self::assertNull($aConstraint->type);

        [$bConstraint] = $metadata->getPropertyMetadata('b')[0]->getConstraints();
        self::assertInstanceOf(Inn::class, $bConstraint);
        self::assertSame(Inn::INDIVIDUALS, $bConstraint->type);
        self::assertSame('myMessageLength', $bConstraint->messageLength);
        self::assertSame('myMessageDigits', $bConstraint->messageDigits);
        self::assertSame('myMessageStructureForForeign', $bConstraint->messageStructureForForeign);
        self::assertSame('myMessageControlNumber', $bConstraint->messageControlNumber);
        self::assertSame(['Default', 'InnDummy'], $bConstraint->groups);

        [$cConstraint] = $metadata->getPropertyMetadata('c')[0]->getConstraints();
        self::assertInstanceOf(Inn::class, $cConstraint);
        self::assertSame(Inn::BUSINESSES, $cConstraint->type);
        self::assertSame(['my_group'], $cConstraint->groups);
        self::assertSame('some attached data', $cConstraint->payload);
    }

    protected function createAnnotationLoader(): LoaderInterface
    {
        // @phpstan-ignore-next-line
        return class_exists(AttributeLoader::class)
            ? new AttributeLoader()
            : new AnnotationLoader();
    }
}

class InnDummy
{
    #[Inn]
    public string $a;

    #[Inn(
        type: Inn::INDIVIDUALS,
        messageLength: 'myMessageLength',
        messageDigits: 'myMessageDigits',
        messageStructureForForeign: 'myMessageStructureForForeign',
        messageControlNumber: 'myMessageControlNumber'
    )]
    public string $b;

    #[Inn(type: Inn::BUSINESSES, groups: ['my_group'], payload: 'some attached data')]
    public string $c;
}
