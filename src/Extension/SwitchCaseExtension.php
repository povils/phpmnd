<?php

declare(strict_types=1);

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Stmt\Case_;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;

class SwitchCaseExtension extends Extension
{
    public function getName(): string
    {
        return 'switch_case';
    }

    public function extend(Node $node): bool
    {
        return ParentConnector::findParent($node) instanceof Case_;
    }
}
