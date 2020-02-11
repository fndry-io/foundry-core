<?php

namespace Foundry\Core\Inputs\Types;

use Foundry\Core\Inputs\Types\Traits\HasConfig;

class HtmlType extends TextInputType
{
    use HasConfig;

    /**
     * @var array The default toolbar options
     */
    protected $toolbar = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        ['blockquote', 'code-block'],
        [['align' => []]],
        [['list' => 'ordered'], ['list' => 'bullet']],
        [['script' => 'sub'], ['script' => 'super']],      // superscript/subscript
        [['indent' => '-1'], ['indent' => '+1']],          // outdent/indent
        [['direction' => 'rtl']],                         // text direction

        [['size' => ['small', false, 'large', 'huge']]],  // custom dropdown
        [['header' => [1, 2, 3, 4, 5, 6, false]]],

        [['color' => []]],          // dropdown with defaults from theme
        ['image', 'video'],

        ['clean']
    ];

    public function __construct(string $name, string $label = null, bool $required = true, $value = null, string $position = 'full', string $rules = null, string $id = null, string $placeholder = null)
    {
        $type = 'html';
        parent::__construct($name, $label, $required, $value, $position, $rules, $id, $placeholder, $type);

        $this->setConfig('editor.block', 'p');
        $this->setConfig('editor.toolbar', $this->toolbar);
    }

    /**
     * Set the default block tag
     *
     * @param string $tag The tag to set the base html blocks as. This should be "p",
     * @return $this
     */
    public function setBlockTag($tag)
    {
        $this->setConfig('editor.block', $tag);
        return $this;
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

}
