<?php


namespace App\Helpers;


class Date
{

    /**
     * @param \DateTime $last
     * @param \DateTime $first
     * @return int
     */
    public static function diffDateTimeToSecond(\DateTime $last , \DateTime $first)
    {
        return $last->getTimestamp() - $first->getTimestamp();

//        $seconds = Date::diffDateTimeToSecond(new \DateTime(), new \DateTime();)
    }

}