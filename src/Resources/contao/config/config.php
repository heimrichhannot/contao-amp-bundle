<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseTemplate']['amp'] = ['huh.amp.event_listener.hook_listener', 'parseTemplate'];
$GLOBALS['TL_HOOKS']['generatePage']['amp'] = ['huh.amp.event_listener.hook_listener', 'generatePage'];