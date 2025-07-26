#!/usr/bin/env php
<?php
/**
 * PHP Compatibility Fix Script
 * This script replaces null coalescing operators (??) with isset() ternary operators
 * for compatibility with PHP versions < 7.0
 */

function fixNullCoalescingInFile($filePath) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Pattern to match: $variable ?? 'default'
    $pattern = '/\$([a-zA-Z_][a-zA-Z0-9_]*(?:\[[^\]]*\])*)\s*\?\?\s*([^;,\)]+)/';
    
    $content = preg_replace_callback($pattern, function($matches) {
        $variable = '$' . $matches[1];
        $default = trim($matches[2]);
        return "isset({$variable}) ? {$variable} : {$default}";
    }, $content);
    
    // Pattern to match: $_SESSION['key'] ?? 'default'
    $sessionPattern = '/\$_SESSION\[([^\]]+)\]\s*\?\?\s*([^;,\)]+)/';
    
    $content = preg_replace_callback($sessionPattern, function($matches) {
        $key = $matches[1];
        $default = trim($matches[2]);
        return "isset(\$_SESSION[{$key}]) ? \$_SESSION[{$key}] : {$default}";
    }, $content);
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "Fixed: $filePath\n";
        return true;
    }
    
    return false;
}

// Get all PHP files in the project
$files = [];
$directories = ['backend', 'includes', 'sections'];

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
    }
}

// Also include root PHP files
$rootFiles = glob('*.php');
$files = array_merge($files, $rootFiles);

$fixedCount = 0;
foreach ($files as $file) {
    if (fixNullCoalescingInFile($file)) {
        $fixedCount++;
    }
}

echo "\nFixed {$fixedCount} files.\n";
echo "All null coalescing operators have been replaced with isset() ternary operators.\n";
?>
