<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Products - MyInventory</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
      color: #222;
    }

    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 250px;
      height: 100vh;
      background: #1e2a38;
      color: #aab8c2;
      display: flex;
      flex-direction: column;
      padding-top: 30px;
      z-index: 1000;
    }
    .sidebar .logo {
      font-size: 24px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 40px;
      text-align: center;
    }
    .sidebar nav a {
      color: #aab8c2;
      text-decoration: none;
      display: flex;
      align-items: center;
      padding: 14px 30px;
      font-weight: 600;
      transition: 0.3s;
      border-left: 4px solid transparent;
    }
    .sidebar nav a i {
      margin-right: 14px;
      font-size: 18px;
    }
    .sidebar nav a:hover,
    .sidebar nav a.active {
      background: #283b4a;
      color: #4fc3f7;
      border-left-color: #4fc3f7;
    }

    header {
      margin-left: 250px;
      height: 60px;
      background: #fff;
      border-bottom: 1px solid #e1e8ed;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding: 0 30px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    header .user-profile {
      display: flex;
      align-items: center;
      cursor: pointer;
    }
    header .user-profile img {
      width: 36px; height: 36px;
      border-radius: 50%;
      margin-right: 12px;
    }

    main {
      margin-left: 250px;
      padding: 30px 40px;
    }
    main h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #1a202c;
    }

    .top-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
      max-width: 100%;
    }
    .top-actions input[type="text"] {
      flex: 1;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .top-actions .button {
      background: #4fc3f7;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 8px 12px;
      height: 36px;
      min-width: 0;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      user-select: none;
      font-size: 14px;
      box-sizing: border-box;
      cursor: pointer;
    }
    .top-actions .button:hover {
      background: #3bb0e0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 6px 12px rgba(0,0,0,0.08);
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background: #4fc3f7;
      color: #fff;
      font-size: 14px;
    }
    tr:hover { background: #f1f1f1; }

    .edit-btn, .delete-btn {
      padding: 4px 8px;
      font-size: 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 5px;
    }
    .edit-btn {
      background: #1e90ff;
      color: #fff;
    }
    .edit-btn:hover { background: #1c86ee; }
    .delete-btn {
      background: #ff4d4d;
      color: #fff;
    }
    .delete-btn:hover { background: #e04343; }

    .select-category {
      min-width: 160px;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      margin-right: 0;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">MyInventory</div>
    <nav>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="product.php" class="active"><i class="fas fa-box-open"></i> Products</a>
      <a href="sale.php"><i class="fas fa-shopping-cart"></i> Sales</a>
      <a href="ledgerm.php"><i class="fas fa-file-invoice-dollar"></i> Ledger</a>
      <a href="vat.php" id="vatMenuBtn"><i class="fas fa-receipt"></i> Vat</a>
      <a href="setting.php" id="settingMenuBtn"><i class="fas fa-cog"></i> Setting</a>
    </nav>
  </aside>

  <!-- Header -->
  <header>
    <div class="user-profile">
      <span></span>
    </div>
  </header>

  <!-- Main Content -->
  <main>
    <h1>Product Inventory</h1>

    <div class="top-actions">
      <!-- Product Name Search -->
      <input type="text" id="searchProduct" placeholder="Search product..." />
      <!-- Category Select (searchable) -->
      <select id="categoryFilter" class="select-category">
        <option value="">All Categories</option>
        <!-- Categories will be populated by JS -->
      </select>
      <button class="button" onclick="window.location.href='category.php'">Add Category</button>
      <button class="button" onclick="addStockPopup()">Add Stock</button>
      <button class="button" onclick="window.location.href='productb.php'">Add Product</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Size</th>
          <th>Category</th>
          <th>Stock</th>
          <th>Selling Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="productTable">
        <!-- Products will be rendered by JS -->
      </tbody>
    </table>
  </main>

  <script>
    function renderProducts() {
      const table = document.getElementById('productTable');
      table.innerHTML = '';
      let products = JSON.parse(localStorage.getItem('products') || '[]');
      // Show most recent first
      products = products.slice().reverse();
      products.forEach((p, idx) => {
        const tr = document.createElement('tr');
        // Use a unique key for each product row: name + category + size
        tr.setAttribute('data-key', `${p.name}||${p.category}||${p.size}`);
        tr.className = p.status === 'not paid' ? 'not-paid-row' : '';
        tr.innerHTML = `
          <td>${p.name}</td>
          <td>${p.size}</td>
          <td>${p.category}</td>
          <td>${p.quantity !== undefined ? p.quantity : (p.stock !== undefined ? p.stock : 0)}</td>
          <td>Rs. ${p.sell}</td>
          <td>
            <button class="edit-btn" onclick="editProduct(${products.length - 1 - idx})">Edit</button>
            <button class="delete-btn" onclick="deleteProduct(${products.length - 1 - idx})">Delete</button>
          </td>
        `;
        table.appendChild(tr);
      });
    }

    // Populate category select from localStorage
    function populateCategoryFilter() {
      const select = document.getElementById('categoryFilter');
      // Remove all except first option
      while (select.options.length > 1) select.remove(1);
      const categories = JSON.parse(localStorage.getItem('categories') || '[]');
      categories.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat;
        opt.text = cat;
        select.appendChild(opt);
      });
    }

    // Filter products by name and category
    function searchProduct() {
      const filter = document.getElementById('searchProduct').value.toLowerCase();
      const selectedCategory = document.getElementById('categoryFilter').value;
      const rows = document.querySelectorAll('#productTable tr');
      rows.forEach(row => {
        const name = row.cells[0].innerText.toLowerCase();
        const category = row.cells[2].innerText;
        const matchName = name.includes(filter);
        const matchCategory = !selectedCategory || category === selectedCategory;
        row.style.display = (matchName && matchCategory) ? '' : 'none';
      });
    }

    function editProduct(idx) {
      localStorage.setItem('editProductIdx', idx);
      window.location.href = 'productb.php';
    }

    function deleteProduct(idx) {
      if (confirm("Are you sure you want to delete this product?")) {
        let products = JSON.parse(localStorage.getItem('products') || '[]');
        products.splice(idx, 1);
        localStorage.setItem('products', JSON.stringify(products));
        renderProducts();
      }
    }

    function addStockPopup() {
      window.location.href = 'stock.php';
    }

    // Password popup for Setting (global, reusable)
    function showPasswordPopup() {
      // Remove if already exists
      const oldPopup = document.getElementById('passwordPopup');
      if (oldPopup) oldPopup.remove();

      const popup = document.createElement('div');
      popup.id = 'passwordPopup';
      popup.style.position = 'fixed';
      popup.style.top = '0';
      popup.style.left = '0';
      popup.style.width = '100vw';
      popup.style.height = '100vh';
      popup.style.background = 'rgba(0,0,0,0.3)';
      popup.style.display = 'flex';
      popup.style.alignItems = 'center';
      popup.style.justifyContent = 'center';
      popup.style.zIndex = '9999';

      popup.innerHTML = `
        <div style="background:#fff;padding:32px 28px 24px 28px;border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,0.15);min-width:320px;max-width:90vw;position:relative;">
          <h2 style="margin-top:0;font-size:22px;color:#1e2a38;">Enter Password</h2>
          <input type="password" id="popupPassword" placeholder="Password" style="width:100%;padding:10px;margin-bottom:16px;border-radius:6px;border:1px solid #ccc;font-size:16px;" autofocus />
          <div id="popupError" style="color:#f44336;font-size:14px;display:none;margin-bottom:8px;"></div>
          <div style="display:flex;justify-content:flex-end;gap:10px;">
            <button id="popupCancelBtn" style="padding:8px 18px;border:none;border-radius:6px;background:#eee;color:#222;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="popupOkBtn" style="padding:8px 18px;border:none;border-radius:6px;background:#4fc3f7;color:#fff;font-weight:600;cursor:pointer;">OK</button>
          </div>
        </div>
      `;
      document.body.appendChild(popup);

      document.getElementById('popupCancelBtn').onclick = function() {
        popup.remove();
      };
      document.getElementById('popupOkBtn').onclick = checkPasswordAndRedirect;
      document.getElementById('popupPassword').onkeydown = function(e) {
        if (e.key === 'Enter') checkPasswordAndRedirect();
      };

      function checkPasswordAndRedirect() {
        const input = document.getElementById('popupPassword').value;
        const loginPass = localStorage.getItem('loginPassword') || '';
        const customerPass = localStorage.getItem('customerPassword') || '';
        const developerPass = localStorage.getItem('developerPassword') || '';
        if (input && (input === loginPass || input === customerPass || input === developerPass)) {
          popup.remove();
          window.location.href = 'setting.php';
        } else {
          document.getElementById('popupError').innerText = 'Incorrect password!';
          document.getElementById('popupError').style.display = 'block';
        }
      }
    }

    // Render products on page load
    document.addEventListener('DOMContentLoaded', function() {
      renderProducts();
      populateCategoryFilter();
      document.getElementById('searchProduct').addEventListener('input', searchProduct);
      document.getElementById('categoryFilter').addEventListener('change', searchProduct);

      const profilePhoto = localStorage.getItem('profilePhoto');
      if (profilePhoto) {
        const userProfile = document.querySelector('header .user-profile');
        const img = document.createElement('img');
        img.src = profilePhoto;
        img.alt = 'User';
        img.style.width = '36px';
        img.style.height = '36px';
        img.style.borderRadius = '50%';
        img.style.marginRight = '12px';
        userProfile.insertBefore(img, userProfile.firstChild);
      }
      // Set profile name from localStorage if available
      const profileName = localStorage.getItem('profileName');
      if (profileName) {
        document.querySelectorAll('header .user-profile span').forEach(span => {
          span.innerText = profileName;
        });
      }
      var vatBtn = document.getElementById('vatMenuBtn');
      if (vatBtn) {
        vatBtn.addEventListener('click', function(e) {
          e.preventDefault();
          window.location.href = 'vat.php';
        });
      }
      var settingBtn = document.getElementById('settingMenuBtn');
      if (settingBtn) {
        settingBtn.addEventListener('click', function(e) {
          e.preventDefault();
          showPasswordPopup();
        });
      }
    });
  </script>

</body>
</html>
