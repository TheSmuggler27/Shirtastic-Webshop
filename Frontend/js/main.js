function getCookie(name) {
  const value = "; " + document.cookie;
  const parts = value.split("; " + name + "=");
  if (parts.length === 2) return parts.pop().split(";").shift();
}

function getCartKey() {
  const uid = localStorage.getItem("userId");
  return uid ? `cart_user_${uid}` : "cart_guest";
}

document.addEventListener("DOMContentLoaded", () => {
  const username = getCookie("username");
  const role = getCookie("role");

  const loginLink = document.getElementById("loginLink");
  const registerLink = document.getElementById("registerLink");
  const centerNav = document.querySelector(".navbar-center");

  if (username) {
    // 登录后显示“我的账号”
    if (loginLink) {
      loginLink.textContent = "My Account";
      loginLink.href = "myaccount.html";
    }

    // 登录后把“Join Us”变成 Logout
    if (registerLink) {
      registerLink.textContent = "Logout";
      registerLink.href = "#";
      registerLink.addEventListener("click", () => {
        document.cookie = "username=; max-age=0; path=/";
        document.cookie = "role=; max-age=0; path=/";
        sessionStorage.clear();
        localStorage.removeItem("userId");
        window.location.href = "login.html";
      });
    }

    // 根据角色动态添加链接到中部导航
    if (role === "admin" && centerNav) {
      const adminLink = document.createElement("a");
      adminLink.href = "admin.html";
      adminLink.textContent = "Products";
      centerNav.appendChild(adminLink);
    }

    if (role === "user" && centerNav) {
      const orderLink = document.createElement("a");
      orderLink.href = "/shirtastic-webshop/Frontend/sites/myorders.html";
      orderLink.textContent = "My Orders";
      centerNav.appendChild(orderLink);
    }
  }

  // 显示购物车数量
  const cartLink = document.getElementById("cartLink");
  if (cartLink) {
    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem(getCartKey()) || "[]");
      cartLink.textContent = `Cart (${cart.length})`;
    }

    updateCartCount();
    setInterval(updateCartCount, 1000);
  }
});



