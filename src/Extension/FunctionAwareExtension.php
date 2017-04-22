<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;

/**
 * @package Povils\PHPMND\Extension
 */
interface FunctionAwareExtension extends Extension
{
    /**
     * @param Node  $node
     * @param array $ignoreFuncs
     *
     * @return bool
     */
    public function ignoreFunc(Node $node, array $ignoreFuncs);
}
