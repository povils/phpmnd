<?php

declare(strict_types=1);

namespace Povils\PHPMND;

use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Expr\UnaryPlus;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\PhpParser\Visitor\ParentConnector;
use Symfony\Component\Finder\SplFileInfo;

class FileReportGenerator
{
    private SplFileInfo $file;

    private Option $option;

    public function __construct(SplFileInfo $file, Option $option)
    {
        $this->file = $file;
        $this->option = $option;
    }

    public function detect(Node $node): iterable
    {
        if ($this->isIgnorableConst($node)) {
            return;
        }

        /** @var LNumber|DNumber|String_ $scalar */
        $scalar = $node;

        if ($this->hasSign($node)) {
            $node = ParentConnector::findParent($node);

            if ($this->isMinus($node)) {
                if (!isset($scalar->value)) {
                    return;
                }

                $scalar->value = -$scalar->value;
            }
        }

        if ($this->isNumber($scalar) || $this->isString($scalar)) {
            foreach ($this->option->getExtensions() as $extension) {
                $extension->setOption($this->option);

                if ($extension->extend($node)) {
                    yield new DetectionResult($this->file, $scalar->getLine(), $scalar->value);
                }
            }
        }
    }

    private function isIgnorableConst(Node $node): bool
    {
        return $node instanceof Const_ &&
            ($this->isNumber($node->value) || $this->isString($node->value));
    }

    private function isNumber(Node $node): bool
    {
        $isNumber = (
            $node instanceof LNumber ||
            $node instanceof DNumber ||
            $this->isValidNumeric($node)
        );

        return $isNumber && $this->ignoreNumber($node) === false;
    }

    private function isString(Node $node): bool
    {
        return $this->option->includeStrings() && $node instanceof String_ && $this->ignoreString($node) === false;
    }

    private function ignoreNumber(Node $node): bool
    {
        return in_array($node->value, $this->option->getIgnoreNumbers(), true);
    }

    private function ignoreString(Node $node): bool
    {
        return in_array($node->value, $this->option->getIgnoreStrings(), true);
    }

    private function hasSign(Node $node): bool
    {
        $parentNode = ParentConnector::findParent($node);

        return $parentNode instanceof UnaryMinus || $parentNode instanceof UnaryPlus;
    }

    private function isMinus(Node $node): bool
    {
        return $node instanceof UnaryMinus;
    }

    private function isValidNumeric(Node $node): bool
    {
        return $this->option->includeNumericStrings()
            && isset($node->value)
            && is_numeric($node->value)
            && $this->ignoreString($node) === false;
    }
}
