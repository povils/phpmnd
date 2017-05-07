<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\Case_;

/**
 * Class SwitchCaseExtension
 *
 * @package Povils\PHPMND\Extension
 */
class SwitchCaseExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'switch_case';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof Case_;
    }
}
