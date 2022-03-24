<?php

namespace Squingla\Test\Collections\Dictionary;

use Squingla\Collections\Dictionary\Dictionary;
use Squingla\Collections\Dictionary\Tuple\SimpleTuple;
use Squingla\Collections\Dictionary\Tuple\Tuple;
use Squingla\Collections\NoSuchElementException;
use Squingla\Collections\Optional;
use Squingla\Collections\UnsupportedTraversalException;

trait DictionaryCollectionTestTrait
{
    use DictionaryTestTrait;

    /** @dataProvider addingDataProvider */
    public function testAppended(array $input, array $added, array $expected): void
    {
        $dictionary = $this->getInstanceWithElements($input);
        $added = $dictionary->appended(new SimpleTuple($added[0], $added[1]));

        self::assertInstanceOf(Dictionary::class, $added);
        self::assertSame(count($expected), $added->getLength());
        foreach ($expected as [$key, $value]) {
            self::assertTrue($added->hasKey($key));
            self::assertSame($value, $added->get($key));
        }
    }

    /** @dataProvider addingDataProvider */
    public function testPrepended(array $input, array $added, array $expected): void
    {
        $dictionary = $this->getInstanceWithElements($input);
        $added = $dictionary->prepended(new SimpleTuple($added[0], $added[1]));

        self::assertInstanceOf(Dictionary::class, $added);
        self::assertSame(count($expected), $added->getLength());
        foreach ($expected as [$key, $value]) {
            self::assertTrue($added->hasKey($key));
            self::assertSame($value, $added->get($key));
        }
    }

