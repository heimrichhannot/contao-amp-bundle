<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\FrontendModule;

use Contao\ModuleNavigation;

/**
 * Nearly a copy of the core module. Simply adds moduleData to item template rendering process.
 *
 * Class ModuleNavigation
 */
class AmpNavigationModule extends ModuleNavigation
{
    const TYPE = 'ampnavigation';
    protected $strTemplate = 'mod_ampnavigation';
}
