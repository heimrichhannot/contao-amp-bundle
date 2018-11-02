<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout']['amp'] = ['huh.amp.event_listener.hook_listener', 'getPageLayout'];
$GLOBALS['TL_HOOKS']['parseTemplate']['amp'] = ['huh.amp.event_listener.hook_listener', 'parseTemplate'];
$GLOBALS['TL_HOOKS']['generatePage']['amp'] = ['huh.amp.event_listener.hook_listener', 'generatePage'];
$GLOBALS['TL_HOOKS']['modifyFrontendPage']['amp'] = ['huh.amp.event_listener.hook_listener', 'modifyFrontendPage'];

/**
 * Frontend modules
 */
System::getContainer()->get('huh.utils.array')->insertInArrayByName(
    $GLOBALS['FE_MOD']['navigationMenu'], 'navigation',
    ['navigation_amp' => '\HeimrichHannot\AmpBundle\Module\ModuleNavigation'], 1
);