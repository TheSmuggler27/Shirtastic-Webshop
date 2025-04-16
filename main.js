// Registrierung speichern
document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");
    const loginForm = document.getElementById("loginForm");
  
    if (registerForm) {
      registerForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const username = document.getElementById("username").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        localStorage.setItem("user", JSON.stringify({ username, email, password }));
        document.getElementById("registerMsg").textContent = "Registrierung erfolgreich!";
      });
    }
  
    if (loginForm) {
      loginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const email = document.getElementById("loginEmail").value;
        const password = document.getElementById("loginPassword").value;
        const user = JSON.parse(localStorage.getItem("user"));
        if (user && user.email === email && user.password === password) {
          document.getElementById("loginMsg").textContent = "Login erfolgreich!";
        } else {
          document.getElementById("loginMsg").textContent = "Falsche Daten!";
        }
      });
    }
  
    const productList = document.getElementById("product-list");
    if (productList) {
      const products = [
        { name: "Black Oversize Tee", price: "€29.99", img: "img/shirt1.jpg" },
        { name: "White Graphic Tee", price: "€34.99", img: "img/shirt2.jpg" }
      ];
      productList.innerHTML = products.map(p => `
        <div class="product">
          <img src="${p.img}" alt="${p.name}" width="200"><br>
          <p><strong>${p.name}</strong></p>
          <p>${p.price}</p>
          <button>Add to Cart</button>
        </div>
      `).join('');
    }
  });
  