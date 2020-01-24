<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\AmpBundle\EventListener;


use Contao\Template;
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
     * @param array $bundleConfig
     * @param ContainerInterface $container
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

        if ($util->isSupportedUiElement($templateName)) {
            if (!$this->bundleConfig['templates'][$templateName]['ampTemplate']) {
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