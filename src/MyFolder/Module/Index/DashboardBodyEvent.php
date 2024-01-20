<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class DashboardBodyEvent extends Event
{
    const NAME = 'dashboard_body.event';

    protected $cards = array();

    public function registerCard(CardInterface $card)
    {
        $this->cards[] = $card;
    }
    public function getCards()
    {
        return $this->cards;
    }
}
