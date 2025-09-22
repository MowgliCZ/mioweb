<?php

$products = include './backend/products.php';

?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8" />
  <title>Objednávkový formulář</title>
  <link rel="stylesheet" href="css/style.css" />
  <script src="js/app.js"></script>
</head>
<body>

<h1>Objednávkový formulář</h1>

<form method="post" action="backend/process.php">
  <table>
    <tr>
      <th><label for="name">Jméno a příjmení:</label></th>
      <td><input type="text" name="name" id="name" required /></td>
    </tr>
    <tr>
      <th><label for="email">E-mail:</label></th>
      <td><input type="email" name="email" id="email" required /></td>
    </tr>
    <tr>
      <th><label for="product">Produkt:</label></th>
      <td>
        <select name="product" id="product" required>
          <option value="">-- Vyberte produkt --</option>
          <?php foreach ($products as $key => $product): ?>
            <option value="<?= htmlspecialchars($key) ?>" data-price="<?= $product['price'] ?>">
              <?= htmlspecialchars($product['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <tr>
      <th>Cena za kus:</th>
      <td><span id="pricePerUnit">0,00 Kč</span></td>
    </tr>
    <tr>
      <th><label for="quantity">Počet kusů:</label></th>
      <td><input type="number" name="quantity" id="quantity" min="1" value="1" required /></td>
    </tr>
    <tr>
      <th>Celkem:</th>
      <td><span id="totalPrice">0,00 Kč</span></td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center;">
        <button type="submit">Odeslat objednávku</button>
      </td>
    </tr>
  </table>
</form>

</body>
</html>
