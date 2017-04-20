<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Arg;

/**
 * Class ArgumentExtension
 *
 * @package Povils\PHPMND\Extension
 */
class ArgumentExtension implements Extension
{
    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof Arg;
    }
}
