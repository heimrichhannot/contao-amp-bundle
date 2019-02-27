<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Manager;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class AmpManager implements FrameworkAwareInterface, ContainerAwareInterface
{
    use FrameworkAwareTrait;
    use ContainerAwareTrait;

    protected static $libs = [];

    private $ampActive = false;

    /**
     * @return bool
     */
    public function isAmpActive(): bool
    {
        return $this->ampActive;
    }

    /**
     * @param bool $ampActive
     */
    public function setAmpActive(bool $ampActive): void
    {
        $this->ampActive = $ampActive;
    }


    public static function addLib(string $ampName, string $url)
    {
        if (!isset(static::$libs[$ampName])) {
            static::$libs[$ampName] = $url;
        }
    }

    /**
     * @return array
     */
    public static function getLibs(): array
    {
        return self::$libs;
    }

    /**
     * @param array $libs
     */
    public static function setLibs(array $libs): void
    {
        self::$libs = $libs;
    }
}
