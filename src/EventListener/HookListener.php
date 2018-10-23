<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
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

    public function parseTemplate(Template $template)
    {
        global $objPage;

        if (null === ($layout = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $objPage->layout)) ||
            !$layout->addAmp) {
            return;
        }

        // prepare values for amp
        switch ($template->getName()) {
            case 'ce_image':
                break;

            default:
                break;
        }

        $util = $this->container->get('huh.amp.util.amp_util');

        // switch template for amp
        if ($util->isSupportedContentElement($template->getName())) {
            $ampName = $util->getAmpNameByContentElement($template->getName());

            // add the needed lib to the manager for fe_page.html5
            if (false !== ($url = $util->getLibraryByAmpName($ampName))) {
                $this->container->get('huh.amp.manager.amp_manager')::addLib($ampName, $url);
            }

            $template->setName($template->getName().'_amp');
        }
    }

    public function generatePage(PageModel $page, LayoutModel $layout, PageRegular $pageRegular)
    {
        if (!$layout->addAmp) {
            return;
        }

        if (!empty($this->container->get('huh.amp.manager.amp_manager')::getLibs())) {
            $scripts = [];

            foreach ($this->container->get('huh.amp.manager.amp_manager')::getLibs() as $ampName => $url) {
                $scripts[] = '<script async custom-element="amp-'.$ampName.'" src="'.$url.'"></script>';
            }

            if (!empty($scripts)) {
                $pageRegular->Template->ampScripts = implode("\n", $scripts);
            }
        }
    }
}
