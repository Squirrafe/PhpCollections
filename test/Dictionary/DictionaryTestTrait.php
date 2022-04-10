<?php

namespace Squingla\Test\Collections\Dictionary;

use Squingla\Collections\Dictionary\Dictionary;
use Squingla\Collections\Dictionary\Tuple\Tuple;
use Squingla\Collections\NoSuchElementException;
use Squingla\Test\Collections\TestTrait;

trait DictionaryTestTrait
{
    use TestTrait;

    /**
     * @template K
     * @template V
     * @param array{K,V}[] $elements
     * @return Dictionary<K,V>
     */
    protected abstract function getInstanceWithElements(array $elements): Dictionary;

    /** @dataProvider gettersDataProvider */
    public function testGet(array $input, $invalidKey): void
    {
        $dictionary = $this->getInstanceWithElements($input);

        foreach ($input as [$key, $value]) {
            self::assertSame($value, $dictionary->get($key));
        }

        $this->getTestInstance()->expectException(NoSuchElementException::class);
        $dictionary->get($invalidKey);
    }

    /** @dataProvider gettersDataProvider */
    public function testArrayAccess(array $input, $invalidKey): void
    {
        $dictionary = $this->getInstanceWithElements($input);

        foreach ($input as [$key, $value]) {
            self::assertTrue(isset($dictionary[$key]));
            self::assertSame($value, $dictionary[$key]);
        }

        self::assertFalse(isset($dictionary[$invalidKey]));
        $this->getTestInstance()->expectException(NoSuchElementException::class);
        $dictionary[$invalidKey];
    }

    /** @dataProvider gettersDataProvider */
    public function testInvoke(array $input, $invalidKey): void
    {
        $dictionary = $this->getInstanceWithElements($input);

        foreach ($input as [$key, $value]) {
            self::assertSame($value, $dictionary($key));
        }

        $this->getTestInstance()->expectException(NoSuchElementException::class);
        $dictionary($invalidKey);
    }

    /** @dataProvider gettersDataProvider */
    public function testGetOption(array $input, $invalidKey): void
    {
        $dictionary = $this->getInstanceWithElements($input);

        foreach ($input as [$key, $value]) {
            $result = $dictionary->getOption($key);
            self::assertTrue($result->nonEmpty());
            self::assertSame($value, $result->get());
        }

        self::assertTrue($dictionary->getOption($invalidKey)->isEmpty());
    }

    /** @dataProvider gettersDataProvider */
    public function testHasKey(array $input, $invalidKey): void
    {
        $dictionary = $this->getInstanceWithElements($input);

        foreach ($input as [$key, $_]) {
            self::assertTrue($dictionary->hasKey($key));
        }

        self::assertFalse($dictionary->hasKey($invalidKey));
    }

    /** @dataProvider gettersDataProvider */
    public function testHasValue(array $input, $invalidKey, $invalidValue): void
    {
        $dictionary = $this->getInstanceWithElements($input);

        foreach ($input as [$_, $value]) {
            self::assertTrue($dictionary->hasValue($value));
        }

        self::assertFalse($dictionary->hasValue($invalidValue));
    }

    /** @dataProvider gettersDataProvider */
    public function testToList(array $input): void
    {
        $dictionary = $this->getInstanceWithElements($input);
        $list = $dictionary->toList();
        self::assertSame(count($input), $list->getLength());

        $tuplesInArray = [];
        /** @var Tuple $tuple */
        foreach ($list as $tuple) {
            self::assertContains([$tuple->getKey(), $tuple->getValue()], $input);
            $tuplesInArray[] = [$tuple->getKey(), $tuple->getValue()];
        }

        foreach ($input as $entry) {
            self::assertContains($entry, $tuplesInArray);
        }
    }

    /** @dataProvider gettersDataProvider */
    public function testKeyList(array $input): void
    {
        $dictionary = $this->getInstanceWithElements($input);
        $keys = $dictionary->keyList();
        self::assertSame(count($input), $keys->getLength());

        $keysInInput = [];
        foreach ($input as [$key, $value]) {
            self::assertContains($key, $keys);
            $keysInInput[] = $key;
        }

        foreach ($keys as $key) {
            self::assertContains($key, $keysInInput);
        }
    }

    /** @dataProvider gettersDataProvider */
    public function testValueList(array $input): void
    {
        $dictionary = $this->getInstanceWithElements($input);
        $values = $dictionary->valueList();
        self::assertSame(count($input), $values->getLength());

        $valuesInInput = [];
        foreach ($input as [$key, $value]) {
            self::assertContains($key, $values);
            $valuesInInput[] = $value;
        }

        foreach ($values as $value) {
            self::assertContains($value, $valuesInInput);
        }
    }

    public function gettersDataProvider(): iterable
    {
        yield [
            'input' => [
                ['key1', 'value1'],
                ['key2', 'value2_repeated'],
                ['key3', 'value2_repeated'],
            ],
            'invalidKey' => 'key4',
            'invalidValue' => 'value4',
        ];

        yield [
            'input' => [
                [13, true],
                [16, false],
                [28, true],
            ],
            'invalidKey' => 55,
            'invalidValue' => 'foo',
        ];

        yield [
            'input' => [
                ['foo', true],
                ['bar', false],
                ['baz', true],
            ],
            'invalidKey' => 'biz',
            'ivalidValue' => 'foo',
        ];

        yield [
            'input' => [],
            'invalidKey' => 'any',
            'invalidValue' => 'any',
        ];

        yield [
            'input' => [
                [['advanced1'], 'foo'],
                [['advanced2'], 'bar'],
            ],
            'invalidKey' => ['advanced3'],
            'invalidValue' => 'biz',
        ];
    }

    /** @dataProvider puttersDataProvider */
    public function testPut(
        array $input,
        $key,
        $value,
        array $expected
    ): void {
        $dictionary = $this->getInstanceWithElements($input);
        $result = $dictionary->put($key, $value);


        self::assertSame(count($expected), $result->getLength());
        foreach ($expected as [$expectedKey, $expectedValue]) {
            self::assertTrue($result->hasKey($expectedKey));
            self::assertSame($expectedValue, $result[$expectedKey]);
        }
    }

    /** @dataProvider puttersDataProvider */
    public function testSet(
        array $input,
        $key,
        $value,
        array $_,
        array $expected
    ): void {
        $dictionary = $this->getInstanceWithElements($input);
        $result = $dictionary->set($key, $value);


        self::assertSame(count($expected), $result->getLength());
        foreach ($expected as [$expectedKey, $expectedValue]) {
            self::assertTrue($result->hasKey($expectedKey));
            self::assertSame($expectedValue, $result[$expectedKey]);
        }
    }

    public function puttersDataProvider(): iterable
    {
        yield [
            'input' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3'],
            ],
            'key' => 'key4',
            'value' => 'value4',
            'putResult' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3'],
                ['key4', 'value4'],
            ],
            'setResult' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3'],
                ['key4', 'value4'],
            ],
        ];

        yield [
            'input' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3_old'],
            ],
            'key' => 'key3',
            'value' => 'value3_new',
            'putResult' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3_old'],
            ],
            'setResult' => [
                ['key1', 'value1'],
                ['key2', 'value2'],
                ['key3', 'value3_new'],
            ],
        ];

        yield [
            'input' => [],
            'key' => 'key4',
            'value' => 'value4',
            'putResult' => [
                ['key4', 'value4'],
            ],
            'setResult' => [
                ['key4', 'value4'],
            ],
        ];
    }
}