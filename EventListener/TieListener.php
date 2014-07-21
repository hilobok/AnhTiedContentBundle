<?php

namespace Anh\TiedContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Anh\TiedContentBundle\Entity\PaperTie;
use Symfony\Component\DependencyInjection\ContainerAware;

class TieListener extends ContainerAware implements EventSubscriber
{
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::preRemove
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof PaperTie || $entity->getParent() !== null) {
            return;
        }

        $repository = $this->container->get('anh_tied_content.tie.repository');
        $children = $repository->fetch(array(
            'parent' => $entity->getChild()
        ));

        if ($children) {
            $manager = $this->container->get('anh_tied_content.tie.manager');
            $manager->delete($children, false);
        }
    }
}
