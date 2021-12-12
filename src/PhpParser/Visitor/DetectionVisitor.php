<?php

declare(strict_types=1);

namespace Povils\PHPMND\PhpParser\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Povils\PHPMND\DetectionResult;
use Povils\PHPMND\FileReportGenerator;

class DetectionVisitor extends NodeVisitorAbstract
{
    private FileReportGenerator $generator;

    /**
     * @var array<iterable<DetectionResult>
     */
    private array $reports = [];

    public function __construct(FileReportGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function beforeTraverse(array $nodes): ?array
    {
        $this->reports = [];

        return null;
    }

    public function leaveNode(Node $node): ?Node
    {
        $this->reports[] = $this->generator->detect($node);

        return null;
    }

    /**
     * @return iterable<DetectionResult>
     */
    public function getDetections(): iterable
    {
        foreach ($this->reports as $report) {
            yield from $report;
        }
    }
}
