document.addEventListener("DOMContentLoaded", () => {
  fetch("http://localhost/Shirtastic-Webshop/Backend/api/admin_orders.php")
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById("adminOrderContainer");

      if (data.status !== "ok") {
        container.innerHTML = "<p>Error loading orders.</p>";
        return;
      }

      if (data.orders.length === 0) {
        container.innerHTML = "<p>No orders found.</p>";
        return;
      }

      data.orders.forEach(order => {
        const div = document.createElement("div");
        div.className = "order-card";

        const itemsList = order.items.map(item =>
          `<li>${item.product_name} - €${item.product_price}</li>`
        ).join("");

        // 下拉菜单 HTML
        const statusOptions = ["Pending", "Shipped", "Cancelled"]
          .map(status => {
            const selected = status === order.status ? "selected" : "";
            return `<option value="${status}" ${selected}>${status}</option>`;
          }).join("");

        div.innerHTML = `
          <h3>Order #${order.id}</h3>
          <p><strong>User:</strong> ${order.user}</p>
          <p><strong>Address:</strong> ${order.address}</p>
          <p><strong>Date:</strong> ${order.created_at}</p>
          <p><strong>Voucher:</strong> ${order.voucher_code || "None"}</p>
          <p><strong>Discount:</strong> €${order.discount_applied}</p>
          <p><strong>Total:</strong> €${order.total}</p>
          <ul><strong>Items:</strong><br>${itemsList}</ul>

          <label><strong>Status:</strong></label>
          <select data-order-id="${order.id}" class="order-status">
            ${statusOptions}
          </select>
          <button class="save-status" data-order-id="${order.id}">Save</button>
          <button class="delete-btn" data-order-id="${order.id}">Delete</button>
          <div class="status-msg" id="msg-${order.id}"></div>
          <hr/>
        `;

        container.appendChild(div);
      });


      // 监听状态保存按钮
document.querySelectorAll(".save-status").forEach(button => {
  button.addEventListener("click", () => {
    const orderId = button.getAttribute("data-order-id");
    const select = document.querySelector(`select[data-order-id="${orderId}"]`);
    const newStatus = select.value;

    fetch("http://localhost/Shirtastic-Webshop/Backend/api/admin_orders_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ orderId, status: newStatus })
    })
      .then(res => res.json())
      .then(resp => {
        const msgDiv = document.getElementById(`msg-${orderId}`);
        if (resp.status === "ok") {
          msgDiv.textContent = "Status updated successfully.";
          msgDiv.style.color = "green";
        } else {
          msgDiv.textContent = "Update failed: " + resp.message;
          msgDiv.style.color = "red";
        }
      })
      .catch(err => {
        const msgDiv = document.getElementById(`msg-${orderId}`);
        msgDiv.textContent = "Server error.";
        msgDiv.style.color = "red";
      });
  });
});

// ✅ 正确绑定删除按钮监听器（必须放这里）
document.querySelectorAll(".delete-btn").forEach(button => {
  button.addEventListener("click", () => {
    const orderId = button.getAttribute("data-order-id");

    if (confirm("Are you sure you want to delete this order?")) {
      fetch("http://localhost/Shirtastic-Webshop/Backend/api/admin_orders_delete.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ orderId })
      })
        .then(res => res.json())
        .then(data => {
          if (data.status === "ok") {
            alert("Order deleted.");
            location.reload();
          } else {
            alert("Error deleting order.");
          }
        })
        .catch(err => {
          console.error("Delete error:", err);
          alert("Server error.");
        });
    }
  });
});


});
})
