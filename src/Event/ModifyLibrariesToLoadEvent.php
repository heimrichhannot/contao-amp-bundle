<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Event;

use Contao\LayoutModel;
use Contao\Template;
use Symfony\Component\EventDispatcher\Event;

class ModifyLibrariesToLoadEvent extends Event
{
    const NAME = 'huh.amp.event.modify_libraries_to_load';

    /**
     * @var string
     */
    protected $ampName;

    /**
     * @var array
     */
    protected $librariesToLoad;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var LayoutModel
     */
    protected $layout;

    /**
     * @param string      $ampName
     * @param array       $librariesToLoad
     * @param string      $template
     * @param LayoutModel $layout
     */
    public function __construct(?string $ampName, array $librariesToLoad, string $template, LayoutModel $layout)
    {
        $this->ampName         = $ampName;
        $this->librariesToLoad = $librariesToLoad;
        $this->template        = $template;
        $this->layout          = $layout;
    }

    /**
     * @return string
     */
    public function getAmpName(): string
    {
        return $this->ampName;
    }

    /**
     * @param string $ampName
     */
    public function setAmpName(string $ampName): void
    {
        $this->ampName = $ampName;
    }

    /**
     * @return array
     */
    public function getLibrariesToLoad(): array
    {
        return $this->librariesToLoad;
    }

    /**
     * @param array $librariesToLoad
     */
    public function setLibrariesToLoad(array $librariesToLoad): void
    {
        $this->librariesToLoad = $librariesToLoad;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }


    /**
     * @return LayoutModel
     */
    public function getLayout(): LayoutModel
    {
        return $this->layout;
    }

    /**
     * @param LayoutModel $layout
     */
    public function setLayout(LayoutModel $layout): void
    {
        $this->layout = $layout;
    }
}
