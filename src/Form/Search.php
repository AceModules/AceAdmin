<?php

namespace AceAdmin\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;

class Search extends Form
{
    public function __construct()
    {
        parent::__construct('search');

        $this->setAttribute('class', 'float-left');
        $this->setAttribute('method', 'GET');

        $q = new Element\Text('q');
        $q->setAttribute('placeholder', 'Search...');
        $q->setAttribute('required', true);
        $this->add($q);

        $submit = new Element\Submit('submit');
        $submit->setAttribute('class', 'btn-primary float-right');
        $submit->setOption('icon', 'fas fa-search');
        $submit->setLabel('Search');
        $this->add($submit);
    }
}