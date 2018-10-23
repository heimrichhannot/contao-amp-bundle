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

    public function parseTemplate(Template $template)
    {
        global $objPage;

        if (null === ($layout = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $objPage->layout)) ||
            !$layout->addAmp) {
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
