# Changelog
All notable changes to this project will be documented in this file.

## [0.2.0] - 2019-07-22

#### Added
* missing english translations

#### Changed
* renamed ModuleNavigation to AmpNavigationModule
* moved AmpNavigationModule to FrontendModule namespace
* [BC BREAK] renamed AmpNavigationModule type to ampnavigation
* [BC BREAK] changed bundle configuration, please see readme
* unset `$GLOBALS['TL_HOOKS']['generatePage']['huh.encore-bundle` if amp page (don't call doAddEncore() twice)
* updated README

#### Fixed
* menu not renders as sidebar when ampRenderSubItemsAsAccordions not checked

## [0.1.2] - 2019-02-28

#### Fixed
* set `huh.head.tag.base` tag content to `/` as it is the only allowed value (see: https://github.com/ampproject/amphtml/issues/2277#issuecomment-278710439)

## [0.1.1] - 2019-02-28

#### Fixed
* skip services `huh.head.tag.base`, `huh.head.tag.pwa.link_manifest`, `huh.head.tag.pwa.meta_themecolor`, `huh.head.tag.pwa.script` in `fe_page_amp.html5` by default

## [0.1.0] - 2019-02-27

#### Added
* initial version that supports html5 and twig templates
