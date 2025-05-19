document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    
if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();

      
        const remember = document.getElementById("rememberMe")?.checked;
        const identifier = document.getElementById("loginEmail").value;
        const password = document.getElementById("loginPassword").value;

        fetch("http://localhost/Shirtastic-Webshop/Backend/api/login.php", {
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
