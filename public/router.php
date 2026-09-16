<?php

// Ce routeur permet au serveur PHP intégré de gérer
// correctement les assets dynamiques (AssetMapper) de Symfony.

$path = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false; // sert le fichier tel quel (CSS statique, images, etc.)
}

require_once __DIR__ . '/index.php';