<?php

namespace App\Event;

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderCreatedEvent extends Event
{
    const NAME = 'order.created';

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}