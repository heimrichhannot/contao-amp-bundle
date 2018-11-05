<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Environment;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\Template;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class HookListener implements FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    public function getPageLayout(PageModel $page, LayoutModel &$layout, PageRegular $pageRegular)
    {
        if ($layout->addAmp && $this->container->get('huh.request')->getGet('amp') && null !== ($ampLayout = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $layout->ampLayout))) {
            $layout = $ampLayout;

            global $objPage;

            $objPage->layout = $layout->id;
        }
    }

    public function parseTemplate(Template $template)
    {
        global $objPage;

        if (null === ($layout = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $objPage->layout)) ||
            !$this->container->get('huh.amp.util.layout_util')->isAmpLayout($layout->id)) {
            return;
        }

        $util = $this->container->get('huh.amp.util.amp_util');

        $templateName = $util->removeTrailingAmp($template->getName());

        // prepare template data for amp
        switch ($templateName) {
            case 'ce_player':
                $files = [];

                if (\is_array($template->files)) {
                    foreach ($template->files as $file) {
                        $files[] = [
                            'mime' => $file->mime,
                            'path' => Environment::get('url').'/'.$file->path,
                            'title' => $file->title,
                        ];
                    }

                    $template->files = $files;
                }

                break;

            case 'ce_accordionSingle':
                $data = $template->getData();

                $this->container->get('huh.utils.accordion')->structureAccordionSingle($data);

                $template->setData($data);

                break;

            case 'ce_accordionStart':
            case 'ce_accordionStop':
                $data = $template->getData();

                $this->container->get('huh.utils.accordion')->structureAccordionStartStop($data);

                $template->setData($data);

                break;

            case 'slick_default':
                $this->prepareSlick($template);

                break;

            case 'nav_default':
                $this->prepareNavItems($template);

                break;

            default:
                // TODO HOOK
                break;
        }

        if ($util->isSupportedUiElement($templateName)) {
            $libsToLoad = [];
            $ampName = $util->getAmpNameByUiElement($templateName);

            // add the needed lib to the manager for fe_page.html5
            switch ($ampName) {
                case 'player':
                    // custom logic for Contao's hybrid media element
                    if ($template->isVideo) {
                        $libsToLoad[] = 'video';
                    } else {
                        $libsToLoad[] = 'audio';
                    }

                    break;

                case 'navigation':
                    // custom logic for Contao's navigation element
                    $libsToLoad[] = 'sidebar';
                    $libsToLoad[] = 'accordion';

                    break;

                default:
                    if ($ampName) {
                        $libsToLoad[] = $ampName;
                    }

                    // TODO HOOK
                    break;
            }

            foreach ($libsToLoad as $lib) {
                if (false !== ($url = $util->getLibraryByAmpName($lib))) {
                    $this->container->get('huh.amp.manager.amp_manager')::addLib($lib, $url);
                }
            }

            // switch template for amp
            $template->setName($templateName.'_amp');
        }
    }

    public function generatePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (!$this->container->get('huh.amp.util.layout_util')->isAmpLayout($layout->id)) {
            return;
        }

        $ampUtil = $this->container->get('huh.amp.util.amp_util');

        // add analytics support
        if ($layout->addAmpAnalytics) {
            $this->container->get('huh.amp.manager.amp_manager')::addLib('analytics', $ampUtil->getLibraryByAmpName('analytics'));

            $pageRegular->Template->ampAnalytics = $this->container->get('twig')->render(
                $this->container->get('huh.utils.template')->getTemplate($layout->ampAnalyticsTemplate),
                [
                    'skip' => $ampUtil->skipAnalyticsForBackend(),
                ]
            );
        }

        // encore
        if ($this->container->get('huh.utils.container')->isBundleActive('HeimrichHannot\EncoreBundle\HeimrichHannotContaoEncoreBundle')) {
            $this->container->get('huh.encore.listener.hooks')->doAddEncore($page, $layout, $pageRegular, 'encoreEntriesAmp', true);
        }
    }

    public function modifyFrontendPage(string $buffer, string $template)
    {
        global $objPage;

        if (!$this->container->get('huh.amp.util.layout_util')->isAmpLayout($objPage->layout)) {
            return $buffer;
        }

        // add needed amp libs
        if (!empty($this->container->get('huh.amp.manager.amp_manager')::getLibs())) {
            $scripts = [];

            foreach ($this->container->get('huh.amp.manager.amp_manager')::getLibs() as $ampName => $url) {
                $scripts[] = '<script async custom-element="amp-'.$ampName.'" src="'.$url.'"></script>';
            }

            if (!empty($scripts)) {
                return str_replace('<!-- ##ampScripts## -->', implode("\n", $scripts), $buffer);
            }
        }

        return $buffer;
    }

    public function prepareSlick(Template $template)
    {
        if (!$this->container->get('huh.utils.container')->isBundleActive('HeimrichHannot\SlickBundle\HeimrichHannotContaoSlickBundle')) {
            return;
        }

        if (!\is_array($template->body)) {
            return;
        }

        $images = [];
        $template->ampCarouselWidth = 0;
        $template->ampCarouselHeight = 0;

        foreach ($template->body as $item) {
            if (!$item->addImage) {
                continue;
            }

            if (isset($item->picture['sources']) && !empty($item->picture['sources'])) {
                $template->sourcesMode = true;

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

                if (!$template->ampCarouselWidth || $item->picture['img']['width'] > $template->ampCarouselWidth) {
                    $template->ampCarouselWidth = $item->picture['img']['width'];
                }

                if (!$template->ampCarouselHeight || $item->picture['img']['height'] > $template->ampCarouselHeight) {
                    $template->ampCarouselHeight = $item->picture['img']['height'];
                }
            }
        }

        $template->ampImages = $images;
    }

    public function prepareNavItems(Template $template)
    {
        // update active property
        $items = $template->items;

        if (!\is_array($items)) {
            return;
        }

        global $objPage;

        foreach ($items as &$item) {
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

        $template->items = $items;
    }
}
