<?php

namespace PHPMND\Extension;

use PhpParser\Node;

/**
 * @package PHPMND\Extension
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
