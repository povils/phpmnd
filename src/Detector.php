<?php

namespace Povils\PHPMND;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Povils\PHPMND\Console\Option;
use Povils\PHPMND\Visitor\DetectorVisitor;
use Povils\PHPMND\Visitor\HintVisitor;
use Povils\PHPMND\Visitor\ParentConnectorVisitor;
use Symfony\Component\Finder\SplFileInfo;
use const PHP_VERSION;

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

    public function __construct(Option $option, HintList $hintList)
    {
        $this->option = $option;
        $this->hintList = $hintList;
    }

    public function detect(SplFileInfo $file): FileReport
    {
        // For PHP < 8.0 we want to specify a lexer object.
        // Otherwise the code creates a `Lexer\Emulative()` instance, which by default uses PHP 8 compatibility
        // with e.g. longer list of reserved keywords
        $lexer = version_compare('8.0', PHP_VERSION) > 0 ? new Lexer() : null;

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7, $lexer);
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
