<?php

declare(strict_types=1);

namespace Povils\PHPMND\PhpParser;

use Povils\PHPMND\PhpParser\Exception\UnparsableFile;
use PhpParser\Node;
use PhpParser\Parser;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class FileParser
{
    private Parser $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @return Node[]
     */
    public function parse(SplFileInfo $fileInfo): array
    {
        try {
            return $this->parser->parse($fileInfo->getContents());
        } catch (Throwable $throwable) {
            $filePath = $fileInfo->getRealPath() === false
                ? $fileInfo->getPathname()
                : $fileInfo->getRealPath();

            throw UnparsableFile::fromInvalidFile($filePath, $throwable);
        }
    }
}
