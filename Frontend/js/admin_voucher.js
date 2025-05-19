document.addEventListener("DOMContentLoaded", () => {
  const role = getCookie("role");
  if (role !== "admin") {
    alert("Access denied. Admins only.");
    window.location.href = "index.html";
    return;
  }

  const tableBody = document.getElementById("voucherTableBody");
  const form = document.getElementById("voucherForm");
  const msg = document.getElementById("voucherMsg");

  // 加载所有优惠券
 function loadVouchers() {
  fetch("http://localhost/Shirtastic-Webshop/Backend/api/voucher_list.php")
    .then((res) => res.json())
    .then((data) => {
      tableBody.innerHTML = ""; // 只清空一次 ✅
      
      (data.vouchers || []).forEach((v) => {
        const tr = document.createElement("tr");
        const statusColor =
          v.status === "Used"
            ? "red"
            : v.status === "Expired"
            ? "gray"
            : "green";

        tr.innerHTML = `
          <td>${v.code}</td>
          <td>${v.amount}</td>
          <td>${v.valid_until}</td>
          <td><span style="color: ${statusColor}">${v.status}</span></td>
        `;
        tableBody.appendChild(tr); // 添加行 ✅
      });
    });
}


  loadVouchers(); // 初始加载

  // 创建新优惠券
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const code = document.getElementById("code").value.trim();
    const amount = document.getElementById("amount").value;
    const validUntil = document.getElementById("validUntil").value;

    fetch("http://localhost/Shirtastic-Webshop/Backend/api/voucher_create.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ code, amount, valid_until: validUntil })
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "ok") {
          msg.textContent = "Voucher created!";
          msg.style.color = "green";
          form.reset();
          loadVouchers(); // 刷新列表
        } else {
          msg.textContent = data.message;
          msg.style.color = "red";
        }
      });
  });
});
