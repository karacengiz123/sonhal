<?php


namespace App\ChatStrategy;


use App\Entity\User;

class LowChatTimeFirst implements ChatStrategyInterFace
{
    public function getChatUser($em)
    {

        return new User();

    }
}