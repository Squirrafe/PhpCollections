<?php

namespace Squingla\Test\Collections\Dictionary;

use Squingla\Collections\Dictionary\Dictionary;
use Squingla\Collections\Dictionary\Tuple\SimpleTuple;
use Squingla\Collections\Dictionary\Tuple\Tuple;
use Squingla\Collections\Lists\ArrayList;
use Squingla\Collections\Optional;
use Squingla\Collections\UnsupportedTraversalException;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

trait DictionaryIterableOnceTestTrait
{
    use DictionaryTestTrait;

    /** @dataProvider basicDataProvider */
    public function testGetLength(array $elements): void
    {
        $instance = $this->getInstanceWithElements($elements);
        self::assertSame(count($elements), $instance->getLength());
    }

    /** @dataProvider basicDataProvider */
    public function testIsEmpty(array $elements): void
    {
        $instance = $this->getInstanceWithElements($elements);
        self::assertSame(count($elements) === 0, $instance->isEmpty());
    }

    /** @dataProvider basicDataProvider */
    public function testNonEmpty(array $elements): void
    {
        $instance = $this->getInstanceWithElements($elements);
        self::assertSame(count($elements) !== 0, $instance->nonEmpty());
    }

    /** @dataProvider basicDataProvider */
    public function testForEach(array $elements): void
    {
        $instance = $this->getInstanceWithElements($elements);
        $result = [];
        $instance->forEach(
            function (Tuple $tuple) use (&$result) {
                $result[] = [$tuple->getKey(), $tuple->getValue()];
            }
        );

        assertSame(count($elements), count($result));
        foreach ($elements as $element) {
            assertTrue(in_array($element, $result));
        }
    }

    public function basicDataProvider(): iterable
    {
        yield ['input' => [
            ['key1', 'value1'],
            ['key2', 'value2'],
            ['key3', 'value3'],
        ]];

        yield ['input' => [
            [13, true],
            [16, false],
            [28, true],
        ]];

        yield ['input' => [
            ['foo', true],
            ['bar', false],
            ['baz', true],
        ]];

        yield ['input' => []];
    }

    /** @dataProvider filterDataProvider */
    public function testFilter(
        array $input,
        callable $lambda,
        array $expectedOutput
    ): void {
        $instance = $this->getInstanceWithElements($input);
        $filtered = $instance->filter($lambda);

        self::assertInstanceOf(Dictionary::class, $filtered);
        self::assertSame(count($expectedOutput), $filtered->getLength());

        foreach ($expectedOutput as [$key, $value]) {
            self::assertTrue($filtered->hasKey($key));
            self::assertSame($value, $filtered[$key]);
        }
    }

    /** @dataProvider filterDataProvider */
    public function testCount(
        array $input,
        callable $lambda,
        array $expectedOutput
    ): void {
        $instance = $this->getInstanceWithElements($input);
        self::assertSame(count($expectedOutput), $instance->count($lambda));
    }

    /**
     * - input
     * - filter lambda
     * - expected result
     */
    public function filterDataProvider(): iterable
    {
        yield [
            [
                [13, true],
                [16, false],
                [28, true],
            ],
            fn (Tuple $tuple) => $tuple->getValue(),
            [
                [13, true],
                [28, true],
            ]
        ];

        yield [
            [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3'],
            ],
            fn (Tuple $tuple) => $tuple->getKey() === 'key2',
            [
                ['key2', 'value2'],
            ]
        ];

        yield [
            [],
            fn (Tuple $tuple) => true,
            [],
        ];
    }

    /** @dataProvider filterNotDataProvider */
    public function testFilterNot(
        array $input,
        callable $lambda,
        array $expectedOutput
    ): void {
        $instance = $this->getInstanceWithElements($input);
        $filtered = $instance->filterNot($lambda);

        self::assertInstanceOf(Dictionary::class, $filtered);
        self::assertSame(count($expectedOutput), $filtered->getLength());

        foreach ($expectedOutput as [$key, $value]) {
            self::assertTrue($filtered->hasKey($key));
            self::assertSame($value, $filtered[$key]);
        }
    }

