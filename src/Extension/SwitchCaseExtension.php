<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\Case_;

class SwitchCaseExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'switch_case';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof Case_;
    }
}
