<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener\Contao;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\AmpBundle\Manager\AmpManager;
use HeimrichHannot\AmpBundle\Util\AmpUtil;
use HeimrichHannot\EncoreBundle\Asset\TemplateAsset;
use HeimrichHannot\TwigSupportBundle\Renderer\TwigTemplateRenderer;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * @Hook("generatePage")
 */
class GeneratePageListener implements ServiceSubscriberInterface
{
    private AmpManager           $ampManager;
    private TwigTemplateRenderer $renderer;
    private AmpUtil              $ampUtil;
    private ContainerInterface   $container;

    public function __construct(ContainerInterface $container, AmpManager $ampManager, TwigTemplateRenderer $renderer, AmpUtil $ampUtil)
    {
        $this->ampManager = $ampManager;
        $this->renderer = $renderer;
        $this->ampUtil = $ampUtil;
        $this->container = $container;
    }

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        if (!$layout->addAmp) {
            return;
        }

        // add analytics support
        if ($layout->addAmpAnalytics) {
            $this->ampManager::addLib('analytics', $this->ampUtil->getComponentUrlByAmpName('analytics'));
            $pageRegular->Template->ampAnalytics = $this->renderer->render(
                $layout->ampAnalyticsTemplate,
                ['skip' => $this->ampUtil->skipAnalyticsForBackend()]
            );
        }
    }

    public static function getSubscribedServices()
    {
        $services = [];

        if (class_exists(TemplateAsset::class)) {
            $services[] = '?'.TemplateAsset::class;
        }

        return $services;
    }
}
