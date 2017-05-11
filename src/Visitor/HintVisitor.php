<?php

namespace Povils\PHPMND\Visitor;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Povils\PHPMND\HintList;

/**
 * Class HintVisitor
 *
 * @package Povils\PHPMND\Visitor
 */
class HintVisitor extends NodeVisitorAbstract
{
    /**
     * @var HintList
     */
    private $hintList;

    /**
     * @param HintList $hintList
     */
    public function __construct(HintList $hintList)
    {
        $this->hintList = $hintList;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Const_) {
            if (false === $node->value instanceof Scalar) {
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            $constantValue = $node->value->value;
            $constParent = $node->getAttribute('parent');
            if ($constParent instanceof ClassConst) {
                $classConstParent = $constParent->getAttribute('parent');
                if ($classConstParent instanceof Class_) {
                    $this->hintList->addClassCont($constantValue, $classConstParent->name, $node->name);
                }
            }
        }

        return null;
    }
}
