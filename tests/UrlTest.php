<?php

declare(strict_types=1);

namespace Benedya\Url\Tests;

use Benedya\Url\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * @var Url
     */
    private $url;

    protected function setUp(): void
    {
        $this->url = new Url('http://login:pass@example.com:443/path?foo=bar&bar=foo#hash');
    }

    protected function tearDown(): void
    {
        unset($this->url);
    }

    public function testWithFragment(): void
    {
        $url = $this->url->withFragment('fragment');

        $this->assertSame('fragment', $url->getFragment());
        $this->assertSame('hash', $this->url->getFragment());
    }

    public function testWitQueryWithEmptyQuery(): void
    {
        $url = $this->url->withQuery('');

        $this->assertSame('', $url->getQuery());
    }

    public function testWithQuery(): void
    {
        $url = $this->url->withQuery('page=2');

        $this->assertSame('page=2', $url->getQuery());
        $this->assertSame('foo=bar&bar=foo', $this->url->getQuery());
    }

    public function testWithPathAbsolute(): void
    {
        $url = $this->url->withPath('/users');

        $this->assertSame('/users', $url->getPath());
    }

    public function testWithPathRelative(): void
    {
        $url = $this->url->withPath('-users');

        $this->assertSame('/path-users', $url->getPath());
    }

    public function testWithPort(): void
    {
        $url = $this->url->withPort(80);

        $this->assertSame(80, $url->getPort());
        $this->assertSame(443, $this->url->getPort());
    }

    public function testWithHost(): void
    {
        $url = $this->url->withHost('google.com');

        $this->assertSame('google.com', $url->getHost());
        $this->assertSame('example.com', $this->url->getHost());
    }


    public function testWithUserInfoWithEmptyUser(): void
    {
        $url = $this->url->withUserInfo('', 'url');

        $this->assertSame('', $url->getUserInfo());
    }

    public function testWithUserInfoWithEmptyPassword(): void
    {
        $url = $this->url->withUserInfo('jack');

        $this->assertSame('jack', $url->getUserInfo());
    }

    public function testWithUserInfoWithUserAndPassword(): void
    {
        $url = $this->url->withUserInfo('jack', 'pwd');

        $this->assertSame('jack:pwd', $url->getUserInfo());
        $this->assertSame('login:pass', $this->url->getUserInfo());
    }

    public function testWithSchemeWithoutScheme(): void
    {
        $url = new Url('http://example.com');
        $urlWithEmptyScheme = $url->withScheme('');

        $this->assertSame('//example.com/', $urlWithEmptyScheme->asString());
    }

    public function testWithScheme(): void
    {
        $url = $this->url->withScheme('ftp');

        $this->assertSame('ftp', $url->getScheme());
        $this->assertSame('http', $this->url->getScheme());
    }


    public function testClone(): void
    {
        $clonedUrl = clone $this->url;

        $clonedUrl->setQueryParameter('cloned', 'yes');

        $this->assertSame('yes', $clonedUrl->getQueryParameter('cloned'));
        $this->assertSame('', $this->url->getQueryParameter('cloned'));
    }

    public function testGetAuthority(): void
    {
        $this->assertSame('login:pass@example.com:443', $this->url->getAuthority());
    }

    public function testClearQuery(): void
    {
        $this->url->clearQuery();

        $this->assertSame('', $this->url->getQuery());
    }

    public function testCreatingFailed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Url('example.com');
    }

    public function testGetScheme(): void
    {
        $this->assertSame('http', $this->url->getScheme());
    }

    public function testGetUser(): void
    {
        $this->assertSame('login', $this->url->getUser());
    }

    public function testGetPass(): void
    {
        $this->assertSame('pass', $this->url->getPass());
    }

    public function testGetHost(): void
    {
        $this->assertSame('example.com', $this->url->getHost());
    }

    public function testGetPath(): void
    {
        $this->assertSame('/path', $this->url->getPath());
    }

    public function testGetPort(): void
    {
        $this->assertSame(443, $this->url->getPort());
    }

    public function testGetEmptyPort(): void
    {
        $this->assertNull((new Url('http://example.com'))->getPort());
    }

    public function testGetQuery(): void
    {
        $this->assertSame('foo=bar&bar=foo', $this->url->getQuery());
    }

    public function testToString(): void
    {
        $this->assertSame('http://login:pass@example.com:443/path?foo=bar&bar=foo#hash', $this->url . '');
    }
}
