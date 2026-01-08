# Filesystem

This is a tiny wrapper around the [Symfony filesystem]. It provides:

- A new class `FileSystem` (extending the Symfony one) which has a few more methods.
- A `FS` static class for when you are not interested of using dependency injection for your filesystem layer.
- A PHPUnit `FileSystemTestCase` which lets you easily write a file-system dependent test.


## Usage

An example of PHPUnit test:

```php

<?php declare(strict_types=1);

namespace App;

use Fidry\FileSystem\FS;
use Fidry\FileSystem\Test\FileSystemTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Finder\Finder;
use function getenv;
use function is_string;

final class MyAppFileSystemTest extends FileSystemTestCase
{
    public static function getTmpDirNamespace(): string
    {
        // This is to make it thread safe with Infection. If you are not using
        // infection or do not need thread safety, this can return a constant
        // string, e.g. your project/library name.
        $threadId = getenv('TEST_TOKEN');

        if (!is_string($threadId)) {
            $threadId = '';
        }

        return 'MyApp'.$threadId;
    }

    public function test_it_works(): void
    {
        // This file is dumped into a temporary directory. Here
        // something like '/private/var/folders/p3/lkw0cgjj2fq0656q_9rd0mk80000gn/T/MyApp/MyAppFileSystemTest10000'
        // on OSX. 
        FS::dumpFile('file1', '');
        
        $files = Finder::create()
            ->files()
            ->in($this->tmp);

        self::assertSame(['file1'], $this->normalizePaths($files));
    }
}

```

## Contributing

[GNU Make] is your friend. Try `make` or `make help`!.


[Symfony filesystem]: https://symfony.com/doc/current/components/filesystem.html
[GNU Make]: https://www.gnu.org/software/make/
