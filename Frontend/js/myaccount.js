document.addEventListener("DOMContentLoaded", () => {
  const userId = localStorage.getItem("userId");

  if (!userId) {
    alert("Please log in first.");
    window.location.href = "login.html";
    return;
  }

  // 加载用户信息
  fetch("http://localhost/Shirtastic-Webshop/Backend/api/myaccount.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ userId })
  })
    .then(res => res.json())
    .then(data => {
      const userInfo = document.getElementById("userInfo");
      if (data.status === "ok") {
        const u = data.user;
        userInfo.innerHTML = `
          <p><strong>Username:</strong> ${u.username}</p>
          <p><strong>Email:</strong> ${u.email}</p>
          <p><strong>Firstname:</strong> ${u.first_name}</p>
          <p><strong>Lastname:</strong> ${u.last_name}</p>
          <p><strong>Address:</strong> ${u.address || "-"}</p>
          <p><strong>Payment Info:</strong> ${u.payment_info || "-"}</p>
        `;

        // 自动填入当前信息到编辑表单
        document.getElementById("editFirstName").value = u.first_name || "";
        document.getElementById("editLastName").value = u.last_name || "";
        document.getElementById("editAddress").value = u.address || "";
        document.getElementById("editPayment").value = u.payment_info || "";

      } else {
        userInfo.innerHTML = `<p style="color:red;">Error: ${data.message}</p>`;
      }
    })
    .catch(err => {
      console.error(err);
      document.getElementById("userInfo").innerHTML = "Failed to load user info.";
    });

  // 绑定保存按钮逻辑
  const editForm = document.getElementById("editForm");
  editForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const firstName = document.getElementById("editFirstName").value;
    const lastName = document.getElementById("editLastName").value;
    const address = document.getElementById("editAddress").value;
    const payment = document.getElementById("editPayment").value;
    const password = document.getElementById("editPassword").value;

    fetch("http://localhost/Shirtastic-Webshop/Backend/api/update_profile.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        first_name: firstName,
        last_name: lastName,
        userId,
        address,
        payment_info: payment,
        current_password: password
      })
    })
      .then((res) => res.json())
      .then((data) => {
        const msgBox = document.getElementById("editMsg");
        if (data.status === "ok") {
          msgBox.textContent = "Changes saved successfully.";
          msgBox.style.color = "green";
        } else {
          msgBox.textContent = data.message;
          msgBox.style.color = "red";
        }
      });
  });
});
