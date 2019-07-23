<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use HeimrichHannot\AmpBundle\Event\PrepareAmpTemplateEvent;

class PrepareAmpTemplateListener
{
    public function onHuhAmpEventPrepareAmpTemplate(PrepareAmpTemplateEvent $event)
    {
        switch ($event->getTemplate()) {
            case 'ce_player':
                $this->preparePlayerContentElement($event);

                break;
        }
    }

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
}
