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

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Path;
use Traversable;
use function func_get_args;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Thomas Schulz <mail@king2500.net>
 */
class FS
{
    private static FileSystem $filesystem;

    public static function setInstance(FileSystem $filesystem): void
    {
        self::$filesystem = $filesystem;
    }

    public static function getInstance(): FileSystem
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset(self::$filesystem)) {
            self::$filesystem = new FileSystem();
        }

        return self::$filesystem;
    }

    /**
     * Copies a file.
     *
     * If the target file is older than the origin file, it's always overwritten.
     * If the target file is newer, it is overwritten only when the
     * $overwriteNewerFiles option is set to true.
     *
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws IOException           When copy fails
     */
    public static function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false): void
    {
        self::getInstance()->copy(...func_get_args());
    }

    /**
     * Creates a directory recursively.
     *
     * @throws IOException On any directory creation failure
     */
    public static function mkdir(iterable|string $dirs, int $mode = 0o777): void
    {
        self::getInstance()->mkdir(...func_get_args());
    }

    /**
     * Checks the existence of files or directories.
     */
    public static function exists(iterable|string $files): bool
    {
        return self::getInstance()->exists($files);
    }

    /**
     * Sets access and modification time of file.
     *
     * @param int|null $time  The touch time as a Unix timestamp, if not supplied the current system time is used
     * @param int|null $atime The access time as a Unix timestamp, if not supplied the current system time is used
     *
     * @throws IOException When touch fails
     */
    public static function touch(iterable|string $files, ?int $time = null, ?int $atime = null): void
    {
        self::getInstance()->touch(...func_get_args());
    }

    /**
     * Removes files or directories.
     *
     * @throws IOException When removal fails
     */
    public static function remove(iterable|string $files): void
    {
        self::getInstance()->remove(...func_get_args());
    }

    /**
     * Change mode for an array of files or directories.
     *
     * @param int  $mode      The new mode (octal)
     * @param int  $umask     The mode mask (octal)
     * @param bool $recursive Whether change the mod recursively or not
     *
     * @throws IOException When the change fails
     */
    public static function chmod(iterable|string $files, int $mode, int $umask = 0o000, bool $recursive = false): void
    {
        self::getInstance()->chmod(...func_get_args());
    }

    /**
     * Change the owner of an array of files or directories.
     *
     * @param string|int $user      A user name or number
     * @param bool       $recursive Whether change the owner recursively or not
     *
     * @throws IOException When the change fails
     */
    public static function chown(iterable|string $files, int|string $user, bool $recursive = false): void
    {
        self::getInstance()->chown(...func_get_args());
    }

    /**
     * Change the group of an array of files or directories.
     *
     * @param string|int $group     A group name or number
     * @param bool       $recursive Whether change the group recursively or not
     *
     * @throws IOException When the change fails
     */
    public static function chgrp(iterable|string $files, int|string $group, bool $recursive = false): void
    {
        self::getInstance()->chgrp(...func_get_args());
    }

    /**
     * Renames a file or a directory.
     *
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public static function rename(string $origin, string $target, bool $overwrite = false): void
    {
        self::getInstance()->rename(...func_get_args());
    }

    /**
     * Creates a symbolic link or copy a directory.
     *
     * @throws IOException When symlink fails
     */
    public static function symlink(string $originDir, string $targetDir, bool $copyOnWindows = false): void
    {
        self::getInstance()->symlink(...func_get_args());
    }

    /**
     * Creates a hard link, or several hard links to a file.
     *
     * @param string|string[] $targetFiles The target file(s)
     *
     * @throws FileNotFoundException When original file is missing or not a file
     * @throws IOException           When link fails, including if link already exists
     */
    public static function hardlink(string $originFile, iterable|string $targetFiles): void
    {
        self::getInstance()->hardlink(...func_get_args());
    }

    /**
     * Resolves links in paths.
     *
     * With $canonicalize = false (default)
     *      - if $path does not exist or is not a link, returns null
     *      - if $path is a link, returns the next direct target of the link without considering the existence of the target
     *
     * With $canonicalize = true
     *      - if $path does not exist, returns null
     *      - if $path exists, returns its absolute fully resolved final version
     */
    public static function readlink(string $path, bool $canonicalize = false): ?string
    {
        return self::getInstance()->readlink(...func_get_args());
    }

    /**
     * Given an existing path, convert it to a path relative to a given starting path.
     */
    public static function makePathRelative(string $endPath, string $startPath): string
    {
        return self::getInstance()->makePathRelative(...func_get_args());
    }

    /**
     * Mirrors a directory to another.
     *
     * Copies files and directories from the origin directory into the target directory. By default:
     *
     *  - existing files in the target directory will be overwritten, except if they are newer (see the `override` option)
     *  - files in the target directory that do not exist in the source directory will not be deleted (see the `delete` option)
     *
     * @param Traversable|null $iterator Iterator that filters which files and directories to copy, if null a recursive iterator is created
     * @param array            $options  An array of boolean options
     *                                   Valid options are:
     *                                   - $options['override'] If true, target files newer than origin files are overwritten (see copy(), defaults to false)
     *                                   - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink(), defaults to false)
     *                                   - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws IOException When file type is unknown
     */
    public static function mirror(string $originDir, string $targetDir, ?Traversable $iterator = null, array $options = []): void
    {
        self::getInstance()->mirror(...func_get_args());
    }

    /**
     * Returns whether the file path is an absolute path.
     */
    public static function isAbsolutePath(string $file): bool
    {
        return self::getInstance()->isAbsolutePath(...func_get_args());
    }

    /**
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     * @param string $suffix The suffix of the generated temporary filename
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public static function tempnam(string $dir, string $prefix, string $suffix = ''): string
    {
        return self::getInstance()->tempnam(...func_get_args());
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param string|resource $content The data to write into the file
     *
     * @throws IOException if the file cannot be written to
     */
    public static function dumpFile(string $filename, $content = ''): void
    {
        self::getInstance()->dumpFile(...func_get_args());
    }

    /**
     * Appends content to an existing file.
     *
     * @param string|resource $content The content to append
     *
     * @throws IOException If the file is not writable
     */
    public static function appendToFile(string $filename, $content/* , bool $lock = false */): void
    {
        self::getInstance()->appendToFile(...func_get_args());
    }

    /**
     * Returns whether a path is relative.
     *
     * @param string $path a path string
     *
     * @return bool returns true if the path is relative or empty, false if
     *              it is absolute
     */
    public static function isRelativePath(string $path): bool
    {
        return self::getInstance()->isRelativePath(...func_get_args());
    }

    public static function escapePath(string $path): string
    {
        return self::getInstance()->escapePath(...func_get_args());
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
    public static function getFileContents(string $file): string
    {
        return self::getInstance()->getFileContents(...func_get_args());
    }

    /**
     * Creates a temporary directory.
     *
     * @param string $namespace the directory path in the system's temporary directory
     * @param string $className the name of the test class
     *
     * @return string the path to the created directory
     */
    public static function makeTmpDir(string $namespace, string $className): string
    {
        return self::getInstance()->makeTmpDir(...func_get_args());
    }

    /**
     * Gets a namespaced temporary directory.
     *
     * @param string $namespace the directory path in the system's temporary directory
     */
    public static function getNamespacedTmpDir(string $namespace): string
    {
        return self::getInstance()->getNamespacedTmpDir(...func_get_args());
    }
}
