<?php

namespace AceAdmin\Datagrid\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Search
{
    /**
     * @var int
     */
    public $minLength = 3;

    /**
     * @var string
     */
    public $columnName;
}