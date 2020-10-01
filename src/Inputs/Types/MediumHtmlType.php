<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasConfig;

class MediumHtmlType extends TextInputType
{
    use HasConfig;

    /**
     * @var array The default toolbar options
     */
    protected $toolbar = [
        'bold',
        'italic',
        'underline',
        'strikethrough',
        'subscript',
        'superscript',
        'anchor',
        'image',
        'quote',
        'orderedlist',
        'unorderedlist',
        'indent',
        'outdent',
        'justifyLeft',
        'justifyCenter',
        'justifyRight',
        'justifyFull',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'removeFormat'
    ];

    /**
     * @var array The extensions toolbar options
     */
    protected $extensionsToolbar = [
        'highlighter',
    ];

    public function __construct(string $name, string $label = null, bool $required = true, $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null)
    {
        $type = 'html';
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder, $type);
        $this->setConfig('editor.toolbar', $this->getFullToolbar());
    }

    /**
     * Set the toolbar options
     *
     * @param array $toolbar
     * @return $this
     */
    public function setToolbar($toolbar){
        $this->setConfig('editor.toolbar', $toolbar);
        return $this;
    }

    /**
     * Returns default and extensions toolbar
     *
     * @return array
     */
    public function getFullToolbar()
    {
        return array_merge($this->toolbar, $this->extensionsToolbar);
    }

    public function setSimpleMode()
    {
        $this->toolbar = [
            'bold', 'italic', 'underline'
        ];

        $this->toolbar = $this->getFullToolbar();

        $this->setConfig('editor.toolbar', $this->toolbar);
        $this->setConfig('editor.disableReturn', true);
    }

    public function setAdvancedMode()
    {
        $this->toolbar = [
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'subscript',
            'superscript',
            'anchor',
            'image',
            'quote',
            'pre',
            'orderedlist',
            'unorderedlist',
            'indent',
            'outdent',
            'justifyLeft',
            'justifyCenter',
            'justifyRight',
            'justifyFull',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'removeFormat',
            'html'
        ];

        $this->toolbar = $this->getFullToolbar();

        $this->setConfig('editor.toolbar', $this->toolbar);
        $this->setConfig('editor.disableReturn', false);
    }
}
