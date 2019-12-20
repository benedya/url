<?php

declare(strict_types=1);

namespace Benedya\Url;

use Psr\Http\Message\UriInterface;

class Url implements UriInterface
{
    /** @var string */
    private $scheme;

    /** @var string */
    private $user;

    /** @var string|null */
    private $pass;

    /** @var string */
    private $host;

    /** @var string */
    private $path;

    /** @var int|null */
    private $port;

    /** @var string */
    private $fragment;

    /** @var ParameterBag */
    private $parameterBag;

    public function __construct(string $url)
    {
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(sprintf('Url "%s" is not valid.', $url));
        }

        $this->initParameterBag();

        $this->parserUrl($url);
    }

    public function __toString()
    {
        $urlAsString =  sprintf('%s//', $this->getScheme() ? $this->getScheme() . ':' : '');

        if ($this->getAuthority()) {
            $urlAsString .= $this->getAuthority();
        } else {
            $urlAsString .= $this->getHost() . ($this->getPort() ?? '');
        }

        $path = $this->getPath();

        if ($path) {
            if (!$this->isAbsolutePath($path)) {
                $path = '/' . $path;
            }

            $urlAsString .= $path;
        }

        if ($this->getQuery()) {
            $urlAsString .= '?' . $this->getQuery();
        }

        if ($this->getFragment()) {
            $urlAsString .= '#' . $this->getFragment();
        }

        return $urlAsString;
    }

    public function __clone()
    {
        $this->parameterBag = clone $this->parameterBag;
    }

    public function withScheme($scheme)
    {
        $url = clone $this;

        $url->scheme = $scheme;

        return $url;
    }

    public function withUserInfo($user, $password = null)
    {
        $url = clone $this;

        $url->user = $user;
        $url->pass = $password;

        return $url;
    }

    public function withHost($host)
    {
        $url = clone $this;

        $url->host = $host;

        return $url;
    }

    public function withPort($port)
    {
        $url = clone $this;

        $url->port = $port;

        return $url;
    }

    public function withPath($path)
    {
        $url = clone $this;

        if ($this->isAbsolutePath($path)) {
            $url->path = $path;
        } else {
            $url->path = $url->getPath() . $path;
        }

        return $url;
    }

    public function withQuery($query)
    {
        $url = clone $this;

        if ($query) {
            $url->initParameterBag(ParameterBag::createFromString($query));
        } else {
            $url->clearQuery();
        }

        return $url;
    }

    public function withFragment($fragment)
    {
        $url = clone $this;

        $url->fragment = $fragment;

        return $url;
    }

    public function getUserInfo()
    {
        $userInfo = $this->getUser();

        if (!$userInfo) {
            return '';
        }

        if ($this->getPass()) {
            $userInfo .= ':' . $this->getPass();
        }

        return $userInfo;
    }

    public function asString(): string
    {
        return $this . '';
    }

    public function getAuthority()
    {
        $authority = $this->getUserInfo();

        if (!$authority) {
            return '';
        }

        if ($this->getHost()) {
            $authority .= '@' . $this->getHost();
        }

        if ($this->getPort()) {
            $authority .= ':' . $this->getPort();
        }

        return $authority;
    }

    public function setQueryParameter(string $key, string $val): self
    {
        $this->parameterBag->set($key, $val);

        return $this;
    }

    public function getQueryParameter(string $key): string
    {
        return $this->parameterBag->get($key);
    }

    public function clearQuery(): self
    {
        $this->initParameterBag();

        return $this;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function getQuery(): string
    {
        return $this->parameterBag . '';
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function initParameterBag(ParameterBag $parameterBag = null): self
    {
        $this->parameterBag = $parameterBag ?? new ParameterBag();

        return $this;
    }

    private function parserUrl(string $url): void
    {
        /** @var array $parts */
        $parts = parse_url($url);

        $this->scheme = $parts['scheme'] ?? '';
        $this->user = $parts['user'] ?? '';
        $this->pass  = $parts['pass'] ?? null;
        $this->host = $parts['host'] ?? '';
        $this->path = $parts['path'] ?? '/';
        $this->port = $parts['port'] ?? null;
        $this->fragment = $parts['fragment'] ?? '';

        if (isset($parts['query'])) {
            $this->initParameterBag(ParameterBag::createFromString($parts['query']));
        }
    }

    private function isAbsolutePath(string $path): bool
    {
        return '/' === substr($path, 0, 1);
    }
}
