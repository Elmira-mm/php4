<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/lib/products.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id !== null && $id !== false) {
    deleteProduct($pdo, $id);
    header('Location: index.php?flash=deleted');
    exit;
}

header('Location: index.php');
exit;