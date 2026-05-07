<?php
// Clear Laravel cache manually
$cacheDir = __DIR__ . '/bootstrap/cache/';
$files = ['config.php', 'packages.php', 'services.php'];

foreach ($files as $file) {
    $filePath = $cacheDir . $file;
    if (file_exists($filePath)) {
        unlink($filePath);
        echo "Deleted: $filePath\n";
    }
}

echo "\n✅ Cache cleared!\n";
echo "Please refresh browser and try again.\n";
?>
