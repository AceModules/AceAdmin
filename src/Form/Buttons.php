<?php

namespace AceAdmin\Form;

use Laminas\Form\Element;
use Laminas\Form\Fieldset;

class Buttons extends Fieldset
{
    /**
     * @param string $submitName
     * @param string $submitClass
     */
    public function __construct($submitName = 'Save', $submitClass = 'btn-primary')
    {
        parent::__construct('buttons');

        $this->setOption('layout', \TwbsHelper\Form\View\Helper\Form::LAYOUT_INLINE);
        $this->setAttribute('class', 'form-group');

        $submit = new Element\Submit('submit');
        $submit->setAttribute('value', $submitName);
        $submit->setAttribute('class', $submitClass . ' float-right');
        $this->add($submit);

        $cancel = new Element\Submit('cancel');
        $cancel->setAttribute('value', 'Cancel');
        $cancel->setAttribute('formnovalidate', true);
        $cancel->setAttribute('data-dismiss', 'modal');
        $cancel->setAttribute('class', 'btn-warning float-right');
        $this->add($cancel);
    }
}