<?php
require_once __DIR__ . '/../../lib/bootstrap.php';
$themes = Theme::getActiveThemes();

header('Content-Type: text/css');

if (isset($themes['light'])) {
    echo ":root {" . PHP_EOL;
    $values = $themes['light']['values'] ?? [];
    foreach ($values as $name => $value) {
        if ($value !== '') {
            echo "    $name: $value;" . PHP_EOL;
        }
    }
    echo "}" . PHP_EOL;
}

foreach ($themes as $themeName => $themeData) {
    if ($themeName === 'high-contrast') {
        echo "@media (prefers-contrast: more) {" . PHP_EOL;
    } elseif (in_array($themeName, ['light', 'dark'])) {
        echo "@media (prefers-color-scheme: $themeName) {" . PHP_EOL;
    } else {
        continue;
    }

    echo "    :root {" . PHP_EOL;
    $values = $themeData['values'] ?? [];
    foreach ($values as $name => $value) {
        if ($value !== '') {
            echo "        $name: $value;" . PHP_EOL;
        }
    }

    echo "    }" . PHP_EOL;
    echo "}" . PHP_EOL;
}
