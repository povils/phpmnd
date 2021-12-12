<?php

declare(strict_types=1);

namespace Povils\PHPMND;

use PhpParser\NodeTraverser;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\PhpParser\FileParser;
use Povils\PHPMND\PhpParser\Visitor\DetectionVisitor;
use Povils\PHPMND\PhpParser\Visitor\HintVisitor;
use Povils\PHPMND\PhpParser\Visitor\ParentConnectorVisitor;
use Symfony\Component\Finder\SplFileInfo;

class Detector
{
    private Option $option;

    private FileParser $parser;

    private HintList $hintList;

    public function __construct(FileParser $parser, Option $option, HintList $hintList)
    {
        $this->parser = $parser;
        $this->option = $option;
        $this->hintList = $hintList;
    }

    public function detect(SplFileInfo $file): iterable
    {
        $statements = $this->parser->parse($file);

        $traverser = new NodeTraverser();

        $detectorVisitor = new DetectionVisitor(
            new FileReportGenerator(
                $file,
                $this->option
            )
        );

        $traverser->addVisitor(new ParentConnectorVisitor());

        if ($this->option->giveHint()) {
            $traverser->addVisitor(new HintVisitor($this->hintList));
        }

        $traverser->addVisitor($detectorVisitor);

        $traverser->traverse($statements);

        yield from $detectorVisitor->getDetections();
    }
}
