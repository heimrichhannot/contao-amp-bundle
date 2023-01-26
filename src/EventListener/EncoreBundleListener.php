<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\StringUtil;
use HeimrichHannot\AmpBundle\Util\LayoutUtil;
use HeimrichHannot\EncoreBundle\Asset\EntrypointCollectionFactory;
use HeimrichHannot\EncoreBundle\Asset\GlobalContaoAsset;
use HeimrichHannot\EncoreBundle\Asset\TemplateAssetGenerator;
use HeimrichHannot\EncoreBundle\Event\EncoreEnabledEvent;
use MatthiasMullie\Minify\CSS;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class EncoreBundleListener implements EventSubscriberInterface, ServiceSubscriberInterface
{
    private ContainerInterface $container;
    private RequestStack       $requestStack;
    private LayoutUtil         $layoutUtil;

    public function __construct(ContainerInterface $container, RequestStack $requestStack, LayoutUtil $layoutUtil)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->layoutUtil = $layoutUtil;
    }

    public function onEncoreEnabledEvent(EncoreEnabledEvent $event): void
    {
        if ($this->layoutUtil->isAmpActive()) {
            $event->setEnabled(false);
        }
    }

    /**
     * @Hook("generatePage", priority=-1)
     */
    public function onGeneratePage(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        if (!$layout->addAmp || !class_exists(TemplateAssetGenerator::class) || !$this->container->has(TemplateAssetGenerator::class) || !$this->container->has(EntrypointCollectionFactory::class)) {
            return;
        }

        /** @var EntrypointCollectionFactory $collectionFactory */
        $collectionFactory = $this->container->get(EntrypointCollectionFactory::class);
        /** @var TemplateAssetGenerator $assetGenerator */
        $assetGenerator = $this->container->get(TemplateAssetGenerator::class);

        $collection = $collectionFactory->createCollection(StringUtil::deserialize($layout->encoreEntries, true));

        $pageRegular->Template->encoreStylesheetsInline = $this->cleanInlineStyles($assetGenerator->inlineCssLinkTag($collection));
    }

    /**
     * @Hook("replaceDynamicScriptTags")
     */
    public function onReplaceDynamicScriptTags(string $buffer): string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request || !$request->query->has('amp')) {
            return $buffer;
        }

        if (class_exists(GlobalContaoAsset::class) && $this->container->has(GlobalContaoAsset::class)) {
            $this->container->get(GlobalContaoAsset::class)->cleanGlobalArrayFromConfiguration();
        }

        return $buffer;
    }

    public static function getSubscribedServices(): array
    {
        $services = [];

        if (class_exists(GlobalContaoAsset::class)) {
            $services[] = '?'.GlobalContaoAsset::class;
        }

        if (class_exists(TemplateAssetGenerator::class)) {
            $services[] = '?'.TemplateAssetGenerator::class;
        }

        if (class_exists(EntrypointCollectionFactory::class)) {
            $services[] = '?'.EntrypointCollectionFactory::class;
        }

        return $services;
    }

    public static function getSubscribedEvents(): array
    {
        $events = [];

        if (class_exists(EncoreEnabledEvent::class)) {
            $events[EncoreEnabledEvent::class] = 'onEncoreEnabledEvent';
        }

        return $events;
    }

    private function cleanInlineStyles(string $styles): string
    {
        $styles = preg_replace('/@charset ".*?";/m', '', $styles);
        $minifier = new CSS();

        return $minifier->add($styles)->minify();
    }
}
