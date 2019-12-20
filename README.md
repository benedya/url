### Simple URl manipulator

## How to use
```php
    $url = (new \Benedya\Url\Url('http://example.com/path?foo=bar&bar=foo'))
        ->withScheme('https')
        ->withHost('google.com')
        ->withPath('/')
        ->clearQuery()
    ;
        
    // Output https://google.com/
    echo $url->asString();
```

Dependencies
-------

- [PSR-7](http://www.php-fig.org/psr/psr-7/)