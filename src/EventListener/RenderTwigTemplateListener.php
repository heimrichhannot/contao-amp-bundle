<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use HeimrichHannot\AmpBundle\Event\PrepareAmpTemplateEvent;
use HeimrichHannot\UtilsBundle\Event\RenderTwigTemplateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RenderTwigTemplateListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, EventDispatcherInterface $eventDispatcher)
    {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function onRenderTemplate(RenderTwigTemplateEvent $event, string $eventName, EventDispatcherInterface $dispatcher)
    {
        global $objPage;

        if (null == $objPage || null === ($layout = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $objPage->layout))
            || !$this->container->get('huh.amp.util.layout_util')->isAmpLayout($layout->id)) {
            return;
        }

        $util = $this->container->get('huh.amp.util.amp_util');
        $context = $event->getContext();
        $template = $util->removeTrailingAmp($event->getTemplate());

        $context = $this->prepareBaseContext($template, $context);

        if ($util->isSupportedUiElement($template)) {
            $componentsToLoad = $util->getComponentsByTemplateName($template);

            /** @var PrepareAmpTemplateEvent $prepareAmpTemplateEvent */
            $prepareAmpTemplateEvent = $this->eventDispatcher->dispatch(
                PrepareAmpTemplateEvent::NAME,
                new PrepareAmpTemplateEvent($template, $context, $componentsToLoad, $layout)
            );
            $componentsToLoad = $prepareAmpTemplateEvent->getComponentsToLoad();
            $context = $prepareAmpTemplateEvent->getContext();
            $template = $prepareAmpTemplateEvent->getTemplate();

            foreach ($componentsToLoad as $lib) {
                if ($url = $util->getComponentUrlByAmpName($lib)) {
                    $this->container->get('huh.amp.manager.amp_manager')::addLib($lib, $url);
                }
            }

            if (!$util->isAmpTemplate($template)) {
                if (false !== ($extensionStart = strpos($template, '.'))) {
                    // ignore template extension
                    $name = substr($template, 0, $extensionStart);
                    $extension = substr($template, $extensionStart, \strlen($template));
                    $event->setTemplate($name.'_amp'.$extension);
                } else {
                    $event->setTemplate($template.'_amp');
                }
            }
        }
        $event->setContext($context);
    }

    /**
     * Prepare slick context.
     *
     * @param array $context
     *
     * @return array
     */
    public function prepareSlickContext(array $context = []): array
    {
        if (!$this->container->get('huh.utils.container')->isBundleActive('HeimrichHannot\SlickBundle\HeimrichHannotContaoSlickBundle')) {
            return $context;
        }

        if (!\is_array($context['body'])) {
            return $context;
        }

        $images = [];
        $context['ampCarouselWidth'] = 0;
        $context['ampCarouselHeight'] = 0;

        foreach ($context['body'] as $item) {
            if (!$item['addImage']) {
                continue;
            }

            if (isset($item['picture']['sources']) && !empty($item['picture']['sources'])) {
                $context['sourcesMode'] = true;

                foreach ($item->picture['sources'] as $source) {
                    if (!isset($images[$source['media']])) {
                        $images[$source['media']] = [
                            'width' => $source['width'],
                            'height' => $source['height'],
                            'images' => [],
                        ];
                    }

                    $images[$source['media']]['images'][] = $source;
                }
            } elseif (isset($item->picture['img'])) {
                $images[] = $item->picture['img'];

                if (!$context['ampCarouselWidth'] || $item['picture']['img']['width'] > $context['ampCarouselWidth']) {
                    $context['ampCarouselWidth'] = $item['picture']['img']['width'];
                }

                if (!$context['ampCarouselHeight'] || $item['picture']['img']['height'] > $context['ampCarouselHeight']) {
                    $context['ampCarouselHeight'] = $item['picture']['img']['height'];
                }
            }
        }

        $context['ampImages'] = $images;

        return $context;
    }

    /**
     * Prepare nav item context.
     *
     * @param array $context
     *
     * @return array
     */
    public function prepareNavItemsContext(array $context = []): array
    {
        if (!\is_array($context['items'])) {
            return $context;
        }

        global $objPage;

        foreach ($context['items'] as &$item) {
            $trail = \in_array($item['id'], $objPage->trail);

            if (($objPage->id == $item['id'] || ('forward' == $item['type'] && $objPage->id == $item['jumpTo'])) && $item['href'] == $this->container->get('huh.utils.url')->removeQueryString(['amp'], \Environment::get('request'))) {
                // Mark active forward pages (see #4822)
                $strClass = (('forward' == $item['type'] && $objPage->id == $item['jumpTo']) ? 'forward'.($trail ? ' trail' : '') : 'active').(('' != $item['subitems']) ? ' submenu' : '').($item['protected'] ? ' protected' : '').(('' != $item['cssClass']) ? ' '.$item['cssClass'] : '');

                $item['isActive'] = true;
                $item['isTrail'] = false;
            } // Regular page
            else {
                $strClass = (('' != $item['subitems']) ? 'submenu' : '').($item['protected'] ? ' protected' : '').($trail ? ' trail' : '').(('' != $item['cssClass']) ? ' '.$item['cssClass'] : '');

                // Mark pages on the same level (see #2419)
                if ($item['pid'] == $objPage->pid) {
                    $strClass .= ' sibling';
                }

                $item['isActive'] = false;
                $item['isTrail'] = $trail;
            }

            $item['class'] = trim($strClass);
        }

        return $context;
    }

    /**
     * Prepare context for default elements.
     *
     * @param string $template
     * @param array  $context
     *
     * @return array
     */
    protected function prepareBaseContext(string $template, array $context = []): array
    {
        // prepare template data for amp
        switch ($template) {
            case 'ce_player':
                $files = [];

                if (\is_array($context['files'])) {
                    foreach ($context['files'] as $file) {
                        $files[] = [
                            'mime' => $file->mime,
                            'path' => $this->container->get('request_stack')->getCurrentRequest()->getUriForPath($file->path),
                            'title' => $file->title,
                        ];
                    }

                    $context['files'] = $files;
                }

                break;

            case 'ce_accordionSingle':
                $this->container->get('huh.utils.accordion')->structureAccordionSingle($context);

                break;

            case 'ce_accordionStart':
            case 'ce_accordionStop':
                $this->container->get('huh.utils.accordion')->structureAccordionStartStop($context);

                break;

            case 'slick_default':
                $context = $this->prepareSlickContext($context);

                break;

            case 'nav_default':
                $context = $this->prepareNavItemsContext($context);

                break;
        }

        return $context;
    }
}
