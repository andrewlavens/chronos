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
namespace Cake\Chronos\Traits;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * A trait for freezing the time aspect of a DateTime.
 *
 * Used in making calendar date objects, both mutable and immutable.
 */
trait FrozenTimeTrait
{
    use RelativeKeywordTrait;

    /**
     * Removes the time components from an input string.
     *
     * Used to ensure constructed objects always lack time.
     *
     * @param \DateTime|\DateTimeImmutable|string|int|null $time The input time. Integer values will be assumed
     *   to be in UTC. The 'now' and '' values will use the current local time.
     * @param \DateTimeZone|null $timezone The timezone in which the date is taken
     * @return string The date component of $time.
     */
    protected function stripTime(DateTime|DateTimeImmutable|string|int|null $time, ?DateTimeZone $timezone): string
    {
        if (is_int($time)) {
            return gmdate('Y-m-d 00:00:00', $time);
        }

        if (is_string($time) && substr($time, 0, 1) === '@') {
            return gmdate('Y-m-d 00:00:00', (int)substr($time, 1));
        }

        if (!($time instanceof DateTimeInterface)) {
            $time = new DateTimeImmutable($time ?? 'now', $timezone);
        }

        return $time->format('Y-m-d 00:00:00');
    }

    /**
     * Remove time components from strtotime relative strings.
     *
     * @param string $time The input expression
     * @return string The output expression with no time modifiers.
     */
    protected function stripRelativeTime(string $time): string
    {
        return preg_replace('/([-+]\s*\d+\s(?:minutes|seconds|hours|microseconds))/', '', $time);
    }

    /**
     * Modify the time on the Date.
     *
     * This method ignores all inputs and forces all inputs to 0.
     *
     * @param int $hours The hours to set (ignored)
     * @param int $minutes The minutes to set (ignored)
     * @param int $seconds The seconds to set (ignored)
     * @param int $microseconds The microseconds to set (ignored)
     * @return static A modified Date instance.
     */
    public function setTime(int $hours, int $minutes, ?int $seconds = null, ?int $microseconds = null): static
    {
        return parent::setTime(0, 0, 0, 0);
    }

    /**
     * Add an Interval to a Date
     *
     * Any changes to the time will be ignored and reset to 00:00:00
     *
     * @param \DateInterval $interval The interval to modify this date by.
     * @return static A modified Date instance
     */
    public function add(DateInterval $interval): static
    {
        return parent::add($interval)->setTime(0, 0, 0);
    }

    /**
     * Subtract an Interval from a Date.
     *
     * Any changes to the time will be ignored and reset to 00:00:00
     *
     * @param \DateInterval $interval The interval to modify this date by.
     * @return static A modified Date instance
     */
    public function sub(DateInterval $interval): static
    {
        return parent::sub($interval)->setTime(0, 0, 0);
    }

    /**
     * No-op method.
     *
     * Timezones have no effect on calendar dates.
     *
     * @param \DateTimeZone|string $value The DateTimeZone object or timezone name to use.
     * @return static
     */
    public function setTimezone(DateTimeZone|string $value): static
    {
        return $this;
    }

    /**
     * Set the timestamp value and get a new object back.
     *
     * This method will discard the time aspects of the timestamp
     * and only apply the date portions
     *
     * @param int $value The timestamp value to set.
     * @return static
     */
    public function setTimestamp(int $value): static
    {
        return parent::setTimestamp($value)->setTime(0, 0, 0);
    }

    /**
     * Overloaded to ignore time changes.
     *
     * Changing any aspect of the time will be ignored, and the resulting object
     * will have its time frozen to 00:00:00.
     *
     * @param string $relative The relative change to make.
     * @return static A new ChronosDate with the applied date changes.
     */
    public function modify(string $relative): static
    {
        if (preg_match('/hour|minute|second/', $relative)) {
            return $this;
        }
        $new = parent::modify($relative);
        if ($new->format('H:i:s') !== '00:00:00') {
            return $new->setTime(0, 0, 0);
        }

        return $new;
    }
}
