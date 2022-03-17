<?php

namespace Squingla\Test\Collections;

use PHPUnit\Framework\TestCase;

trait TestTrait
{
    protected abstract function getTestInstance(): TestCase;
}