<?php

namespace AppBundle\Form\Events;

use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;


    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        );
    }

    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var User $data */
        $data = $event->getData();

        if ($form->isValid()) {
            if ('ROLE_ADMIN' === $form['rol']->getData()) {
                $data->addRole('ROLE_ADMIN');
            } elseif ('ROLE_USER' === $form['rol']->getData()) {
                $data->removeRole('ROLE_ADMIN');
            }

            if ($data->getPlainPassword()) {
                $this->userManager->updatePassword($data);
            }
        }
    }
}
