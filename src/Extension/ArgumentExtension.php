<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

class ArgumentExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'argument';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node): bool
    {
        return $node->getAttribute('parent') instanceof Arg && false === $this->ignoreFunc($node);
    }

    /**
     * @param Node $node
     * @return bool
     */
    private function ignoreFunc(Node $node): bool
    {
        /** @var FuncCall $funcCallNode */
        $funcCallNode = $node->getAttribute('parent')->getAttribute('parent');

        return
            $funcCallNode instanceof FuncCall
            &&
            $funcCallNode->name instanceof Name
            &&
            in_array($funcCallNode->name->getLast(), $this->option->getIgnoreFuncs(), true);
    }
}
