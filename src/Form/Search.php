<?php

namespace AceAdmin\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;

class Search extends Form
{
    public function __construct()
    {
        parent::__construct('search');

        $this->setAttribute('class', 'pull-left');
        $this->setAttribute('method', 'GET');

        $q = new Element\Text('q');
        $q->setAttribute('placeholder', 'Search...');
        $q->setAttribute('required', true);
        $this->add($q);

        $submit = new Element\Submit('submit');
        $submit->setAttribute('class', 'btn-primary pull-right');
        $submit->setOption('glyphicon', 'search');
        $submit->setLabel('Search');
        $this->add($submit);
    }
}