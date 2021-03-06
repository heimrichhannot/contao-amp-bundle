# Changelog
All notable changes to this project will be documented in this file.

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
