<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\AmpBundle\Manager\AmpManager;
use HeimrichHannot\AmpBundle\Util\AmpUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

class GeneratePageListener
{
    /**
     * @var AmpUtil
     */
    private $ampUtil;
    /**
     * @var AmpManager
     */
    private $ampManager;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * GeneratePageListener constructor.
     */
    public function __construct(ContainerInterface $container, AmpManager $ampManager, AmpUtil $ampUtil, Environment $twig)
    {
        $this->ampUtil = $ampUtil;
        $this->ampManager = $ampManager;
        $this->twig = $twig;
        $this->container = $container;
    }

    public function onGeneratePage(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        if (!$layout->addAmp) {
            return;
        }

        // add analytics support
        if ($layout->addAmpAnalytics) {
            $this->ampManager::addLib('analytics', $this->ampUtil->getComponentUrlByAmpName('analytics'));

            $pageRegular->Template->ampAnalytics = $this->twig->render(
                $this->container->get('huh.utils.template')->getTemplate($layout->ampAnalyticsTemplate),
                [
                    'skip' => $this->ampUtil->skipAnalyticsForBackend(),
                ]
            );
        }

        // encore
        if ($this->container->has('huh.encore.asset.template')) {
            $templateAssets = $this->container->get('huh.encore.asset.template')->createInstance($pageModel, $layout);
            $pageRegular->Template->encoreStylesheetsInline = preg_replace('/@charset ".*?";/m', '', $templateAssets->inlineCssLinkTag());
        }
    }
}
