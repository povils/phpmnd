<?php

namespace Povils\PHPMND\Extension;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;

/**
 * Class ArgumentExtension
 *
 * @package Povils\PHPMND\Extension
 */
class ArgumentExtension implements FunctionAwareExtension
{
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'argument';
    }

    /**
     * @inheritdoc
     */
    public function extend(Node $node)
    {
        return $node->getAttribute('parent') instanceof Arg;
    }

    /**
     * @inheritdoc
     */
    public function ignoreFunc(Node $node, array $ignoreFuncs)
    {
        /** @var FuncCall $funcCallNode */
        $funcCallNode = $node->getAttribute('parent')->getAttribute('parent');

        return
            $funcCallNode instanceof FuncCall
            &&
            $funcCallNode->name instanceof Name
            &&
            in_array($funcCallNode->name->getLast(), $ignoreFuncs, true);
    }
}
