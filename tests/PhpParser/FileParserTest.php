<?php

declare(strict_types=1);

namespace Povils\PHPMND\Tests\PhpParser;

use function array_map;
use function explode;
use function implode;
use Povils\PHPMND\PhpParser\Exception\UnparsableFile;
use Povils\PHPMND\PhpParser\FileParser;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeDumper;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use function realpath;
use function sprintf;
use Symfony\Component\Finder\SplFileInfo;

class FileParserTest extends TestCase
{
    public function testItParsesTheGivenFileOnlyOnce(): void
    {
        $fileInfo = self::createFileInfo('/unknown', $fileContents = 'contents');

        $phpParser = $this->createMock(Parser::class);

        $phpParser
            ->expects($this->once())
            ->method('parse')
            ->with($fileContents)
            ->willReturn($expectedReturnedStatements = []);

        $parser = new FileParser($phpParser);

        $returnedStatements = $parser->parse($fileInfo);

        $this->assertSame($expectedReturnedStatements, $returnedStatements);
    }

    /**
     * @dataProvider fileToParserProvider
     */
    public function testItCanParseFile(SplFileInfo $fileInfo, string $expectedPrintedParsedContents): void
    {
        $statements = (new FileParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7)))->parse($fileInfo);

        foreach ($statements as $statement) {
            $this->assertInstanceOf(Node::class, $statement);
        }

        $actualPrintedParsedContents = (new NodeDumper())->dump($statements);

        $this->assertSame(
            $expectedPrintedParsedContents,
            $actualPrintedParsedContents
        );
    }

    public function testItThrowsUponFailure(): void
    {
        $parser = new FileParser((new ParserFactory())->create(ParserFactory::PREFER_PHP7));

        try {
            $parser->parse(self::createFileInfo('/unknown', '<?php use foo as self;'));

            $this->fail('Expected PHPParser to be unable to parse the above expression');
        } catch (UnparsableFile $exception) {
            $this->assertSame(
                'Could not parse the file "/unknown". Check if it is a valid PHP file',
                $exception->getMessage()
            );
            $this->assertSame(0, $exception->getCode());
            $this->assertInstanceOf(Error::class, $exception->getPrevious());
        }

        $fileRealPath = realpath(__FILE__);

        $this->assertNotFalse($fileRealPath);

        try {
            $parser->parse(self::createFileInfo($fileRealPath, '<?php use foo as self;'));

            $this->fail('Expected PHPParser to be unable to parse the above expression');
        } catch (UnparsableFile $exception) {
            $this->assertSame(
                sprintf(
                    'Could not parse the file "%s". Check if it is a valid PHP file',
                    $fileRealPath
                ),
                $exception->getMessage()
            );

            $this->assertSame(0, $exception->getCode());
            $this->assertInstanceOf(Error::class, $exception->getPrevious());
        }
    }

    public function fileToParserProvider(): iterable
    {
        yield 'empty file' => [
            self::createFileInfo('/unknown', ''),
            <<<'AST'
array(
)
AST
            ,
        ];

        yield 'empty PHP file' => [
            self::createFileInfo(
                '/unknown',
                <<<'PHP'
<?php

PHP
            ),
            <<<'AST'
array(
)
AST
        ];
    }

    private static function createFileInfo(string $path, string $contents): SplFileInfo
    {
        return new class($path, $contents) extends SplFileInfo {
            /**
             * @var string
             */
            private $contents;

            public function __construct(string $path, string $contents)
            {
                parent::__construct($path, $path, $path);

                $this->contents = $contents;
            }

            public function getContents(): string
            {
                return $this->contents;
            }
        };
    }

    private function normalizeString(string $string): string
    {
        return implode(
            "\n",
            array_map('rtrim', explode("\n", $string))
        );
    }
}
