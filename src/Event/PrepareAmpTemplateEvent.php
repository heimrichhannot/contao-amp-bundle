<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AmpBundle\Event;

use Contao\LayoutModel;
use Symfony\Contracts\EventDispatcher\Event;

class PrepareAmpTemplateEvent extends Event
{
    /** @deprecated Use FQCN as event name instead. Will be removed in a future major version. */
    public const NAME = 'huh.amp.event.prepare_amp_template';

    private string $template;
    private array $context;
    private array $componentsToLoad;
    private LayoutModel $layout;

    /**
     * PrepareAmpTemplate constructor.
     *
     * @param string      $template         Template name
     * @param array       $context          Template context
     * @param array       $componentsToLoad AMP components to load for this template
     * @param LayoutModel $layout           The page layout
     */
    public function __construct(string $template, array $context, array $componentsToLoad, LayoutModel $layout)
    {
        $this->template = $template;
        $this->context = $context;
        $this->componentsToLoad = $componentsToLoad;
        $this->layout = $layout;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function getComponentsToLoad(): array
    {
        return $this->componentsToLoad;
    }

    public function setComponentsToLoad(array $componentsToLoad): void
    {
        $this->componentsToLoad = $componentsToLoad;
    }

    public function getLayout(): LayoutModel
    {
        return $this->layout;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }
}
