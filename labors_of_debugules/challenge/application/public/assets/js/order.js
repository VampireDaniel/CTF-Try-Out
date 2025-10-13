function buyProduct(productId, quantity = 1) {
  fetch('order.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'X-Requested-With': 'XMLHttpRequest'  // Signal this is an AJAX request
    },
    body: new URLSearchParams({
      product_id: productId,
      quantity: quantity
    })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network error: ' + response.statusText);
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      alert(data.message);
      // Optionally, update UI or refresh page after successful purchase.
    } else {
      alert("Order failed: " + data.message);
    }
  })
  .catch(error => {
    alert("An error occurred: " + error.message);
  });
}