    public function addingDataProvider(): iterable
    {
        yield [
            'input' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
            ],
            'added' => ['key3', 'value3'],
            'result' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3'],
            ],
        ];
        yield [
            'input' => [],
            'added' => ['key3', 'value3'],
            'result' => [
                ['key3', 'value3'],
            ],
        ];
        yield [
            'input' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3_old'],
            ],
            'added' => ['key3', 'value3_new'],
            'result' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3_old'],
            ],
        ];
    }

    /** @dataProvider concatDataProvider */
    public function testConcat(array $input1, array $input2, array $result): void
    {
        $dictionary1 = $this->getInstanceWithElements($input1);
        $dictionary2 = $this->getInstanceWithElements($input2);
        $concat = $dictionary1->concat($dictionary2);

        self::assertInstanceOf(Dictionary::class, $concat);
        self::assertSame(count($result), $concat->getLength());
        foreach ($result as [$key, $value]) {
            self::assertTrue($concat->hasKey($key));
            self::assertSame($value, $concat->get($key));
        }
    }

    public function concatDataProvider(): iterable
    {
        yield [
            'input1' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
            ],
            'input2' => [
                ['key3', 'value3'],
                ['key4', 'value4'],
            ],
            'result' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3'],
                ['key4', 'value4'],
            ],
        ];
        yield [
            'input1' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3_old'],
            ],
            'input2' => [
                ['key3', 'value3_new'],
                ['key4', 'value4'],
            ],
            'result' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3_old'],
                ['key4', 'value4'],
            ],
        ];
    }

    /** @dataProvider deconstructingDataProvider */
    public function testHead(array $input): void
    {
        $keys = array_map(
            fn (array $entry) => $entry[0],
            $input,
        );
        $values = array_map(
            fn (array $entry) => $entry[1],
            $input,
        );
        $dictionary = $this->getInstanceWithElements($input);

        if (count($input) === 0) {
            $this->getTestInstance()->expectException(NoSuchElementException::class);
            $dictionary->head();
        } else {
            /** @var Tuple $head */
            $head = $dictionary->head();
            $keyIndex = array_search($head->getKey(), $keys, true);
            self::assertIsInt($keyIndex);
            self::assertSame($values[$keyIndex], $head->getValue());
        }
    }

    /** @dataProvider deconstructingDataProvider */
    public function testHeadOption(array $input): void
    {
        $keys = array_map(
            fn (array $entry) => $entry[0],
            $input,
        );
        $values = array_map(
            fn (array $entry) => $entry[1],
            $input,
        );
        $dictionary = $this->getInstanceWithElements($input);

        $headOption = $dictionary->headOption();
        if (count($input) === 0) {
            self::assertTrue($headOption->isEmpty());
        } else {
            self::assertFalse($headOption->isEmpty());
            /** @var Tuple $head */
            $head = $headOption->get();
            $keyIndex = array_search($head->getKey(), $keys, true);
            self::assertIsInt($keyIndex);
            self::assertSame($values[$keyIndex], $head->getValue());
        }
    }

    /** @dataProvider deconstructingDataProvider */
    public function testTail(array $input): void
    {
        $dictionary = $this->getInstanceWithElements($input);
        $tail = $dictionary->tail();
        self::assertSame(max(0, count($input) - 1), $tail->getLength());
    }

    /** @dataProvider deconstructingDataProvider */
    public function testDeconstruct(array $input): void
    {
        $keys = array_map(
            fn (array $entry) => $entry[0],
            $input,
        );
        $values = array_map(
            fn (array $entry) => $entry[1],
            $input,
        );
        $dictionary = $this->getInstanceWithElements($input);

        if (count($input) === 0) {
            $this->getTestInstance()->expectException(UnsupportedTraversalException::class);
            $dictionary->deconstruct();
        } else {
            /**
             * @var Tuple $head
             * @var Dictionary $tail
             */
            [$head, $tail] = $dictionary->deconstruct();

            $keyIndex = array_search($head->getKey(), $keys, true);
            self::assertIsInt($keyIndex);
            self::assertSame($values[$keyIndex], $head->getValue());

            self::assertSame(max(0, count($input) - 1), $tail->getLength());

            foreach ($tail as $tuple) {
                $keyIndexOfTail = array_search($tuple->getKey(), $keys, true);
                self::assertIsInt($keyIndexOfTail);
                self::assertSame($values[$keyIndexOfTail], $tuple->getValue());
                self::assertNotSame($keyIndex, $keyIndexOfTail);
            }
        }
    }

    /** @dataProvider deconstructingDataProvider */
    public function testDeconstructOption(array $input): void
    {
        $keys = array_map(
            fn (array $entry) => $entry[0],
            $input,
        );
        $values = array_map(
            fn (array $entry) => $entry[1],
            $input,
        );
        $dictionary = $this->getInstanceWithElements($input);
        /**
         * @var Optional<Tuple> $headOption
         * @var Dictionary $tail
         */
        [$headOption, $tail] = $dictionary->deconstructOption();

        if (count($input) === 0) {
            self::assertTrue($headOption->isEmpty());
            self::assertSame(count($input), $tail->getLength());
        } else {
            self::assertFalse($headOption->isEmpty());
            /** @var Tuple $head */
            $head = $headOption->get();
            $keyIndex = array_search($head->getKey(), $keys, true);
            self::assertIsInt($keyIndex);
            self::assertSame($values[$keyIndex], $head->getValue());
            self::assertSame(max(0, count($input) - 1), $tail->getLength());

            foreach ($tail as $tuple) {
                $keyIndexOfTail = array_search($tuple->getKey(), $keys, true);
                self::assertIsInt($keyIndexOfTail);
                self::assertSame($values[$keyIndexOfTail], $tuple->getValue());
                self::assertNotSame($keyIndex, $keyIndexOfTail);
            }
        }
    }

    /** @dataProvider deconstructingDataProvider */
    public function testToNative(array $input): void
    {
        $keys = array_map(
            fn (array $entry) => $entry[0],
            $input,
        );
        $values = array_map(
            fn (array $entry) => $entry[1],
            $input,
        );

        $dictionary = $this->getInstanceWithElements($input);
        /** @var Tuple[] $nativeArray */
        $nativeArray = $dictionary->toNative();
        self::assertSame(count($input), count($nativeArray));

        foreach ($nativeArray as $tuple) {
            $keyIndex = array_search($tuple->getKey(), $keys, true);
            self::assertIsInt($keyIndex);
            self::assertSame($values[$keyIndex], $tuple->getValue());
        }
    }

    public function deconstructingDataProvider(): iterable
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
}