<?php

namespace Povils\PHPMND;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\Visitor\DetectorVisitor;
use Povils\PHPMND\Visitor\HintVisitor;
use Povils\PHPMND\Visitor\ParentConnectorVisitor;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Detector
 *
 * @package Povils\PHPMND
 */
class Detector
{
    /**
     * @var Option
     */
    private $option;

    /**
     * @var HintList
     */
    private $hintList;

    /**
     * @param Option   $option
     * @param HintList $hintList
     */
    public function __construct(Option $option, HintList $hintList)
    {
        $this->option = $option;
        $this->hintList = $hintList;
    }

    /**
     * @param SplFileInfo $file
     *
     * @return FileReport
     */
    public function detect(SplFileInfo $file)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser = new NodeTraverser();

        $fileReport = new FileReport($file);

        $traverser->addVisitor(new ParentConnectorVisitor());
        $traverser->addVisitor(new DetectorVisitor($fileReport, $this->option));
        if ($this->option->giveHint()) {
            $traverser->addVisitor(new HintVisitor($this->hintList));
        }


        $stmts = $parser->parse($file->getContents());
        $traverser->traverse($stmts);

        return $fileReport;
    }
}
