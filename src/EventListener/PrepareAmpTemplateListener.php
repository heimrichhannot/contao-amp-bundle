<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\AmpBundle\EventListener;


use HeimrichHannot\AmpBundle\Event\PrepareAmpTemplateEvent;

class PrepareAmpTemplateListener
{
    public function onHuhAmpEventPrepareAmpTemplate(PrepareAmpTemplateEvent $event)
    {
        switch ($event->getTemplate())
        {
            case 'ce_player':
                $this->preparePlayerContentElement($event);
                break;
        }
    }

    protected function preparePlayerContentElement(PrepareAmpTemplateEvent $event)
    {
        $componentsToLoad = $event->getComponentsToLoad();
        if ($event->getContext()['isVideo'])
        {
            $componentsToLoad[] = 'video';
        } else
        {
            $componentsToLoad[] = 'audio';
        }
        $event->setComponentsToLoad($componentsToLoad);
        $event->stopPropagation();
    }
}