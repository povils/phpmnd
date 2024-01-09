<?php

declare(strict_types=1);

namespace Povils\PHPMND;

use function array_key_exists;
use Closure;
use function get_class;
use function gettype;
use InvalidArgumentException;
use function is_object;
use Povils\PHPMND\PhpParser\FileParser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use function sprintf;

class Container
{
    private array $keys = [];
    private array $factories = [];
    private array $values = [];

    private function __construct(array $values)
    {
        foreach ($values as $id => $value) {
            $this->offsetSet($id, $value);
        }
    }

    public static function create(): self
    {
        return new self([
            Parser::class => static fn (): Parser => (new ParserFactory())->createForHostVersion(),
            FileParser::class => static fn (self $container): FileParser => new FileParser($container->getParser()),
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

    private function offsetSet(string $id, Closure $value): void
    {
        $this->keys[$id] = true;
        $this->factories[$id] = $value;
        unset($this->values[$id]);
    }

    private function get(string $id): object
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
