# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased] - 2023-01-24
- Changed: removed request bundle dependency

## [0.4.4] - 2023-01-06
- Changed: exclude tl_page.enableAmp field
- Changed: removed unused test setup

## [0.4.3] - 2022-10-25
- Fixed: deprecated symfony class

## [0.4.2] - 2022-05-16
- Changed: minumum php version is now 7.4
- Fixed: exception in backend in contao 4.13

## [0.4.1] - 2022-05-05
- Fixed: symfony 5 compatiblity

## [0.4.0] - 2022-05-05
- Changed: minimum contao version now 4.9
- Changed: allow php 8
- Fixed: symfony 5 compatibility

## [0.3.2] - 2021-10-20
- Fixed: dca fields not correctly added to dca

## [0.3.1] - 2020-03-04
- Fixed: added default filter in image template

## [0.3.0] - 2020-01-27
- [BREAKING] Only registered templates will be loaded
- [BREAKING] Renamed Bundle class to `HeimrichHannotAmpBundle` -> This means also twig namespace changed to `HeimrichHannotAmp`
- [BREAKING] Renamed ampTemplate config key to amp_template
- add `convert_html` config option to automatically convert html code to amp-html code** 
- removed unused config key
- some code enhancements


## [0.2.3] - 2019-12-13
- adapted to changes from encore bundle
- refactored generatePageHook listener to own class
- updated utils bundle and encore bundle minimum version

## [0.2.2] - 2019-12-13
- don't index amp pages (maybe you need to rebuild your search index)

## [0.2.1] - 2019-11-25
- fixed wrong amp url on auto_item pages
- fixed deprecation warning with contao-encore-bundle

## [0.2.0] - 2019-07-22

This update contains some BC breaks. If your update, you need to updated the navigation module, migration your config and update your event listeners. If you use encore bundle, you now can just use the default encore entries field in your layout.

#### Added
* missing english translations
* support for encoreEntries field in layout section

#### Changed
* renamed ModuleNavigation to AmpNavigationModule
* moved AmpNavigationModule to FrontendModule namespace
* [BC BREAK] renamed AmpNavigationModule type to ampnavigation
* [BC BREAK] changed bundle configuration, please see readme
* [BC BREAK] Replaced ModifyLibrariesToLoadEvent and AfterPrepareUiElementEvent with PrepareAmpTemplateEvent
* CustomNav now renders without sidebar
* unset `$GLOBALS['TL_HOOKS']['generatePage']['huh.encore-bundle` if amp page (don't call doAddEncore() twice)
* [BC BREAK] renamed tl_page.amp to tl_page.enableAmp
* changed hook entry names to huh_amp
* remove invalid @charset rule when using encore
* updated README

#### Fixed
* menu not renders as sidebar when ampRenderSubItemsAsAccordions not checked
* dca field merge

## [0.1.2] - 2019-02-28

#### Fixed
* set `huh.head.tag.base` tag content to `/` as it is the only allowed value (see: https://github.com/ampproject/amphtml/issues/2277#issuecomment-278710439)

## [0.1.1] - 2019-02-28

#### Fixed
* skip services `huh.head.tag.base`, `huh.head.tag.pwa.link_manifest`, `huh.head.tag.pwa.meta_themecolor`, `huh.head.tag.pwa.script` in `fe_page_amp.html5` by default

## [0.1.0] - 2019-02-27

#### Added
* initial version that supports html5 and twig templates
