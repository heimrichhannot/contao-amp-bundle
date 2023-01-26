<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\EventListener\Contao;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use Contao\System;
use HeimrichHannot\AmpBundle\Manager\AmpManager;
use HeimrichHannot\AmpBundle\Util\LayoutUtil;
use HeimrichHannot\HeadBundle\Manager\HtmlHeadTagManager;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook("getPageLayout")
 */
class GetPageLayoutListener
{
    private LayoutUtil         $layoutUtil;
    private RequestStack       $requestStack;
    private HtmlHeadTagManager $headTagManager;
    private AmpManager         $ampManager;
    private Utils              $utils;

    public function __construct(LayoutUtil $layoutUtil, RequestStack $requestStack, HtmlHeadTagManager $headTagManager, AmpManager $ampManager, Utils $utils)
    {
        $this->layoutUtil = $layoutUtil;
        $this->requestStack = $requestStack;
        $this->headTagManager = $headTagManager;
        $this->ampManager = $ampManager;
        $this->utils = $utils;
    }

    public function __invoke(PageModel $pageModel, LayoutModel &$layout, PageRegular $pageRegular): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request || null == ($ampLayout = $this->layoutUtil->getAmpLayoutForCurrentPage($pageModel))) {
            return;
        }

        if ($request->query->has('amp')) {
            $layout = $ampLayout;
            $layout->setRow($ampLayout->row());

            $pageModel->layout = $layout->id;
            $this->headTagManager->setBaseTag('/');
            $this->ampManager->setAmpActive(true);

            return;
        }

        System::getContainer()->get('huh.head.tag.link_amp')->setContent(
            Controller::addToUrl('amp=1'.($this->utils->container()->isDev() ? '#development=1' : ''))
        );
    }
}
