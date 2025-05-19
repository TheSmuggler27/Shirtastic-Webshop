document.addEventListener("DOMContentLoaded", () => {
  const role = getCookie("role");
  if (role !== "admin") {
    alert("Admins only.");
    window.location.href = "index.html";
    return;
  }

  const tableBody = document.getElementById("userTableBody");

  function loadUsers() {
    fetch("http://localhost/Shirtastic-Webshop/Backend/api/admin_users.php")
      .then((res) => res.json())
      .then((data) => {
        tableBody.innerHTML = "";
        data.forEach((u) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${u.id}</td>
            <td>${u.username}</td>
            <td>${u.email}</td>
            <td>${u.role}</td>
            <td>${u.active == 1 ? "Yes" : "No"}</td>
            <td>
              <button data-id="${u.id}" data-active="${u.active}">
                ${u.active == 1 ? "Disable" : "Enable"}
              </button>
            </td>
          `;
          tableBody.appendChild(tr);
        });

        document.querySelectorAll("button[data-id]").forEach((btn) => {
          btn.addEventListener("click", () => {
            const id = btn.getAttribute("data-id");
            const current = btn.getAttribute("data-active");
            const newStatus = current == 1 ? 0 : 1;

            fetch("http://localhost/Shirtastic-Webshop/Backend/api/admin_users_active.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ id, active: newStatus })
            })
              .then((res) => res.json())
              .then((r) => {
                if (r.status === "ok") {
                  loadUsers();
                } else {
                  alert("Failed to update.");
                }
              });
          });
        });
      });
  }

  loadUsers();
});
