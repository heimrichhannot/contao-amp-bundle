<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
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
        if (null == ($ampLayout = $this->container->get('huh.amp.util.layout_util')->getAmpLayoutForCurrentPage($page))) {
            return;
        }

        if ($this->container->get('huh.request')->getGet('amp')) {
            $layout = $ampLayout;
            $page->layout = $layout->id;
            $this->container->get('huh.head.tag.base')->setContent('/');
            $this->container->get('huh.amp.manager.amp_manager')->setAmpActive(true);

            if (isset($GLOBALS['TL_HOOKS']['generatePage']['huh.encore-bundle'])) {
                unset($GLOBALS['TL_HOOKS']['generatePage']['huh.encore-bundle']);
            }

            return;
        }

        $this->container->get('huh.head.tag.link_amp')->setContent($this->container->get('huh.utils.url')->addQueryString('amp=1'.($this->container->getParameter('kernel.debug') ? '#development=1' : ''), $page->getAbsoluteUrl()));
    }

    public function parseTemplate(Template $template)
    {
        global $objPage;

        if (null === ($layout = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $objPage->layout))
            || !$this->container->get('huh.amp.util.layout_util')->isAmpLayout($layout->id)) {
            return;
        }

        $util = $this->container->get('huh.amp.util.amp_util');

        $templateName = $util->removeTrailingAmp($template->getName());

        if ($util->isSupportedUiElement($templateName)) {
            if (!$this->container->getParameter('huh_amp')['templates'][$templateName]['ampTemplate']) {
                // switch template for amp
                $template->setName($templateName.'_amp');
            }
        }
    }

    public function generatePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (!$layout->addAmp) {
            return;
        }

        $ampUtil = $this->container->get('huh.amp.util.amp_util');

        // add analytics support
        if ($layout->addAmpAnalytics) {
            $this->container->get('huh.amp.manager.amp_manager')::addLib('analytics', $ampUtil->getComponentUrlByAmpName('analytics'));

            $pageRegular->Template->ampAnalytics = $this->container->get('twig')->render(
                $this->container->get('huh.utils.template')->getTemplate($layout->ampAnalyticsTemplate),
                [
                    'skip' => $ampUtil->skipAnalyticsForBackend(),
                ]
            );
        }

        // encore
        if ($this->container->get('huh.utils.container')->isBundleActive('HeimrichHannot\EncoreBundle\HeimrichHannotContaoEncoreBundle')) {
            $layout->encoreEntriesAmp = $layout->encoreEntries;
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
}
