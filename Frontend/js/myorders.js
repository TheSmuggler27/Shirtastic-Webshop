document.addEventListener("DOMContentLoaded", () => {
  const userId = localStorage.getItem("userId");
  const container = document.getElementById("ordersContainer");

  if (!userId) {
    container.innerHTML = `<p>You must be logged in to view your orders.</p>`;
    return;
  }

  fetch("http://localhost/Shirtastic-Webshop/Backend/api/get_orders.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ userId })
  })
    .then(res => res.json())
    .then(data => {
      if (data.status !== "ok") {
        container.innerHTML = `<p>Error loading orders.</p>`;
        return;
      }

      if (data.orders.length === 0) {
        container.innerHTML = `<p>No orders found.</p>`;
        return;
      }

      data.orders.forEach(order => {
  const orderDiv = document.createElement("div");
  orderDiv.className = "order-card";

  const discount = parseFloat(order.discount_applied || 0);
  const finalTotal = Math.max(0, order.total - discount);

  orderDiv.innerHTML = `
    <h3>Order #${order.id}</h3>
    <p><strong>Status:</strong> ${order.status || "Pending"}</p>
    <p><strong>Name:</strong> ${order.name}</p>
    <p><strong>Address:</strong> ${order.address}</p>
    <p><strong>Created:</strong> ${order.created_at}</p>
    <ul>
      ${
        Object.entries(
          order.items.reduce((acc, item) => {
            const key = `${item.product_name}||${item.product_price}`;
            acc[key] = acc[key] || { name: item.product_name, price: item.product_price, qty: 0 };
            acc[key].qty += 1;
            return acc;
          }, {})
        )
        .map(([_, item]) => `<li>${item.name} - €${item.price} × ${item.qty}</li>`)
        .join("")
      }
    </ul>
    <p><strong>Discount:</strong> €${discount.toFixed(2)}</p>
    <p><strong>Voucher Code:</strong> ${order.voucher_code || "None"}</p>
    <p><strong>Total:</strong> €${finalTotal.toFixed(2)}</p>
    <button onclick="printOrder(this)" class="cta-button">Print Invoice</button>
  `;

      container.appendChild(orderDiv);
    });

    })
    .catch(err => {
      console.error("Error:", err);
      container.innerHTML = `<p>Server error while loading orders.</p>`;
    });
});

function printOrder(button) {
  const orderCard = button.closest(".order-card");
  const printWindow = window.open('', '', 'width=800,height=700');
  printWindow.document.write('<html><head><title>Invoice</title>');
  printWindow.document.write('<link rel="stylesheet" href="../css/style.css">');
  printWindow.document.write('</head><body>');
  printWindow.document.write(orderCard.outerHTML);
  printWindow.document.write('</body></html>');
  printWindow.document.close();
  printWindow.print();
}

