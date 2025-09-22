document.addEventListener('DOMContentLoaded', function() {
  const productSelect = document.getElementById('product');
  const quantityInput = document.getElementById('quantity');
  const pricePerUnitSpan = document.getElementById('pricePerUnit');
  const totalPriceSpan = document.getElementById('totalPrice');

  function updatePrice() {
    let pricePerUnit = 0;
    if (productSelect.selectedOptions.length > 0) {
      pricePerUnit = parseFloat(productSelect.selectedOptions[0].dataset.price) || 0;
    }
    const quantity = parseInt(quantityInput.value) || 0;
    pricePerUnitSpan.textContent = pricePerUnit.toFixed(2).replace('.', ',') + ' Kč';
    totalPriceSpan.textContent = (pricePerUnit * quantity).toFixed(2).replace('.', ',') + ' Kč';
  }

  productSelect.addEventListener('change', updatePrice);
  quantityInput.addEventListener('input', updatePrice);

  updatePrice();
});
