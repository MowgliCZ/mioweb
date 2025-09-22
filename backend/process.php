<?php
session_start();

define('DPH_PERCENT', 0.21);

$products = include 'products.php';


function validate_input($data) {
  return htmlspecialchars(stripslashes(trim($data)));
}

function loadCnbRates($date) {
  $url = "https://www.cnb.cz/cs/financni-trhy/devizovy-trh/kurzy-devizoveho-trhu/kurzy-devizoveho-trhu/denni_kurz.xml?date=$date";

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_TIMEOUT, 10);

  $response = curl_exec($curl);
  curl_close($curl);

  if ($response === false) return false;

  $xml = simplexml_load_string($response);
  if ($xml === false || !isset($xml->tabulka->radek) || count($xml->tabulka->radek) === 0) return false;

  $currencies = ['CZK' => 1.0];
  foreach ($xml->tabulka->radek as $rate) {
    $code = (string) $rate['kod'];
    $amountRaw = trim((string) $rate['mnozstvi']);
    if ($amountRaw === '' || $amountRaw === '0') continue;
    $amount = (int) $amountRaw;
    $kursRaw = (string) $rate['kurz'];
    $kurs = (float) str_replace(',', '.', $kursRaw);
    if ($amount === 0) continue;
    $currencies[$code] = $kurs / $amount;
  }
  return $currencies;
}

$today = date('d.m.Y');
$currencies = loadCnbRates($today);

if ($currencies === false) {
  die('Nepodařilo se načíst kurzovní lístek ČNB pro datum ' . htmlspecialchars($today) . '.');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = validate_input($_POST['name'] ?? '');
  $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
  $product_key = $_POST['product'] ?? '';
  $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_VALIDATE_INT);

  $errors = [];
  if (!$name) $errors[] = 'Jméno je povinné.';
  if (!$email) $errors[] = 'Neplatný e-mail.';
  if (!array_key_exists($product_key, $products)) $errors[] = 'Neplatný produkt.';
  if ($quantity === false || $quantity < 1) $errors[] = 'Neplatný počet kusů.';

  if (count($errors) > 0) {
    die('Chyba dat: ' . implode(', ', $errors));
  }

  $product_name = $products[$product_key]['name'];
  $priceCZK = $products[$product_key]['price'];
  $subtotalCZK = $priceCZK * $quantity;
  $totalWithTaxCZK = $subtotalCZK * (1 + DPH_PERCENT);

  $_SESSION['order'] = [
    'name' => $name,
    'email' => $email,
    'product_key' => $product_key,
    'product_name' => $product_name,
    'priceCZK' => $priceCZK,
    'quantity' => $quantity,
    'subtotal' => $subtotalCZK,
    'totalWithTax' => $totalWithTaxCZK,
    'currencies' => $currencies,
  ];

  header('Location: recapitulation.php');
  exit;
} else {
  header('Location: ../index.php');
  exit;
}
