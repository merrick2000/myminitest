<?php

namespace App\EventListener;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderCreatedListener implements EventSubscriberInterface
{
    //EntityManager
    protected $em;
    protected $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
       
    }

    public static function getSubscribedEvents()
    {
        return [
            'order.created' => 'onOrderCreated',
        ];
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function onOrderCreated($event): void
    {
        $entity = $event->getOrder();
        $user = $entity->getPurchasedBy();
        $email = (new TemplatedEmail())
                ->from(new Address('merrick@myminitest.com', 'Order Completed | Merrick MyMiniTest'))
                ->to($user->getEmail())
                ->subject("Order Completet | Merrick MyMiniTest")
                ->htmlTemplate('mail/order-success.html.twig')
            ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
           
        }
    }
}