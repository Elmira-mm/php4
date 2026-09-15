<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/lib/products.php';

/**
 * Крок 3. Виведення всіх записів через SELECT (fetchAll всередині getAllProducts()).
 */
$products = getAllProducts($pdo);
$totalValue = totalStockValue($pdo);

// Демонстрація findBySku() через пошукову форму (GET ?sku=...)
$searchSku = trim((string) ($_GET['sku'] ?? ''));
$foundProduct = $searchSku !== '' ? findBySku($pdo, $searchSku) : null;

// Повідомлення після редіректу з add.php / edit.php / delete.php
$flash = $_GET['flash'] ?? null;
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Каталог товарів — БД — Wellness Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="hero">
        <span class="eyebrow">Практична робота №4 · Wellness Edit</span>
        <h1>Каталог <em>у базі даних</em></h1>
        <p class="subtitle">Той самий каталог, тепер збережений у MySQL через PDO з підготовленими запитами.</p>
    </div>

    <?php if ($flash === 'added'): ?>
        <div class="alert alert-success"><strong>Товар додано.</strong></div>
    <?php elseif ($flash === 'updated'): ?>
        <div class="alert alert-success"><strong>Товар оновлено.</strong></div>
    <?php elseif ($flash === 'deleted'): ?>
        <div class="alert alert-success"><strong>Товар видалено.</strong></div>
    <?php endif; ?>

    <div class="card form-card">
        <form method="get" action="index.php" class="search-form">
            <div class="field">
                <label for="sku">Пошук за SKU (findBySku)</label>
                <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($searchSku) ?>" placeholder="Напр. MAT-003">
            </div>
            <button type="submit" class="btn-primary">Знайти</button>
            <?php if ($searchSku !== ''): ?>
                <a href="index.php" class="btn-secondary-link">Скинути</a>
            <?php endif; ?>
        </form>

        <?php if ($searchSku !== ''): ?>
            <?php if ($foundProduct !== null): ?>
                <div class="alert alert-success" style="margin-top:16px;">
                    <strong>Знайдено:</strong>
                    <span><?= htmlspecialchars($foundProduct['name']) ?> — <?= number_format((float) $foundProduct['price'], 2, '.', ' ') ?> грн, залишок <?= (int) $foundProduct['stock'] ?> шт.</span>
                </div>
            <?php else: ?>
                <div class="alert alert-empty" style="margin-top:16px;">
                    Товар з SKU «<?= htmlspecialchars($searchSku) ?>» не знайдено.
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>SKU</th>
                    <th>Ціна</th>
                    <th>Залишок, шт.</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <?php $inStock = (int) $product['stock'] > 0; ?>
                    <tr class="<?= $inStock ? 'row-in' : 'row-out' ?>">
                        <td><span class="product-name"><?= htmlspecialchars($product['name']) ?></span></td>
                        <td class="stock"><?= htmlspecialchars($product['sku']) ?></td>
                        <td>
                            <span class="price"><?= number_format((float) $product['price'], 2, '.', ' ') ?></span><span class="price-currency">грн</span>
                        </td>
                        <td class="stock"><?= (int) $product['stock'] ?></td>
                        <td>
                            <span class="badge <?= $inStock ? 'badge-in' : 'badge-out' ?>">
                                <?= $inStock ? 'В наявності' : 'Немає в наявності' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="edit.php?id=<?= (int) $product['id'] ?>" class="action-link">Редагувати</a>
                            <form method="post" action="delete.php" class="inline-form" onsubmit="return confirm('Видалити «<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>»?');">
                                <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                <button type="submit" class="action-link action-link--danger">Видалити</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="empty-row">Товарів поки немає — додайте перший нижче.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Сумарна вартість складу (SQL SUM)</div>
            <div class="value"><?= number_format($totalValue, 2, '.', ' ') ?> <span class="accent">грн</span></div>
        </div>
        <div class="summary-item">
            <div class="label">Товарів у каталозі</div>
            <div class="value"><?= count($products) ?></div>
        </div>
    </div>

    <div class="card form-card">
        <h2 class="section-title">Додати товар</h2>
        <a href="add.php" class="btn-primary btn-block-link">Перейти до форми додавання</a>
    </div>
</body>
</html>