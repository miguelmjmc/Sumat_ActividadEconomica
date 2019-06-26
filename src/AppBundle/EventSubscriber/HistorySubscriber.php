<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\HistorySession;
use AppBundle\Entity\HistoryRequest;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class HistorySubscriber implements EventSubscriberInterface
{
    /**
     * @var  EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;


    public function __construct(EntityManagerInterface $entityManager, TokenStorage $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onLoginSuccess',
            KernelEvents::RESPONSE => 'onKernelResponse',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        );
    }

    public function onLoginSuccess(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();

        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        $history = (new HistorySession())
            ->setDateLogin(new \DateTime())
            ->setDateLogout(new \DateTime())
            ->setIp($request->getClientIp())
            ->setUser($user);

        $this->entityManager->persist($history);

        $this->entityManager->flush();
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->tokenStorage->getToken() instanceof TokenInterface) {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof User && $event->isMasterRequest()) {
                if (false === strpos($event->getRequest()->getPathInfo(), '/_wdt/')) {
                    $history = (new HistoryRequest())
                        ->setDate(new \DateTime())
                        ->setUri($event->getRequest()->getPathInfo())
                        ->setMethod($event->getRequest()->getMethod())
                        ->setStatusCode($event->getResponse()->getStatusCode())
                        ->setIp($event->getRequest()->getClientIp())
                        ->setUser($user);

                    $this->entityManager->persist($history);

                    $this->entityManager->flush();
                }
            }
        }
    }

    public function onKernelTerminate(KernelEvent $kernelEvent)
    {
        if ($this->tokenStorage->getToken() instanceof TokenInterface) {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof User && $kernelEvent->isMasterRequest()) {
                $history = $this->entityManager->getRepository(HistorySession::class)->findBy(
                    array('user' => $user),
                    array('dateLogin' => 'DESC'),
                    1
                );

                if (isset($history[0])) {
                    /** @var HistorySession $lastHistory */
                    $lastHistory = $history[0];

                    $lastHistory->setDateLogout(new \DateTime());

                    $this->entityManager->flush();
                }
            }
        }
    }

    /*
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($this->tokenStorage->getToken()) {
            foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
                if (HistoryResolver::encodeTargetEntity(get_class($entity))) {
                    $history = new OperationHistory();

                    $history->setDate(new \DateTime())
                        ->setUser($this->tokenStorage->getToken()->getUser())
                        ->setTargetEntity(HistoryResolver::encodeTargetEntity(get_class($entity)))
                        ->setOperationType(1);

                    $em->persist($history);
                    $classMetadata = $em->getClassMetadata(OperationHistory::class);
                    $uow->computeChangeSet($classMetadata, $history);
                }
            }

            foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
                if (HistoryResolver::encodeTargetEntity(get_class($entity))) {
                    $history = new OperationHistory();

                    if ($entity instanceof User) {
                        $uow->getEntityChangeSet($entity);

                        $changes = $uow->getEntityChangeSet($entity);

                        if (array_key_exists('lastLogin', $changes) || array_key_exists(
                                'confirmationToken',
                                $changes
                            ) || array_key_exists('passwordRequestedAt', $changes)) {
                            continue;
                        }
                    }

                    $history->setDate(new \DateTime())
                        ->setUser($this->tokenStorage->getToken()->getUser())
                        ->setTargetEntity(HistoryResolver::encodeTargetEntity(get_class($entity)))
                        ->setOperationType(2);

                    $em->persist($history);
                    $classMetadata = $em->getClassMetadata(OperationHistory::class);
                    $uow->computeChangeSet($classMetadata, $history);
                }
            }

            foreach ($uow->getScheduledEntityDeletions() as $keyEntity => $entity) {
                if (HistoryResolver::encodeTargetEntity(get_class($entity))) {
                    $history = new OperationHistory();

                    $history->setDate(new \DateTime())
                        ->setUser($this->tokenStorage->getToken()->getUser())
                        ->setTargetEntity(HistoryResolver::encodeTargetEntity(get_class($entity)))
                        ->setOperationType(3);

                    $em->persist($history);
                    $classMetadata = $em->getClassMetadata(OperationHistory::class);
                    $uow->computeChangeSet($classMetadata, $history);
                }
            }
        }
    }
    */
}
