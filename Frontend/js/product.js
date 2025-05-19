document.addEventListener("DOMContentLoaded", () => {
  const productList = document.getElementById("product-list");
  const searchInput = document.getElementById("searchInput");
  const categoryContainer = document.getElementById("categoryContainer");
  const cartZone = document.getElementById("cartLink");


  function getCartKey() {
    const uid = localStorage.getItem("userId");
    return uid ? `cart_user_${uid}` : "cart_guest";
  }

  function renderProducts(products) {
    productList.innerHTML = "";

    products.forEach(p => {
      const div = document.createElement("div");
      div.className = "product";
      div.draggable = true;
      div.dataset.name = p.name;
      div.dataset.price = p.price;

      div.innerHTML = `
        <img src="../img/${p.image_path}" alt="${p.name}" style="width: 100px;">
        <h3 class="product-name">${p.name}</h3>
        <p class="product-description" style="display:none;">${p.description}</p>
        <p>€${p.price}</p>
        <p>Rating: ${"★".repeat(p.rating)}${"☆".repeat(5 - p.rating)}</p>
        <button class="add-to-cart" data-name="${p.name}" data-price="${p.price}">Add to Cart</button>
      `;
      productList.appendChild(div);
    });

    // 点击添加购物车
    document.querySelectorAll(".add-to-cart").forEach(btn => {
      btn.addEventListener("click", e => {
        e.stopPropagation();
        const name = btn.getAttribute("data-name");
        const price = btn.getAttribute("data-price");
        const key = getCartKey();
        let cart = JSON.parse(localStorage.getItem(key) || "[]");
        cart.push({ name, price });
        localStorage.setItem(key, JSON.stringify(cart));
        alert(`${name} has been added to the cart!`);
      });
    });

    // 查看详情（点击整个商品）
    document.querySelectorAll(".product").forEach(product => {
      product.addEventListener("click", () => {
        const name = product.querySelector(".product-name")?.textContent || "";
        const image = product.querySelector("img")?.src || "";
        const description = product.querySelector(".product-description")?.textContent || "No description.";

        document.getElementById("modalImage").src = image;
        document.getElementById("modalName").textContent = name;
        document.getElementById("modalDescription").textContent = description;

        document.getElementById("productModal").style.display = "flex";
      });

      // ✅ 拖动行为
      product.addEventListener("dragstart", (e) => {
        e.dataTransfer.setData("name", product.dataset.name);
        e.dataTransfer.setData("price", product.dataset.price);
        e.dataTransfer.effectAllowed = "copy";
      });
    });
  }

  // 加载产品（可选 category_id）
  function loadProductsByCategory(categoryId = null) {
    const url = categoryId ? `../../Backend/api/products.php?category_id=${categoryId}` : `../../Backend/api/products.php`;
    fetch(url)
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          renderProducts(data.products);
        }
      });
  }

  // 渲染分类按钮
  function loadCategories() {
    fetch("../../Backend/api/product_categories.php")
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          categoryContainer.innerHTML = "";

          const allBtn = document.createElement("button");
          allBtn.textContent = "All";
          allBtn.className = "category-button active";
          allBtn.addEventListener("click", () => {
            setActive(allBtn);
            loadProductsByCategory(null);
          });
          categoryContainer.appendChild(allBtn);

          data.categories.forEach(cat => {
            const btn = document.createElement("button");
            btn.textContent = cat.name;
            btn.className = "category-button";
            btn.dataset.id = cat.id;

            btn.addEventListener("click", () => {
              setActive(btn);
              loadProductsByCategory(cat.id);
            });

            categoryContainer.appendChild(btn);
          });

          loadProductsByCategory(null);
        }
      });
  }

  function setActive(button) {
    document.querySelectorAll(".category-button").forEach(btn => btn.classList.remove("active"));
    button.classList.add("active");
  }

  // 搜索过滤
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      const query = searchInput.value.toLowerCase();
      const allProducts = document.querySelectorAll(".product");

      allProducts.forEach(p => {
        const name = p.querySelector(".product-name").textContent.toLowerCase();
        p.style.display = name.includes(query) ? "block" : "none";
      });
    });
  }

  // 模态框关闭
  const modal = document.getElementById("productModal");
  const closeModal = document.getElementById("closeModal");
  if (modal && closeModal) {
    closeModal.addEventListener("click", () => modal.style.display = "none");
    modal.addEventListener("click", e => { if (e.target === modal) modal.style.display = "none"; });
  }

  // ✅ 拖放目标行为
  if (cartZone) {
    cartZone.addEventListener("dragover", (e) => {
      e.preventDefault();
      cartZone.classList.add("drag-over");
    });

    cartZone.addEventListener("dragleave", () => {
      cartZone.classList.remove("drag-over");
    });

    cartZone.addEventListener("drop", (e) => {
      e.preventDefault();
      cartZone.classList.remove("drag-over");

      const name = e.dataTransfer.getData("name");
      const price = e.dataTransfer.getData("price");

      if (name && price) {
        const key = getCartKey();
        let cart = JSON.parse(localStorage.getItem(key) || "[]");
        cart.push({ name, price });
        localStorage.setItem(key, JSON.stringify(cart));
        alert(`${name} has been added to the cart by drag & drop!`);
      }
    });
  }

  loadCategories();
});




