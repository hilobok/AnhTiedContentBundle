<?php

namespace Anh\TiedContentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Anh\ContentBundle\Entity\Paper;
use Anh\TiedContentBundle\Entity\PaperTie;
use Anh\TiedContentBundle\Entity\PaperTieManager;

class TieListener implements EventSubscriber
{
    /**
     * PaperTie manager
     *
     * @var PaperTieManager
     */
    private $tieManager;

    /**
     * Constructor
     *
     * @param PaperTieManager $tieManager
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

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

        if (!$entity instanceof Paper) {
            return;
        }

        $tieManager = $this->container->get('anh_tied_content.manager.tie');

        // delete children
        $ties = $tieManager->findChildren($entity);
        foreach ($ties as $tie) {
            $tieManager->delete($tie->getChild(), false);
            $tieManager->delete($tie, false);
        }

        // delete tie
        $tie = $tieManager->getTie($entity);
        if ($tie) {
            $tieManager->delete($tie, false);
        }
    }
}
