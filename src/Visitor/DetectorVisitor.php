<?php

namespace Povils\PHPMND\Visitor;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\Extension\FunctionAwareExtension;
use Povils\PHPMND\FileReport;

/**
 * Class DetectorVisitor
 *
 * @package Povils\PHPMND
 */
class DetectorVisitor extends NodeVisitorAbstract
{
    /**
     * @var FileReport
     */
    private $fileReport;

    /**
     * @var Option
     */
    private $option;

    /**
     * @param FileReport $fileReport
     * @param Option $option
     */
    public function __construct(FileReport $fileReport, Option $option)
    {
        $this->fileReport = $fileReport;
        $this->option = $option;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Const_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        if ($this->isNumber($node) || $this->isString($node)) {
            foreach ($this->option->getExtensions() as $extension) {
                if ($extension->extend($node) && false === $this->ignoreFunc($node, $extension)) {
                    $this->fileReport->addEntry($node->getLine(), $node->value);

                    return null;
                }
            }
        }

        return null;
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isNumber(Node $node)
    {
        return ($node instanceof LNumber || $node instanceof DNumber) && false === $this->ignoreNumber($node);
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function isString(Node $node)
    {
        return $this->option->includeStrings() && $node instanceof String_ && false === $this->ignoreString($node);
    }

    /**
     * @param LNumber|DNumber|Node $node
     *
     * @return bool
     */
    private function ignoreNumber(Node $node)
    {
        return in_array($node->value, $this->option->getIgnoreNumbers(), true);
    }

    /**
     * @param String_|Node $node
     *
     * @return bool
     */
    private function ignoreString(Node $node)
    {
        return in_array($node->value, $this->option->getIgnoreStrings(), true);
    }

    /**
     * @param Node      $node
     * @param Extension $extension
     *
     * @return bool
     */
    private function ignoreFunc(Node $node, Extension $extension)
    {
        if ($extension instanceof FunctionAwareExtension) {
            return $extension->ignoreFunc($node, $this->option->getIgnoreFuncs());
        }

        return false;
    }
}
