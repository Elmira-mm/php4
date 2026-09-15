<?php
declare(strict_types=1);

/**
 * Функції для роботи з таблицею products через PDO.
 * Усі запити з даними користувача — виключно підготовлені (prepared statements).
 */

/**
 * Крок 3. Повертає всі товари.
 */
function getAllProducts(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM products ORDER BY id')->fetchAll();
}

/**
 * Повертає один товар за id (для форми редагування) або null.
 */
function getProductById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    return $row !== false ? $row : null;
}

/**
 * Вибірка за SKU — findBySku($sku).
 */
function findBySku(PDO $pdo, string $sku): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE sku = :sku');
    $stmt->execute([':sku' => $sku]);
    $row = $stmt->fetch();

    return $row !== false ? $row : null;
}

/**
 * Агрегат — totalStockValue(): сумарна вартість складу через SQL SUM().
 */
function totalStockValue(PDO $pdo): float
{
    $row = $pdo->query('SELECT SUM(price * stock) AS total FROM products')->fetch();

    return (float) ($row['total'] ?? 0);
}

/**
 * Крок 4. Додавання товару — addProduct().
 */
function addProduct(PDO $pdo, string $name, float $price, string $sku, int $stock): void
{
    $stmt = $pdo->prepare(
        'INSERT INTO products (name, price, sku, stock) VALUES (:name, :price, :sku, :stock)'
    );
    $stmt->execute([
        ':name'  => $name,
        ':price' => $price,
        ':sku'   => strtoupper($sku),
        ':stock' => $stock,
    ]);
}

/**
 * Крок 5. Редагування товару — updateProduct($id, ...).
 */
function updateProduct(PDO $pdo, int $id, string $name, float $price, string $sku, int $stock): bool
{
    $stmt = $pdo->prepare(
        'UPDATE products SET name = :name, price = :price, sku = :sku, stock = :stock WHERE id = :id'
    );
    $stmt->execute([
        ':name'  => $name,
        ':price' => $price,
        ':sku'   => strtoupper($sku),
        ':stock' => $stock,
        ':id'    => $id,
    ]);

    return $stmt->rowCount() > 0;
}

/**
 * Крок 6. Видалення товару.
 */
function deleteProduct(PDO $pdo, int $id): bool
{
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);

    return $stmt->rowCount() > 0;
}

/**
 * Валідація полів форми (спільна для add.php і edit.php).
 * Ті самі правила, що й у практикумі №2: price > 0, stock >= 0, sku формату ABC-123.
 */
function validateProductInput(string $name, string $price, string $sku, string $stock): array
{
    $errors = [];

    if (trim($name) === '') {
        $errors['name'] = 'Назва товару обов\'язкова.';
    }

    if ($price === '' || !is_numeric($price) || (float) $price <= 0) {
        $errors['price'] = 'Ціна має бути числом більшим за 0.';
    }

    if ($sku === '' || !preg_match('/^[A-Za-z]+-\d+$/', $sku)) {
        $errors['sku'] = 'Формат SKU: літери-дефіс-цифри, наприклад MAT-045.';
    }

    if ($stock === '' || filter_var($stock, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]) === false) {
        $errors['stock'] = 'Залишок має бути цілим невід\'ємним числом.';
    }

    return $errors;
}