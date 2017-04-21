<?php

namespace Povils\PHPMND\Visitor;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Povils\PHPMND\Extension\Extension;
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
     * @var Extension[]
     */
    private $extensions;

    /**
     * @var array
     */
    private $ignoreNumbers;

    /**
     * @param FileReport $fileReport
     * @param Extension[] $extensions
     * @param array $ignoreNumbers
     */
    public function __construct(FileReport $fileReport, array $extensions, array $ignoreNumbers)
    {
        $this->fileReport = $fileReport;
        $this->extensions = $extensions;
        $this->ignoreNumbers = $ignoreNumbers;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Const_) {
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        /** @var LNumber $node */
        if (($node instanceof LNumber || $node instanceof DNumber) && false === $this->ignoreNumber($node)) {
            foreach ($this->extensions as $extension) {
                if ($extension->extend($node)) {
                    $this->fileReport->addEntry($node->getLine(), $node->value);

                    return null;
                }
            }
        }

        return null;
    }

    /**
     * @param LNumber|DNumber|Scalar $node
     *
     * @return bool
     */
    private function ignoreNumber(Scalar $node)
    {
        return in_array($node->value, $this->ignoreNumbers, true);
    }
}
