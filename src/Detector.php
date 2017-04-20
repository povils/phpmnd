<?php

namespace Povils\PHPMND;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Povils\PHPMND\Extension\DefaultExtension;
use Povils\PHPMND\Extension\Extension;
use Povils\PHPMND\Visitor\DetectorVisitor;
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
     * @var Extension[]
     */
    private $extensions;

    /**
     * @var array
     */
    private $ignoreNumbers = [0, 0., 1];

    public function __construct()
    {
        $this->extensions[] = new DefaultExtension();
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
        $traverser->addVisitor(new DetectorVisitor($fileReport, $this->extensions, $this->ignoreNumbers));

        $stmts = $parser->parse($file->getContents());
        $traverser->traverse($stmts);

        return $fileReport;
    }

    /**
     * @param Extension $extension
     */
    public function addExtension(Extension $extension)
    {
        $this->extensions[] = $extension;
    }

    /**
     * @param array $ignoreNumbers
     */
    public function setIgnoreNumbers(array $ignoreNumbers)
    {
        $this->ignoreNumbers = array_merge($this->ignoreNumbers, $ignoreNumbers);
    }
}
