<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use Contao\Template;
use HeimrichHannot\TwigSupportBundle\EventListener\RenderListener;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ParseTemplateListener
{
    /**
     * @var array
     */
    private $bundleConfig;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ParseTemplateListener constructor.
     */
    public function __construct(array $bundleConfig, ContainerInterface $container)
    {
        $this->bundleConfig = $bundleConfig;
        $this->container = $container;
    }

    /**
     * @Hook("parseTemplate")
     *
     * @param Template $template
     */
    public function onParseTemplate($template): void
    {
        global $objPage;

        if (null === ($layout = $this->container->get('huh.utils.model')->findModelInstanceByPk('tl_layout', $objPage->layout))
            || !$this->container->get('huh.amp.util.layout_util')->isAmpLayout($layout->id)) {
            return;
        }

        $util = $this->container->get('huh.amp.util.amp_util');

        $templateName = $util->removeTrailingAmp($template->getName());

        if ('twig_template_proxy' === $templateName) {
            $templateName = $template->{RenderListener::TWIG_TEMPLATE};
        }

        if ($util->isSupportedUiElement($templateName)) {
            if (!$this->bundleConfig['templates'][$templateName]['amp_template']) {
                // switch template for amp
                $template->setName($templateName.'_amp');
            }
        } elseif (!$this->container->get('huh.utils.string')->startsWith($templateName, 'fe_page')) {
            $template->setName('amp_template_not_supported');

            if ($this->container->get('huh.utils.container')->isDev()) {
                $template->ampOriginTemplateName = $templateName;
            }
        }
    }
}
