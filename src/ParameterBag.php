<?php

declare(strict_types=1);

namespace Benedya\Url;

class ParameterBag implements \Countable
{
    /** @var array  */
    private $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function count(): int
    {
        return \count($this->parameters);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    public function get(string $key, string $default = null): string
    {
        return (string)($this->parameters[$key] ?? $default);
    }

    public function set(string $key, string $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function all(): array
    {
        return $this->parameters;
    }

    public static function createFromString(string $parametersAsString): self
    {
        $parameterBag = new self();
        $queryArr = explode('&', $parametersAsString);

        foreach ($queryArr as $value) {
            [$key, $value] = explode('=', $value);
            $parameterBag->set($key, $value);
        }

        return $parameterBag;
    }

    public function __toString()
    {
        return http_build_query($this->parameters);
    }
}
