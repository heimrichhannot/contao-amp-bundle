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

    protected static $accordionCache = [];
    protected static $accordionCacheBuilt = false;

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

        // prepare values for amp
        switch ($template->getName()) {
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
                if (!static::$accordionCacheBuilt) {
                    if (null !== ($elements = $this->container->get('huh.utils.model')->findModelInstancesBy('tl_content', [
                            'ptable=?',
                            'tl_content.pid=?',
                            'invisible!=1',
                        ], [
                            'tl_article',
                            $template->pid,
                        ], [
                            'order' => 'sorting ASC',
                        ]))) {
                        $lastOneIsAccordionSingle = false;
                        $elementGroup = [];

                        foreach ($elements as $i => $element) {
                            if ('accordionSingle' === $element->type) {
                                $elementGroup[] = $element->row();
                            }

                            if ('accordionSingle' !== $element->type) {
                                if ($lastOneIsAccordionSingle) {
                                    static::$accordionCache[] = $elementGroup;
                                    $elementGroup = [];
                                }

                                $lastOneIsAccordionSingle = false;

                                continue;
                            }

                            $lastOneIsAccordionSingle = true;

                            if ($i === \count($elements) - 1) {
                                static::$accordionCache[] = $elementGroup;
                                $elementGroup = [];
                            }
                        }

                        static::$accordionCacheBuilt = true;
                    }
                }

                foreach (static::$accordionCache as $elementGroup) {
                    foreach ($elementGroup as $i => $element) {
                        if ($template->id == $element['id']) {
                            if (0 === $i) {
                                $template->first = true;
                            }

                            if ($i === \count($elementGroup) - 1) {
                                $template->last = true;
                            }

                            break 2;
                        }
                    }
                }

                break;

            default:
                // TODO HOOK
                break;
        }

        $util = $this->container->get('huh.amp.util.amp_util');

        if ($util->isSupportedContentElement($template->getName())) {
            $ampTemplateName = $util->getAmpNameByContentElement($template->getName());

            // add the needed lib to the manager for fe_page.html5
            switch ($ampTemplateName) {
                case 'player':
                    // custom logic for Contao's hybrid media element
                    if ($template->isVideo) {
                        $ampTemplateName = 'video';
                    } else {
                        $ampTemplateName = 'audio';
                    }

                    break;

                default:
                    // TODO HOOK
                    break;
            }

            if (false !== ($url = $util->getLibraryByAmpName($ampTemplateName))) {
                $this->container->get('huh.amp.manager.amp_manager')::addLib($ampTemplateName, $url);
            }

            // switch template for amp
            $template->setName($template->getName().'_amp');
        }
    }

    public function generatePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (!$this->container->get('huh.amp.util.layout_util')->isAmpLayout($layout->id)) {
            return;
        }

        // add needed amp libs
        if (!empty($this->container->get('huh.amp.manager.amp_manager')::getLibs())) {
            $scripts = [];

            foreach ($this->container->get('huh.amp.manager.amp_manager')::getLibs() as $ampName => $url) {
                $scripts[] = '<script async custom-element="amp-'.$ampName.'" src="'.$url.'"></script>';
            }

            if (!empty($scripts)) {
                $pageRegular->Template->ampScripts = implode("\n", $scripts);
            }
        }

        // add analytics support
        if ($layout->addAmpAnalytics) {
            $pageRegular->Template->ampAnalytics = $this->container->get('twig')->render(
                $this->container->get('huh.utils.template')->getTemplate($layout->ampAnalyticsTemplate),
                [
                    'skip' => $this->container->get('huh.amp.util.amp_util')->skipAnalyticsForBackend(),
                ]
            );
        }
    }
}