    /**
     * - input
     * - filter lambda
     * - expected result
     */
    public function filterNotDataProvider(): iterable
    {
        yield [
            [
                [13, true],
                [16, false],
                [28, true],
            ],
            fn (Tuple $tuple) => $tuple->getValue(),
            [
                [16, false],
            ]
        ];

        yield [
            [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3'],
            ],
            fn (Tuple $tuple) => $tuple->getKey() === 'key2',
            [
                ['key1', 'value1'],
                ['key3', 'value3'],
            ]
        ];

        yield [
            [],
            fn (Tuple $tuple) => false,
            [],
        ];
    }

    /** @dataProvider existsDataProvider */
    public function testExists(
        array $input,
        callable $lambda,
        bool $exists
    ): void {
        $instance = $this->getInstanceWithElements($input);
        self::assertSame($exists, $instance->exists($lambda));
    }

    /** @dataProvider existsDataProvider */
    public function testForAll(
        array $input,
        callable $lambda,
        bool $_,
        bool $forAll
    ): void {
        $instance = $this->getInstanceWithElements($input);
        self::assertSame($forAll, $instance->forAll($lambda));
    }

    public function existsDataProvider(): iterable
    {
        yield [
            [
                [13, true],
                [16, false],
                [28, true],
            ],
            fn (Tuple $tuple) => $tuple->getValue(),
            true,
            false,
        ];

        yield [
            [
                [13, true],
                [16, false],
                [28, true],
            ],
            fn (Tuple $tuple) => $tuple->getKey() < 0,
            false,
            false,
        ];

        yield [
            [
                [13, true],
                [16, false],
                [28, true],
            ],
            fn (Tuple $tuple) => $tuple->getKey() > 0,
            true,
            true,
        ];

        yield [
            [],
            fn (Tuple $tuple) => $tuple->getKey() === 0,
            false,
            true,
        ];
    }

    public function testMap(): void
    {
        $input = $this->getInstanceWithElements([
            ['key1', 'value1'],
            ['key2', 'value2'],
            ['key3', 'value3'],
        ]);
        $mapped = $input->map(
            fn (Tuple $t) => $t->getKey().'-'.$t->getValue(),
        );

        $results = [];
        foreach ($mapped as $result) {
            $results[] = $result;
        }

        self::assertSame(
            ['key1-value1', 'key2-value2', 'key3-value3'],
            $results,
        );
    }

    public function testFlatMap(): void
    {
        $input = $this->getInstanceWithElements([
            ['key1', 'value1'],
            ['key2', 'value2'],
            ['key3', 'value3'],
        ]);
        $mapped = $input->flatMap(
            fn (Tuple $t) => ArrayList::with([$t->getKey(), $t->getValue(), $t->getKey().'-'.$t->getValue()]),
        );

        $results = [];
        foreach ($mapped as $result) {
            $results[] = $result;
        }

        self::assertSame(
            [
                'key1',
                'value1',
                'key1-value1',
                'key2',
                'value2',
                'key2-value2',
                'key3',
                'value3',
                'key3-value3',
                ],
            $results,
        );
    }

    public function testFoldLeft(): void
    {
        $input = $this->getInstanceWithElements([
            ['key1', 1],
            ['key2', 4],
            ['key3', 15],
        ]);

        $folded = $input->foldLeft(
            3,
            fn (int $i, Tuple $t) => $i - $t->getValue(),
        );

        self::assertSame(((3 - 1) - 4) - 15, $folded);
    }

    public function testFoldRight(): void
    {
        $input = $this->getInstanceWithElements([
            ['key1', 1],
            ['key2', 4],
            ['key3', 15],
        ]);

        $folded = $input->foldRight(
            3,
            fn (Tuple $t, int $i) => $t->getValue() - $i,
        );

        self::assertSame(1 - (4 - (15 - 3)), $folded);
    }

