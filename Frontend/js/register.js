document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");

if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const salutation = document.getElementById("salutation").value;
      const first_name = document.getElementById("first_name").value;
      const last_name = document.getElementById("last_name").value;
      const address = document.getElementById("address").value;
      const postal_code = document.getElementById("postal_code").value;
      const city = document.getElementById("city").value;
      const email = document.getElementById("email").value;
      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirmPassword").value;
      const payment_info = document.getElementById("payment_info").value;

      // ✅ 检查所有字段是否为空
        if (
        !salutation || !first_name || !last_name || !address ||
        !postal_code || !city || !email || !username || !password ||
        !confirmPassword || !payment_info
        ) {
        alert("Please fill in all fields.");
        return;
        }

      
      if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
      }

      fetch("http://localhost/Shirtastic-Webshop/Backend/api/register.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          salutation, first_name, last_name, address,
          postal_code, city, email, username, password, payment_info
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          alert("Registration successful!");
          window.location.href = "login.html";
        } else {
          alert("Error: " + data.message);
        }
      });
    });
  }
  })
