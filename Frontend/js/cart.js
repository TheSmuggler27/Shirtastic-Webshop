function getCartKey() {
  const uid = localStorage.getItem("userId");
  return uid ? `cart_user_${uid}` : "cart_guest";
}

document.addEventListener("DOMContentLoaded", () => {
  let cart = JSON.parse(localStorage.getItem(getCartKey())) || [];
  const cartList = document.getElementById("cartItems");
  const cartTotal = document.getElementById("cartTotal");
  const discountedTotalEl = document.getElementById("discountedTotal");
  const paymentSelect = document.getElementById("payment");
  const voucherInput = document.getElementById("voucherCode");
  const form = document.getElementById("cartForm");
  const msg = document.getElementById("cartMsg");

  let total = 0;
  let validVoucher = false;
  let voucherDiscount = 0;

  const mergedCart = {};
  cart.forEach(item => {
    const key = item.name + item.price;
    if (mergedCart[key]) {
      mergedCart[key].quantity += 1;
    } else {
      mergedCart[key] = { ...item, quantity: 1 };
    }
  });

  Object.values(mergedCart).forEach(item => {
    const li = document.createElement("li");
    li.innerHTML = `
  <div class="cart-item-row">
    <div class="item-info">
      <span>${item.name} - €${item.price}</span>
    </div>
    <div class="item-actions">
      <span class="qty-label">x</span>
      <input type="number" min="1" value="${item.quantity}" class="quantity-input" data-name="${item.name}" data-price="${item.price}">
      <button class="delete-btn" title="Remove item" data-name="${item.name}" data-price="${item.price}">🗑</button>
    </div>
  </div>
`;



    cartList.appendChild(li);
    total += parseFloat(item.price) * item.quantity;
  });

  cartTotal.textContent = `Total: €${total.toFixed(2)}`;

  cartList.addEventListener("click", (e) => {
    if (e.target.classList.contains("delete-btn")) {
      const name = e.target.getAttribute("data-name");
      const price = e.target.getAttribute("data-price");
      cart = cart.filter(item => !(item.name === name && item.price === price));
      localStorage.setItem(getCartKey(), JSON.stringify(cart));
      location.reload();
    }
  });

  cartList.addEventListener("change", (e) => {
    if (e.target.tagName === "INPUT" && e.target.type === "number") {
      const name = e.target.getAttribute("data-name");
      const price = e.target.getAttribute("data-price");
      const newQty = parseInt(e.target.value);

      cart = cart.filter(item => !(item.name === name && item.price === price));
      for (let i = 0; i < newQty; i++) {
        cart.push({ name, price });
      }

      localStorage.setItem(getCartKey(), JSON.stringify(cart));
      location.reload();
    }
  });

  if (paymentSelect) {
    paymentSelect.addEventListener("change", () => {
      if (paymentSelect.value === "voucher") {
        document.getElementById("voucherSection").style.display = "block";
      } else {
        document.getElementById("voucherSection").style.display = "none";
        discountedTotalEl.textContent = "";
        validVoucher = false;
      }
    });
  }

  const verifyBtn = document.getElementById("verifyVoucherBtn");
  if (verifyBtn) {
    verifyBtn.addEventListener("click", () => {
      const code = voucherInput.value.trim();
      if (!code) {
        discountedTotalEl.textContent = "Please enter a voucher code.";
        discountedTotalEl.style.color = "red";
        validVoucher = false;
        return;
      }

      fetch(`http://localhost/Shirtastic-Webshop/Backend/api/validate_voucher.php?code=${encodeURIComponent(code)}`)
        .then(res => res.json())
        .then(data => {
          if (data.status === "ok") {
            voucherDiscount = parseFloat(data.amount);
            let discounted = total - voucherDiscount;
            if (discounted < 0) discounted = 0;
            discountedTotalEl.textContent = `Discounted Total: €${discounted.toFixed(2)}`;
            discountedTotalEl.style.color = "green";
            validVoucher = true;
          } else {
            discountedTotalEl.textContent = "Invalid voucher code.";
            discountedTotalEl.style.color = "red";
            validVoucher = false;
          }
        })
        .catch(() => {
          discountedTotalEl.textContent = "Error checking voucher.";
          discountedTotalEl.style.color = "red";
          validVoucher = false;
        });
    });
  }

  if (form) {
    form.addEventListener("submit", (e) => {
  e.preventDefault();

  const name = document.getElementById("name").value.trim();
  const address = document.getElementById("address").value.trim();
  const payment = document.getElementById("payment").value;
  const voucherCode = payment === "voucher" ? voucherInput.value.trim() : null;
  const userId = localStorage.getItem("userId");
  const cartItems = JSON.parse(localStorage.getItem(getCartKey()) || "[]");

  if (!userId) {
    alert("You must be logged in to place an order.");
    window.location.href = "login.html";
    return;
  }

  if (!name || !address) {
    msg.textContent = "Please enter name and address.";
    return;
  }

  if (cartItems.length === 0) {
    msg.textContent = "Your cart is empty.";
    return;
  }

  // 计算总价
  let total = cartItems.reduce((sum, item) => sum + parseFloat(item.price), 0);

// 处理优惠券抵扣后的实际支付金额
let finalTotal = total;
if (validVoucher) {
  finalTotal -= voucherDiscount;
  if (finalTotal < 0) finalTotal = 0;
}

fetch("http://localhost/Shirtastic-Webshop/Backend/api/cart.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
  name,
  address,
  total: finalTotal,
  voucherCode,
  discount: validVoucher ? voucherDiscount : 0, // ✅ 加这个
  items: cartItems,
  userId
})



  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "ok") {
        msg.textContent = "Order submitted successfully!";
        form.reset();
        localStorage.removeItem(getCartKey());
        document.getElementById("cartItems").innerHTML = "";
        document.getElementById("cartTotal").textContent = "Total: €0.00";
        document.getElementById("discountedTotal").textContent = "";
      } else {
        msg.textContent = "Error: " + data.message;
      }
    })
    .catch((err) => {
      msg.textContent = "Failed to submit order.";
      console.error(err);
    });
  });

  }
});