    /** @dataProvider reduceDataProvider */
    public function testReduceLeftOption(
        array $input,
        callable $reduce,
        Optional $reduceLeft,
        Optional $reduceRight
    ): void {
        $input = $this->getInstanceWithElements($input);
        $reduced = $input->reduceLeftOption($reduce);

        self::assertSame($reduceLeft->isEmpty(), $reduced->isEmpty());
        self::assertSame($reduceLeft->map(fn (Tuple $t) => $t->getKey())->getOrNull(), $reduced->map(fn (Tuple $t) => $t->getKey())->getOrNull());
        self::assertSame($reduceLeft->map(fn (Tuple $t) => $t->getValue())->getOrNull(), $reduced->map(fn (Tuple $t) => $t->getValue())->getOrNull());
    }

    /** @dataProvider reduceDataProvider */
    public function testReduceRightOption(
        array $input,
        callable $reduce,
        Optional $reduceLeft,
        Optional $reduceRight
    ): void {
        $input = $this->getInstanceWithElements($input);
        $reduced = $input->reduceRightOption($reduce);

        self::assertSame($reduceRight->isEmpty(), $reduced->isEmpty());
        self::assertSame($reduceRight->map(fn (Tuple $t) => $t->getKey())->getOrNull(), $reduced->map(fn (Tuple $t) => $t->getKey())->getOrNull());
        self::assertSame($reduceRight->map(fn (Tuple $t) => $t->getValue())->getOrNull(), $reduced->map(fn (Tuple $t) => $t->getValue())->getOrNull());
    }

    /** @dataProvider reduceDataProvider */
    public function testReduceLeft(
        array $input,
        callable $reduce,
        Optional $reduceLeft,
        Optional $reduceRight
    ): void {
        $input = $this->getInstanceWithElements($input);

        if ($reduceLeft->isEmpty()) {
            $this->getTestInstance()->expectException(UnsupportedTraversalException::class);
            $input->reduceLeft($reduce);
        } else {
            $reduced = $input->reduceLeft($reduce);
            self::assertSame($reduceLeft->get()->getKey(), $reduced->getKey());
            self::assertSame($reduceLeft->get()->getValue(), $reduced->getValue());
        }
    }

    /** @dataProvider reduceDataProvider */
    public function testReduceRight(
        array $input,
        callable $reduce,
        Optional $reduceLeft,
        Optional $reduceRight
    ): void {
        $input = $this->getInstanceWithElements($input);

        if ($reduceRight->isEmpty()) {
            $this->getTestInstance()->expectException(UnsupportedTraversalException::class);
            $input->reduceRight($reduce);
        } else {
            $reduced = $input->reduceRight($reduce);
            self::assertSame($reduceRight->get()->getKey(), $reduced->getKey());
            self::assertSame($reduceRight->get()->getValue(), $reduced->getValue());
        }
    }

    public function reduceDataProvider(): iterable
    {
        yield [
            'input' => [
                ['key1', 1],
                ['key2', 4],
                ['key3', 15],
            ],
            'reduce' => fn (Tuple $a, Tuple $b) => new SimpleTuple($a->getKey(), $a->getValue() - $b->getValue()),
            'reduceLeft' => Optional::some(new SimpleTuple('key1', (1 - 4) - 15)),
            'reduceRight' => Optional::some(new SimpleTuple('key1', 1 - (4 - 15))),
        ];
        yield [
            'input' => [
                ['key3', 1],
            ],
            'reduce' => fn (Tuple $a, Tuple $b) => new SimpleTuple($a->getKey(), $a->getValue() - $b->getValue()),
            'reduceLeft' => Optional::some(new SimpleTuple('key3', 1)),
            'reduceRight' => Optional::some(new SimpleTuple('key3', 1)),
        ];
        yield [
            'input' => [],
            'reduce' => fn (Tuple $a, Tuple $b) => new SimpleTuple($a->getKey(), $a->getValue() - $b->getValue()),
            'reduceLeft' => Optional::none(),
            'reduceRight' => Optional::none(),
        ];
    }
}