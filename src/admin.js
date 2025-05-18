document.addEventListener("DOMContentLoaded", () => {
  const role = getCookie("role");
  if (role !== "admin") {
    document.getElementById("adminOnly").style.display = "none";
    document.getElementById("accessDenied").style.display = "block";
    return;
  } else {
    document.getElementById("adminOnly").style.display = "block";
  }

  const form = document.getElementById("addProductForm");
  const productList = document.getElementById("productList");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append("name", document.getElementById("productName").value);
    formData.append("description", document.getElementById("productDescription").value);
    formData.append("price", document.getElementById("productPrice").value);
    formData.append("category_id", document.getElementById("productCategory").value);
    formData.append("rating", document.getElementById("productRating").value);
    formData.append("image", document.getElementById("productImage").files[0]);

    fetch("http://localhost/Shirtastic-Webshop/api/product_add.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "ok") {
        alert("Product added!");
        form.reset();
        loadProducts();
      } else {
        alert("Error: " + data.message);
      }
    });
  });

  function loadProducts() {
    fetch("http://localhost/Shirtastic-Webshop/api/products.php")
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          productList.innerHTML = "";
          data.products.forEach(product => {
            const div = document.createElement("div");
            div.classList.add("product");
            div.innerHTML = `
              <img src="/SHIRTASTIC-WEBSHOP/img/${product.image_path}" alt="${product.name}" style="width: 100px;"><br>
              <strong>${product.name}</strong><br>
              Description: <span class="desc">${product.description}</span><br>
              Price: <span class="price">€${product.price}</span><br>
              Category: <span class="cat">${product.category_name || "Uncategorized"}</span><br>
              Rating: <span class="rate">${product.rating}</span><br>
              <button class="delete-btn" data-id="${product.id}">Delete</button>
              <button class="edit-btn" data-id="${product.id}">Edit</button>
            `;
            productList.appendChild(div);
          });

          // ✅ 绑定删除按钮
          document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", () => {
              const id = button.dataset.id;
              if (confirm("Do you really want to delete this product?")) {
                fetch(`../api/product_delete.php?id=${id}`, { method: "DELETE" })
                  .then(res => res.json())
                  .then(data => {
                    if (data.status === "ok") {
                      alert("Product deleted.");
                      loadProducts();
                    } else {
                      alert("Error deleting product.");
                    }
                  });
              }
            });
          });

          // ✅ 绑定编辑按钮
          document.querySelectorAll(".edit-btn").forEach(button => {
              button.addEventListener("click", () => {
              const id = button.dataset.id;
              const productDiv = button.parentElement;

              const currentName = productDiv.querySelector("strong")?.innerText;
              const currentDesc = productDiv.querySelector(".desc")?.innerText;
              const currentPrice = productDiv.querySelector(".price")?.innerText.replace("€", "");
              const currentRating = productDiv.querySelector(".rate")?.innerText || "3";
              const currentCategory = productDiv.querySelector(".cat")?.innerText;

              const newName = prompt("New name:", currentName);
              const newDesc = prompt("New description:", currentDesc);
              const newPrice = prompt("New price:", currentPrice);
              const newRating = prompt("New rating (1-5):", currentRating);

              // 提示选择分类
              const newCategoryName = prompt("New category (1 = Asian Tradition, 2 = American Streetwear):", currentCategory);
              const category_id = newCategoryName === "Asian Tradition" ? 1 :
                        newCategoryName === "American Streetwear" ? 2 :
                        parseInt(newCategoryName);

              if (!newName || !newDesc || !newPrice || !newRating || !category_id) return;

              fetch("../api/product_update.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                  id,
                  name: newName,
                  description: newDesc,
                  price: newPrice,
                  rating: newRating,
                  category_id: category_id
                  })
              })
                .then(res => res.json())
                .then(data => {
                  if (data.status === "ok") {
                  alert("Product updated.");
                  loadProducts();
                  } else {
                  alert("Update failed.");
                  }
                });
              });

          });
        }
      });
  }

  loadProducts();
});

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
}
