<?php

declare(strict_types=1);

namespace Povils\PHPMND;

use function array_key_exists;
use Closure;
use function get_class;
use function gettype;
use InvalidArgumentException;
use function is_object;
use const PHP_VERSION;
use Povils\PHPMND\PhpParser\FileParser;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use function sprintf;
use function version_compare;

class Container
{
    private $keys = [];
    private $factories = [];
    private $values = [];

    private function __construct(array $values)
    {
        foreach ($values as $id => $value) {
            $this->offsetSet($id, $value);
        }
    }

    public static function create(): self
    {
        return new self([
            Lexer::class => static function (self $container): Lexer {
                // For PHP < 8.0 we want to specify a lexer object.
                // Otherwise, the code creates a `Lexer\Emulative()` instance, which by default uses PHP 8 compatibility
                // with e.g. longer list of reserved keywords
                return version_compare('8.0', PHP_VERSION, '<') ? new Lexer() : new Lexer\Emulative();
            },
            Parser::class => static function (self $container): Parser {
                return (new ParserFactory())->create(ParserFactory::PREFER_PHP7, $container->getLexer());
            },
            FileParser::class => static function (self $container): FileParser {
                return new FileParser($container->getParser());
            },
        ]);
    }

    public function getFileParser(): FileParser
    {
        return $this->get(FileParser::class);
    }

    private function getParser(): Parser
    {
        return $this->get(Parser::class);
    }

    private function getLexer(): Lexer
    {
        return $this->get(Lexer::class);
    }

    private function offsetSet(string $id, Closure $value): void
    {
        $this->keys[$id] = true;
        $this->factories[$id] = $value;
        unset($this->values[$id]);
    }

    /**
     * @return object
     */
    private function get(string $id)
    {
        if (!isset($this->keys[$id])) {
            throw new InvalidArgumentException(sprintf('Unknown service "%s"', $id));
        }

        if (array_key_exists($id, $this->values)) {
            $value = $this->values[$id];
        } else {
            $value = $this->values[$id] = $this->factories[$id]($this);
        }

        if (!$value instanceof $id) {
            throw new InvalidArgumentException(sprintf(
                'Expected an instance of %2$s. Got: %s',
                is_object($value) ? get_class($value) : gettype($value),
                $id
            ));
        }

        return $value;
    }
}
