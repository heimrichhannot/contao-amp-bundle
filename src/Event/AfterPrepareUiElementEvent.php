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

class AfterPrepareUiElementEvent extends Event
{
    const NAME = 'huh.amp.event.after_prepare_ui_element';

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var LayoutModel
     */
    protected $layout;

    /**
     * @param Template    $template
     * @param LayoutModel $layout
     */
    public function __construct(Template $template, LayoutModel $layout)
    {
        $this->template = $template;
        $this->layout = $layout;
    }

    /**
     * @return Template
     */
    public function getTemplate(): Template
    {
        return $this->template;
    }

    /**
     * @param Template $template
     */
    public function setTemplate(Template $template): void
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
