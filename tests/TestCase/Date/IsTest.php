<?php
declare(strict_types=1);

/**
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Cake\Chronos\Test\TestCase\Date;

use Cake\Chronos\ChronosDate;
use Cake\Chronos\Test\TestCase\TestCase;

class IsTest extends TestCase
{
    public function testIsTodayTrue()
    {
        $this->assertTrue(ChronosDate::now()->isToday());
    }

    public function testIsTodayOtherTimezone()
    {
        $this->withTimezone('Asia/Tokyo', function () {
            $today = ChronosDate::today();
            $this->assertSame('Asia/Tokyo', $today->tzName);
            $this->assertTrue($today->isToday());
        });
    }

    public function testIsTodayFalseWithYesterday()
    {
        $this->assertFalse(ChronosDate::now()->subDays(1)->endOfDay()->isToday());
    }
}
