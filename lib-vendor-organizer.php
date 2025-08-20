#!/usr/bin/env php
<?php

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

$sourceDirectory = $argv[1];
$sourceDirectory = rtrim($sourceDirectory, '/') . '/';

if (!str_starts_with('/', $sourceDirectory)) {
	$sourceDirectory = getcwd() . '/' . $sourceDirectory;
}

$stripNamespacePrefix = $argv[2] ?? '';
if ($stripNamespacePrefix) {
	printf("Namespace Prefix to strip from destination dir is %s%s", $stripNamespacePrefix, PHP_EOL);
}

if (!file_exists($sourceDirectory) || !is_dir($sourceDirectory)) {
	print("Directory not found");
	exit(1);
}
$organizationList = [];
foreach(scandir($sourceDirectory) as $file) {
	if (!is_dir($sourceDirectory . $file) || $file === '.' || $file === '..') {
		continue;
	}
	$organizationList[] = $sourceDirectory . $file . '/';
}

$projectList = [];
foreach($organizationList as $organizationDir) {
	foreach(scandir($organizationDir) as $file) {
		if (!is_dir($organizationDir . $file) || $file === '.' || $file === '..') {
			continue;
		}
		$projectList[] = $organizationDir . $file . '/';
	}
}

$destinations = array();
foreach ($projectList as $projectDir) {
	if (!file_exists($projectDir . 'composer.json')) {
		continue;
	}

	$projectInfo = json_decode(file_get_contents($projectDir . 'composer.json'), true);
    if (!isset($projectInfo['autoload']['psr-4'])) {
		printf("No supported autoload configuration in %s" . PHP_EOL, $projectDir);
		exit(2);
	}
	foreach ($projectInfo['autoload']['psr-4'] as $namespace => $codeDir) {
		if ($stripNamespacePrefix !== '' && str_starts_with($namespace, $stripNamespacePrefix)) {
			$namespace = str_replace($stripNamespacePrefix, '', $namespace);
		}
		$destination = rtrim($sourceDirectory, '/') . str_replace('\\', '/', $namespace);
        $destinations = insertion_sort($destinations, [
                "destination"=>$destination,
                "codeDir"=>$codeDir,
                "projectDir"=>$projectDir,
        ]);
    }
}

foreach ($destinations as $item) {
    $destination = $item["destination"];
    $codeDir = $item["codeDir"];
    $projectDir = $item["projectDir"];

    if (file_exists($destination)) {
        rmdir_recursive($destination);
    }
    mkdir($destination, 0777, true);

    if (!rename_or_move($projectDir . $codeDir, $destination)) {
        printf("Failed to move %s to %s" . PHP_EOL, $projectDir . $codeDir, $destination);
        exit(3);
    }
}

function insertion_sort($array, $element): array
{
    for($i = 0; $i < count($array); $i++) {
        $compare = strcmp($array[$i]["destination"], $element["destination"]);
        if($compare > 0) {
            array_splice($array, $i, 0, [$element]);
            return $array;
        }else if($compare == 0){
            return $array;
        }
    }
    $array[] = $element;
    return $array;
}

foreach($organizationList as $organizationDir) {
	rmdir_recursive($organizationDir);
}

function rmdir_recursive($dir): void
{
	foreach(scandir($dir) as $file) {
		if ('.' === $file || '..' === $file) {
			continue;
		}
		if (is_dir("$dir/$file")) {
			rmdir_recursive("$dir/$file");
		} else {
			unlink("$dir/$file");
		}
	}
	rmdir($dir);
}

function rename_or_move(string $orig, string $dest): bool {
	if (@rename($orig, $dest)) {
		return true;
	}

	foreach (scandir($orig) as $file) {
		if ('.' === $file || '..' === $file) {
			continue;
		}
		if (!rename_or_move("$orig/$file", "$dest/$file")) {
			return false;
		}
	}

	return true;
}

