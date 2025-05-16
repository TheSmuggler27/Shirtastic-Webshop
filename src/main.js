document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");
  const loginForm = document.getElementById("loginForm");
  const productList = document.getElementById("product-list");
  const searchInput = document.getElementById("searchInput");

  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const username = document.getElementById("username").value;
      const email = document.getElementById("email").value;
      const password = document.getElementById("password").value;
  
      fetch("http://localhost/Shirtastic-Webshop/api/register.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ username, email, password })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          alert("Registrierung erfolgreich!");
          window.location.href = "login.html";
        } else {
          alert("Fehler: " + data.message);
        }
      })
      .catch(error => {
        console.error("Fehler beim Senden:", error);
        alert("Ein Fehler ist aufgetreten.");
      });
    });
  }
  
  
  

  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
  
      const email = document.getElementById("loginEmail").value;
      const password = document.getElementById("loginPassword").value;
  
      fetch("http://localhost/Shirtastic-Webshop/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          document.getElementById("loginMsg").textContent = "Login erfolgreich!";
          // Optional: Speichere Nutzername
          sessionStorage.setItem("username", data.username);
          setTimeout(() => {
            window.location.href = "index.html";
          }, 1000);
        } else {
          document.getElementById("loginMsg").textContent = "Fehler: " + data.message;
        }
      })
      .catch(err => {
        document.getElementById("loginMsg").textContent = "Serverfehler: " + err;
      });
    });
  }
  

  
  if (productList) {
    let allProducts = [];
  
    fetch("http://localhost/Shirtastic-Webshop/api/products.php")
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          allProducts = data.products;
          renderProducts(allProducts);
        }
      });
  
    function renderProducts(products) {
      productList.innerHTML = "";
      products.forEach(p => {
        const div = document.createElement("div");
        div.className = "product";
        div.innerHTML = `
          <p><strong>${p.name}</strong></p>
          <p>€${p.price}</p>
          <button data-name="${p.name}" data-price="${p.price}">Add to Cart</button>
        `;
        productList.appendChild(div);
      });
    }
  
    if (searchInput) {
      searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        const filtered = allProducts.filter(p => p.name.toLowerCase().includes(query));
        renderProducts(filtered);
      });
    }
  
    productList.addEventListener("click", (e) => {
      if (e.target.tagName === "BUTTON") {
        const name = e.target.getAttribute("data-name");
        const price = e.target.getAttribute("data-price");
        const cart = JSON.parse(localStorage.getItem("cart") || "[]");
        cart.push({ name, price });
        localStorage.setItem("cart", JSON.stringify(cart));
        alert(`${name} wurde zum Warenkorb hinzugefügt.`);
      }    
    });
  }
});
