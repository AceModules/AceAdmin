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

        $this->setOption('twb-layout', 'inline');
        $this->setAttribute('class', 'form-group');

        $submit = new Element\Submit('submit');
        $submit->setAttribute('value', $submitName);
        $submit->setAttribute('class', $submitClass . ' pull-right');
        $this->add($submit);

        $cancel = new Element\Submit('cancel');
        $cancel->setAttribute('value', 'Cancel');
        $cancel->setAttribute('formnovalidate', true);
        $cancel->setAttribute('data-dismiss', 'modal');
        $cancel->setAttribute('class', 'btn-warning pull-right');
        $this->add($cancel);
    }
}