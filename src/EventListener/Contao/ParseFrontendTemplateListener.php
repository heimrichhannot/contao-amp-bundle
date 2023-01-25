<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener\Contao;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\FrontendTemplate;
use HeimrichHannot\AmpBundle\AmpLibrary\SvgImgTagTransformPass;
use HeimrichHannot\AmpBundle\Util\LayoutUtil;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Hook("parseFrontendTemplate")
 */
class ParseFrontendTemplateListener
{
    private LayoutUtil            $layoutUtil;
    private ParameterBagInterface $parameterBag;

    public function __construct(LayoutUtil $layoutUtil, ParameterBagInterface $parameterBag)
    {
        $this->layoutUtil = $layoutUtil;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(string $buffer, string $templateName, FrontendTemplate $template): string
    {
        if (!$this->layoutUtil->isAmpActive()) {
            return $buffer;
        }

        if (!$this->parameterBag->has('huh_amp')) {
            return $buffer;
        }

        $bundleConfig = $this->parameterBag->get('huh_amp');

        if (\in_array(
            $template,
            array_keys($bundleConfig['templates'])) && (true === $bundleConfig['templates'][$template]['convert_html'])
        ) {
            if (!class_exists('\Lullabot\AMP\AMP')) {
                trigger_error('huh_amp.templates.[template].convert_html is set, but necassary Library lullabot/amp is not installed. HTML Code could not be converted to AMP-HTML code.',
                    \E_USER_NOTICE);

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

        return $buffer;
    }
}
