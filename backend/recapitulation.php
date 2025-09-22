<?php
session_start();

if (!isset($_SESSION['order'])) {
  header('Location: ../index.php');
  exit;
}

$order = $_SESSION['order'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="UTF-8" />
  <title>Rekapitulace objednávky</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/recapitulation.css" />
</head>
<body>

<h1>Rekapitulace objednávky</h1>

<table>
  <tr><th>Jméno a příjmení</th><td><?= htmlspecialchars($order['name']) ?></td></tr>
  <tr><th>E-mail</th><td><?= htmlspecialchars($order['email']) ?></td></tr>
  <tr><th>Produkt</th><td><?= htmlspecialchars($order['product_name']) ?></td></tr>
  <tr><th>Cena za kus (CZK)</th><td><?= number_format($order['priceCZK'], 2, ',', ' ') ?> Kč</td></tr>
  <tr><th>Počet kusů</th><td><?= $order['quantity'] ?></td></tr>
  <tr><th>Celkem bez DPH (CZK)</th><td><?= number_format($order['subtotal'], 2, ',', ' ') ?> Kč</td></tr>
  <tr><th>DPH (21 %)</th><td><?= number_format($order['subtotal'] * 0.21, 2, ',', ' ') ?> Kč</td></tr>
  <tr><th>Celkem s DPH (CZK)</th><td id="priceCZK"><?= number_format($order['totalWithTax'], 2, ',', ' ') ?> Kč</td></tr>
</table>

<label for="currency">Vyberte měnu pro přepočet:</label>
<select id="currency" name="currency">
  <?php foreach ($order['currencies'] as $code => $rate): ?>
    <option value="<?= htmlspecialchars($code) ?>" data-rate="<?= $rate ?>"><?= htmlspecialchars($code) ?></option>
  <?php endforeach; ?>
</select>

<div id="convertedPrice">Cena v jiné měně: <?= number_format($order['totalWithTax'], 2, ',', ' ') ?> Kč</div>

<script>
  const currencySelect = document.getElementById('currency');
  const convertedPriceDiv = document.getElementById('convertedPrice');
  const basePriceCZK = <?= json_encode($order['totalWithTax']) ?>;

  function updateConvertedPrice() {
    const rate = parseFloat(currencySelect.selectedOptions[0].dataset.rate) || 1;
    const currency = currencySelect.value;
    const converted = basePriceCZK / rate;
    convertedPriceDiv.textContent = `Cena v jiné měně (${currency}): ${converted.toFixed(2)}`;
  }

  currencySelect.addEventListener('change', updateConvertedPrice);
  updateConvertedPrice();
</script>

<a href="../index.php" class="button back-link">Nová objednávka</a>

</body>
</html>
