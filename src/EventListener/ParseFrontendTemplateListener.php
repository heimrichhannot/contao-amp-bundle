<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use Contao\Controller;
use Contao\Environment;
use Contao\LayoutModel;
use Contao\PageModel;
use HeimrichHannot\AmpBundle\AmpLibrary\SvgImgTagTransformPass;
use Lullabot\AMP\AMP;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ParseFrontendTemplateListener
{
    /**
     * @var array
     */
    private $bundleConfig;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(array $bundleConfig, ContainerInterface $container)
    {
        $this->bundleConfig = $bundleConfig;
        $this->container = $container;
    }

    /**
     * @Hook("parseFrontendTemplate")
     */
    public function onParseFrontendTemplate(string $buffer, string $template): string
    {
        /* @var PageModel $objPage */
        global $objPage;
        $layout = LayoutModel::findByPk($objPage->layout);

        if (!$layout || !$layout->addAmp) {
            return $buffer;
        }

        if (\in_array($template, array_keys($this->bundleConfig['templates'])) && (true === $this->bundleConfig['templates'][$template]['convert_html'])) {
            if (!class_exists('\Lullabot\AMP\AMP')) {
                trigger_error('huh_amp.templates.[template].convert_html is set, but necassary Library lullabot/amp is not installed. HTML Code could not be converted to AMP-HTML code.',
                    E_USER_NOTICE);

                return $buffer;
            }
            $rootUrl = Environment::get('url');

            $buffer = Controller::replaceInsertTags($buffer);
            $amp = new AMP();
            $amp->passes[] = SvgImgTagTransformPass::class;
            $amp->loadHtml($buffer, [
                'base_url_for_relative_path' => $rootUrl,
                'server_url' => $rootUrl,
            ]);
            $buffer = $amp->convertToAmpHtml();
            $components = $amp->getComponentJs();
            $diff = $amp->getInputOutputHtmlDiff();
            $warning = $amp->warningsHumanText();
        }

        return $buffer;
    }
}
