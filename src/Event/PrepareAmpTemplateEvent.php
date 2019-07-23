<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\AmpBundle\Event;


use Contao\LayoutModel;
use Symfony\Component\EventDispatcher\Event;

class PrepareAmpTemplateEvent extends Event
{
    const NAME = 'huh.amp.event.prepare_amp_template';

    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $context;
    /**
     * @var array
     */
    private $componentsToLoad;
    /**
     * @var LayoutModel
     */
    private $layout;

    /**
     * PrepareAmpTemplate constructor.
     * @param string $template Template name
     * @param array $context Template context
     * @param array $componentsToLoad AMP components to load for this template
     * @param LayoutModel $layout The page layout
     */
    public function __construct(string $template, array $context, array $componentsToLoad, LayoutModel $layout)
    {
        $this->template = $template;
        $this->context = $context;
        $this->componentsToLoad = $componentsToLoad;
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getComponentsToLoad(): array
    {
        return $this->componentsToLoad;
    }

    /**
     * @param array $componentsToLoad
     */
    public function setComponentsToLoad(array $componentsToLoad): void
    {
        $this->componentsToLoad = $componentsToLoad;
    }

    /**
     * @return LayoutModel
     */
    public function getLayout(): LayoutModel
    {
        return $this->layout;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }


}