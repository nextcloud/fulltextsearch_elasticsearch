<?php

/*
 * This code is licensed under the BSD 3-Clause License.
 *
 * Copyright (c) 2022, Théo FIDRY <theo.fidry@gmail.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Fidry\FileSystem;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Filesystem\Path;
use Webmozart\Assert\Assert;
use function error_get_last;
use function file_get_contents;
use function random_int;
use function realpath;
use function sprintf;
use function str_replace;
use function sys_get_temp_dir;
use const DIRECTORY_SEPARATOR;

class FileSystem extends SymfonyFilesystem
{
    /**
     * Returns whether a path is relative.
     *
     * @param string $path a path string
     *
     * @return bool returns true if the path is relative or empty, false if
     *              it is absolute
     */
    public function isRelativePath(string $path): bool
    {
        return !$this->isAbsolutePath($path);
    }

    public function escapePath(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    public function dumpFile(string $filename, $content = ''): void
    {
        parent::dumpFile($filename, $content);
    }

    /**
     * Gets the contents of a file.
     *
     * @param string $file File path
     *
     * @throws IOException If the file cannot be read
     *
     * @return string File contents
     */
    public function getFileContents(string $file): string
    {
        Assert::file($file);
        Assert::readable($file);

        if (false === ($contents = @file_get_contents($file))) {
            throw new IOException(
                sprintf(
                    'Failed to read file "%s": %s.',
                    $file,
                    error_get_last()['message'],
                ),
                0,
                null,
                $file,
            );
        }

        return $contents;
    }

    /**
     * Creates a temporary directory.
     *
     * @param string $namespace the directory path in the system's temporary directory
     * @param string $className the name of the test class
     *
     * @return string the path to the created directory
     */
    public function makeTmpDir(string $namespace, string $className): string
    {
        $shortClass = false !== ($pos = mb_strrpos($className, '\\'))
            ? mb_substr($className, $pos + 1)
            : $className;

        $basePath = $this->getNamespacedTmpDir($namespace).'/'.$shortClass;

        $result = false;
        $attempts = 0;

        do {
            $tmpDir = $this->escapePath($basePath.random_int(10000, 99999));

            if ($this->exists($tmpDir)) {
                ++$attempts;

                continue;
            }

            try {
                $this->mkdir($tmpDir, 0o777);

                $result = true;
            } catch (IOException) {
                ++$attempts;
            }
        } while (false === $result && $attempts <= 10);

        return $tmpDir;
    }

    /**
     * Gets a namespaced temporary directory.
     *
     * @param string $namespace the directory path in the system's temporary directory
     */
    public function getNamespacedTmpDir(string $namespace): string
    {
        // Usage of realpath() is important if the temporary directory is a
        // symlink to another directory (e.g. /var => /private/var on some Macs)
        // We want to know the real path to avoid comparison failures with
        // code that uses real paths only
        $systemTempDir = str_replace('\\', '/', realpath(sys_get_temp_dir()));

        return $systemTempDir.'/'.$namespace;
    }
}
