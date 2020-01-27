<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use HeimrichHannot\AmpBundle\Event\PrepareAmpTemplateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PrepareAmpTemplateListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onHuhAmpEventPrepareAmpTemplate(PrepareAmpTemplateEvent $event)
    {
        switch ($event->getTemplate()) {
            case 'ce_player':
                $this->preparePlayerContentElement($event);

                break;

            case 'ce_image':

                break;
        }
    }

    /**
     * Prepare ce_player for amp.
     *
     * @param PrepareAmpTemplateEvent $event
     */
    protected function preparePlayerContentElement(PrepareAmpTemplateEvent $event)
    {
        $componentsToLoad = $event->getComponentsToLoad();

        if ($event->getContext()['isVideo']) {
            $componentsToLoad[] = 'video';
        } else {
            $componentsToLoad[] = 'audio';
        }
        $event->setComponentsToLoad($componentsToLoad);
        $event->stopPropagation();
    }

    /**
     * Prepare ce_image for amp.
     *
     * @param PrepareAmpTemplateEvent $event
     */
    protected function prepareImageContentElement(PrepareAmpTemplateEvent $event)
    {
        $context = $event->getContext();
        $data = [];
        $this->container->get('huh.utils.image')->addToTemplateData('singleSRC', 'addImage', $data, $context);
        $event->setContext($data);
        $event->stopPropagation();
    }
}
