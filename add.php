 <?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/lib/products.php';

$errors = [];
$values = ['name' => '', 'price' => '', 'sku' => '', 'stock' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name']  = trim((string) ($_POST['name'] ?? ''));
    $values['price'] = trim((string) ($_POST['price'] ?? ''));
    $values['sku']   = trim((string) ($_POST['sku'] ?? ''));
    $values['stock'] = trim((string) ($_POST['stock'] ?? ''));

    $errors = validateProductInput($values['name'], $values['price'], $values['sku'], $values['stock']);

    if (empty($errors)) {
        addProduct($pdo, $values['name'], (float) $values['price'], $values['sku'], (int) $values['stock']);
        header('Location: index.php?flash=added');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати товар — Wellness Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="hero">
        <span class="eyebrow">Практична робота №4 · Wellness Edit</span>
        <h1>Додати <em>товар</em></h1>
        <p class="subtitle">Дані зберігаються в MySQL через підготовлений INSERT-запит.</p>
    </div>

    <div class="card form-card">
        <form method="post" action="add.php" novalidate>
            <div class="field">
                <label for="name">Назва товару</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($values['name']) ?>" required minlength="2">
                <?php if (isset($errors['name'])): ?><span class="error"><?= htmlspecialchars($errors['name']) ?></span><?php endif; ?>
            </div>

            <div class="field-row">
                <div class="field">
                    <label for="price">Ціна, грн</label>
                    <input type="number" id="price" name="price" value="<?= htmlspecialchars($values['price']) ?>" required min="0.01" step="0.01">
                    <?php if (isset($errors['price'])): ?><span class="error"><?= htmlspecialchars($errors['price']) ?></span><?php endif; ?>
                </div>

                <div class="field">
                    <label for="stock">Залишок, шт.</label>
                    <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($values['stock']) ?>" required min="0" step="1">
                    <?php if (isset($errors['stock'])): ?><span class="error"><?= htmlspecialchars($errors['stock']) ?></span><?php endif; ?>
                </div>
            </div>

            <div class="field">
                <label for="sku">SKU (формат ABC-123)</label>
                <input type="text" id="sku" name="sku" value="<?= htmlspecialchars($values['sku']) ?>" required pattern="[A-Za-z]+-\d+" placeholder="MAT-045">
                <?php if (isset($errors['sku'])): ?><span class="error"><?= htmlspecialchars($errors['sku']) ?></span><?php endif; ?>
            </div>

            <button type="submit" class="btn-primary">Зберегти товар</button>
            <a href="index.php" class="btn-secondary-link">Назад до каталогу</a>
        </form>
    </div>
</body>
</html>