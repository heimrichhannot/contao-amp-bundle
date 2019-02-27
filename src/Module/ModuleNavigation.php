<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Module;

use Contao\FrontendTemplate;
use Contao\ModuleSitemap;
use Contao\PageModel;
use Contao\System;

/**
 * Nearly a copy of the core module. Simply adds moduleData to item template rendering process.
 *
 * Class ModuleNavigation
 */
class ModuleNavigation extends \Contao\ModuleNavigation
{
    protected function renderNavigation($pid, $level = 1, $host = null, $language = null)
    {
        // Get all active subpages
        $objSubpages = \PageModel::findPublishedSubpagesWithoutGuestsByPid($pid, $this->showHidden, $this instanceof ModuleSitemap);

        if (null === $objSubpages) {
            return '';
        }

        $items  = [];
        $groups = [];

        // Get all groups of the current front end user
        if (FE_USER_LOGGED_IN) {
            $this->import('FrontendUser', 'User');
            $groups = $this->User->groups;
        }

        // Layout template fallback
        if ('' == $this->navigationTpl) {
            $this->navigationTpl = 'nav_default';
        }

        /** @var FrontendTemplate|object $objTemplate */
        $objTemplate = new \FrontendTemplate($this->navigationTpl);

        $objTemplate->pid   = $pid;
        $objTemplate->type  = \get_class($this);
        $objTemplate->cssID = $this->cssID; // see #4897
        $objTemplate->level = 'level_'.$level++;

        /* @var PageModel $objPage */
        global $objPage;

        // Browse subpages
        foreach ($objSubpages as $objSubpage) {
            // Skip hidden sitemap pages
            if ($this instanceof ModuleSitemap && 'map_never' == $objSubpage->sitemap) {
                continue;
            }

            $subitems = '';
            $_groups  = \StringUtil::deserialize($objSubpage->groups);

            // Override the domain (see #3765)
            if (null !== $host) {
                $objSubpage->domain = $host;
            }

            // Do not show protected pages unless a front end user is logged in
            if (!$objSubpage->protected || (\is_array($_groups) && \count(array_intersect($_groups, $groups))) || $this->showProtected || ($this instanceof ModuleSitemap && 'map_always' == $objSubpage->sitemap)) {
                // Check whether there will be subpages
                if ($objSubpage->subpages > 0 && (!$this->showLevel || $this->showLevel >= $level || (!$this->hardLimit && ($objPage->id == $objSubpage->id || \in_array($objPage->id, $this->Database->getChildRecords($objSubpage->id, 'tl_page')))))) {
                    $subitems = $this->renderNavigation($objSubpage->id, $level, $host, $language);
                }

                $href = null;

                // Get href
                switch ($objSubpage->type) {
                    case 'redirect':
                        $href = $objSubpage->url;

                        if (0 === strncasecmp($href, 'mailto:', 7)) {
                            $href = \StringUtil::encodeEmail($href);
                        }

                        break;

                    case 'forward':
                        if ($objSubpage->jumpTo) {
                            /** @var PageModel $objNext */
                            $objNext = $objSubpage->getRelated('jumpTo');
                        } else {
                            $objNext = \PageModel::findFirstPublishedRegularByPid($objSubpage->id);
                        }

                        $isInvisible = !$objNext->published || ('' != $objNext->start && $objNext->start > time()) || ('' != $objNext->stop && $objNext->stop < time());

                        // Hide the link if the target page is invisible
                        if (!$objNext instanceof PageModel || ($isInvisible && !BE_USER_LOGGED_IN)) {
                            continue 2;
                        }

                        $href = $objNext->getFrontendUrl();

                        break;

                    default:
                        $href = $objSubpage->getFrontendUrl();

                        break;
                }


                // stay in amp context
                $href = System::getContainer()->get('huh.utils.url')->addQueryString('amp=1'.(System::getContainer()->getParameter('kernel.debug') ? '#development=1' : ''), $href);

                $row   = $objSubpage->row();
                $trail = \in_array($objSubpage->id, $objPage->trail);

                // Active page
                if (($objPage->id == $objSubpage->id || ('forward' == $objSubpage->type && $objPage->id == $objSubpage->jumpTo)) && !($this instanceof ModuleSitemap) && $href == \Environment::get('request')) {
                    // Mark active forward pages (see #4822)
                    $strClass = (('forward' == $objSubpage->type && $objPage->id == $objSubpage->jumpTo) ? 'forward'.($trail ? ' trail' : '') : 'active').(('' != $subitems) ? ' submenu' : '').($objSubpage->protected ? ' protected' : '').(('' != $objSubpage->cssClass) ? ' '.$objSubpage->cssClass : '');

                    $row['isActive'] = true;
                    $row['isTrail']  = false;
                } // Regular page
                else {
                    $strClass = (('' != $subitems) ? 'submenu' : '').($objSubpage->protected ? ' protected' : '').($trail ? ' trail' : '').(('' != $objSubpage->cssClass) ? ' '.$objSubpage->cssClass : '');

                    // Mark pages on the same level (see #2419)
                    if ($objSubpage->pid == $objPage->pid) {
                        $strClass .= ' sibling';
                    }

                    $row['isActive'] = false;
                    $row['isTrail']  = $trail;
                }

                $row['subitems']    = $subitems;
                $row['class']       = trim($strClass);
                $row['title']       = \StringUtil::specialchars($objSubpage->title, true);
                $row['pageTitle']   = \StringUtil::specialchars($objSubpage->pageTitle, true);
                $row['link']        = $objSubpage->title;
                $row['href']        = $href;
                $row['nofollow']    = (0 === strncmp($objSubpage->robots, 'noindex,nofollow', 16));
                $row['target']      = '';
                $row['description'] = str_replace(["\n", "\r"], [' ', ''], $objSubpage->description);

                // Override the link target
                if ('redirect' == $objSubpage->type && $objSubpage->target) {
                    $row['target'] = ' target="_blank"';
                }

                $items[] = $row;
            }
        }

        // Add classes first and last
        if (!empty($items)) {
            $last = \count($items) - 1;

            $items[0]['class']     = trim($items[0]['class'].' first');
            $items[$last]['class'] = trim($items[$last]['class'].' last');
        }

        $objTemplate->items = $items;
        // HHFIX
        $objTemplate->moduleData = $this->arrData;

        // HHENDFIX

        return !empty($items) ? $objTemplate->parse() : '';
    }
}
