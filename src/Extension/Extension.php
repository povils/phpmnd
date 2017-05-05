<?php

namespace PHPMND\Extension;

use PhpParser\Node;

/**
 * Interface Extension
 */
interface Extension
{
    /**
     * Extend magic number detection.
     *
     * @param Node $node
     *
     * @return bool
     */
    public function extend(Node $node);
}
