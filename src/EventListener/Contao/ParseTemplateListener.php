<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener\Contao;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Template;
use HeimrichHannot\AmpBundle\Event\PrepareAmpTemplateEvent;
use HeimrichHannot\AmpBundle\FrontendModule\AmpNavigationModule;
use HeimrichHannot\AmpBundle\Manager\AmpManager;
use HeimrichHannot\AmpBundle\Util\AmpUtil;
use HeimrichHannot\AmpBundle\Util\LayoutUtil;
use HeimrichHannot\SlickBundle\HeimrichHannotContaoSlickBundle;
use HeimrichHannot\TwigSupportBundle\EventListener\RenderListener;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ParseTemplateListener
{
    private AmpUtil                  $ampUtil;
    private LayoutUtil               $layoutUtil;
    private Utils                    $utils;
    private RequestStack             $requestStack;
    private EventDispatcherInterface $eventDispatcher;
    private AmpManager               $ampManager;

    public function __construct(
        AmpUtil $ampUtil,
        LayoutUtil $layoutUtil,
        Utils $utils,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher,
        AmpManager $ampManager
    ) {
        $this->ampUtil = $ampUtil;
        $this->layoutUtil = $layoutUtil;
        $this->utils = $utils;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->ampManager = $ampManager;
    }

    /**
     * @Hook("parseTemplate")
     */
    public function __invoke(Template $template): void
    {
        if (!$this->layoutUtil->isAmpActive()) {
            return;
        }

        $templateName = $template->getName();
        $templateContext = $template->getData();
        $twigProxy = false;

        if (str_starts_with($templateName, 'fe_page')) {
            return;
        }

        // Twig support bundle support
        if ('twig_template_proxy' === $templateName && class_exists(RenderListener::class)) {
            $templateName = $template->{RenderListener::TWIG_TEMPLATE};
            $templateContext = $template->{RenderListener::TWIG_CONTEXT};
            $twigProxy = true;
        }

        $templateName = $this->ampUtil->removeTrailingAmp($templateName);

        if ($this->ampUtil->isSupportedUiElement($templateName)) {
            $componentsToLoad = $this->ampUtil->getComponentsByTemplateName($templateName);
            $componentsToLoad = $this->prepareDynamicComponents($templateName, $templateContext, $componentsToLoad);
            $templateContext = $this->prepareBaseContext($templateName, $templateContext);

            if (!$this->ampUtil->isAmpTemplate($templateName)) {
                $templateName = $templateName.'_amp';
            }

            $layout = $this->layoutUtil->getAmpLayoutForCurrentPage();

            /** @var PrepareAmpTemplateEvent $prepareAmpTemplateEvent */
            $prepareAmpTemplateEvent = $this->eventDispatcher->dispatch(
                new PrepareAmpTemplateEvent($templateName, $templateContext, $componentsToLoad, $layout),
                PrepareAmpTemplateEvent::NAME
            );

            $prepareAmpTemplateEvent = $this->eventDispatcher->dispatch(
                $prepareAmpTemplateEvent,
                PrepareAmpTemplateEvent::class
            );

            $componentsToLoad = $prepareAmpTemplateEvent->getComponentsToLoad();
            $templateContext = $prepareAmpTemplateEvent->getContext();
            $templateName = $prepareAmpTemplateEvent->getTemplate();

            foreach ($componentsToLoad as $lib) {
                if ($url = $this->ampUtil->getComponentUrlByAmpName($lib)) {
                    $this->ampManager::addLib($lib, $url);
                }
            }
        } else {
            if ($this->utils->container()->isDev()) {
                $templateContext['ampOriginTemplateName'] = $templateName;
            }

            $templateName = 'amp_template_not_supported';
            $twigProxy = false;
        }

        if ($twigProxy) {
            $template->{RenderListener::TWIG_TEMPLATE} = $templateName;
            $template->{RenderListener::TWIG_CONTEXT} = $templateContext;
        } else {
            $template->setName($templateName);
            $template->setData($templateContext);
        }
    }

    /**
     * Prepare slick context.
     */
    public function prepareSlickContext(array $context = []): array
    {
        if (!class_exists(HeimrichHannotContaoSlickBundle::class)) {
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
     */
    public function prepareNavItemsContext(array $context = []): array
    {
        return $context;

        if (!\is_array($context['items'])) {
            return $context;
        }

        global $objPage;

        $currentUrl = $this->utils->url()->makeUrlRelative($this->utils->url()->removeQueryStringParameter('amp'));

        foreach ($context['items'] as &$item) {
            $trail = \in_array($item['id'], $objPage->trail);

            if (($objPage->id == $item['id'] || ('forward' == $item['type'] && $objPage->id == $item['jumpTo'])) && $item['href'] == $currentUrl) {
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
     * @Hook("parseTemplate", priority=100)
     */
    public function prepareNavigationModule(Template $template): void
    {
        if (!$this->layoutUtil->isAmpActive() || !str_starts_with($template->getName(), 'nav_')) {
            return;
        }

        /** @var AmpNavigationModule $module */
        $module = $template->module;

        if (!$module || !$module instanceof AmpNavigationModule) {
            return;
        }
        $template->moduleData = $module->getModel()->row();

        $context = $template->getData();

        foreach ($context['items'] as &$item) {
            $item['href'] = $this->utils->url()->addQueryStringParameterToUrl('amp=1', $item['href']).($this->utils->container()->isDev() ? '#development=1' : '');
        }

        $template->setData($context);
    }

    /**
     * Prepare context for default elements.
     */
    private function prepareBaseContext(string $template, array $context = []): array
    {
        // prepare template data for amp
        switch ($template) {
            case 'ce_player':
                $files = [];

                if (\is_array($context['files'])) {
                    foreach ($context['files'] as $file) {
                        $files[] = [
                            'mime' => $file->mime,
                            'path' => $this->requestStack->getCurrentRequest()->getUriForPath($file->path),
                            'title' => $file->title,
                        ];
                    }

                    $context['files'] = $files;
                }

                break;

            case 'ce_accordionSingle':
                $this->utils->accordion()->structureAccordionSingle($context);

                break;

            case 'ce_accordionStart':
            case 'ce_accordionStop':
                $this->utils->accordion()->structureAccordionStartStop($context);

                break;

            case 'slick_default':
                $context = $this->prepareSlickContext($context);

                break;

                break;
        }

        return $context;
    }

    private function prepareDynamicComponents(string $templateName, array $templateContext, array $componentsToLoad): array
    {
        switch ($templateName) {
            case 'ce_player':
                if ($templateContext['isVideo']) {
                    $componentsToLoad[] = 'video';
                } else {
                    $componentsToLoad[] = 'audio';
                }
        }

        return $componentsToLoad;
    }
}
