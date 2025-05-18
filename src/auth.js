document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");
  const loginForm = document.getElementById("loginForm");

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

      if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
      }

      fetch("http://localhost/Shirtastic-Webshop/api/register.php", {
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






  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();

      
        const remember = document.getElementById("rememberMe")?.checked;
        const identifier = document.getElementById("loginEmail").value;
        const password = document.getElementById("loginPassword").value;

        fetch("http://localhost/Shirtastic-Webshop/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ identifier, password })
        })


      .then(res => res.json())
      .then(data => {
      if (data.status === "ok") {
        sessionStorage.setItem("username", data.username);
        sessionStorage.setItem("role", data.role);
        localStorage.setItem("userId", data.userId); // ✅ 这行非常关键！


           // 不管是否 remember，都设置 cookie
          const cookieMaxAge = remember ? 604800 : 3600; // 7天 or 1小时
          document.cookie = `username=${data.username}; max-age=${cookieMaxAge}; path=/`;
          document.cookie = `role=${data.role}; max-age=${cookieMaxAge}; path=/`;

          document.getElementById("loginMsg").textContent = "Login successful!";

            setTimeout(() => {
        if (data.role === "admin") {
            window.location.href = "admin.html";
        } else {
            window.location.href = "index.html";
        }
        }, 1000);

        } else {
          document.getElementById("loginMsg").textContent = "Error: " + data.message;
          }
      });
    });
  }
});
