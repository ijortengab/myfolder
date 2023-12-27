<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class DashboardBodyEvent extends Event
{
    const NAME = 'dashboard_body.event';

    protected static $instance;

    protected $cards = array();

    public static function load()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function registerCard(CardInterface $card)
    {
        $this->cards[] = $card;
    }

    public function getCards()
    {
        return $this->cards;
    }
}
