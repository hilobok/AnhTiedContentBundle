<?php

namespace Anh\TiedContentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Anh\ContentBundle\Event\GenerateUrlEvent;
use Anh\ContentBundle\Entity\Paper;
use Anh\TiedContentBundle\Entity\PaperTie;

class UrlGenerator implements EventSubscriberInterface
{
    protected $tieManager;

    public function __construct($tieManager)
    {
        $this->tieManager = $tieManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            GenerateUrlEvent::GENERATE_URL => 'onGenerateUrl'
        );
    }

    public function onGenerateUrl(GenerateUrlEvent $event)
    {
        $data = $event->getData();

        if ($data instanceof Paper) {
            $data = $this->tieManager->getTie($data);
        }

        if (!($data instanceof PaperTie)) {
            return;
        }

        $event->setArguments(array(
            'alias' => $data->getParent() === null ? 'tied_parent' : 'tied_child',
            'section' => $data->getChild()->getSection(),
            'parameters' => $data->getUrlParameters()
        ));

        $event->stopPropagation();
    }
}