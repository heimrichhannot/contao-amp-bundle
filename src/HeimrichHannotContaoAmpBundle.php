<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle;

use HeimrichHannot\AmpBundle\DependencyInjection\AmpExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoAmpBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new AmpExtension();
    }
}
