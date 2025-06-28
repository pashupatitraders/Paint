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
  <title>Sales - MyInventory</title>
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
    .search-add {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      max-width: 600px;
    }
    .search-add input {
      flex: 1;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .search-add button {
      background: #4fc3f7;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 8px 18px;
      margin-left: 10px;
      cursor: pointer;
      font-weight: 600;
    }
    .search-add button:hover {
      background: #3bb1e0;
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
    .edit-btn, .delete-btn, .view-btn {
      padding: 4px 8px;
      font-size: 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
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
    .view-btn {
      background: #6c757d;
      color: #fff;
      cursor: pointer;
    }
    .sale-actions {
      display: flex;
      width: 100%;
      gap: 10px;
      margin-bottom: 24px;
    }
    .sale-actions input[type="text"] {
      flex: 1;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .sale-actions .button {
      background: #4fc3f7;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 8px 12px;
      height: 36px;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      min-width: 90px;
    }
    .sale-actions .button:hover {
      background: #3bb0e0;
    }
    .select-customer {
      min-width: 160px;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .select-status {
      min-width: 130px;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .not-paid-blink {
      box-shadow: 0 0 0 4px #f44336, 0 0 16px 4px #f44336;
      animation: blink-red-sale 1s linear infinite;
      border: 2px solid #f44336 !important;
    }
    @keyframes blink-red-sale {
      0%, 100% { background: #fff; border-color: #f44336; }
      50% { background: #ffeaea; border-color: #fff; }
    }
    .partial-paid-blink {
      box-shadow: 0 0 0 4px #4caf50, 0 8px 16px 0 #4caf50;
      animation: blink-green-sale 1s linear infinite;
      border: 2px solid #4caf50 !important;
    }
    @keyframes blink-green-sale {
      0%, 100% { background: #fff; border-color: #4caf50; }
      50% { background: #eaffea; border-color: #fff; }
    }
  </style>
</head>
<body>

  <!-- Sidebar Menu -->
  <aside class="sidebar">
    <div class="logo">MyInventory</div>
    <nav>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="product.php"><i class="fas fa-box-open"></i> Products</a>
      <a href="sale.php" class="active"><i class="fas fa-shopping-cart"></i> Sales</a>
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

  <!-- Main Sales Content -->
  <main>
    <h1>Sales Records</h1>

    <div class="sale-actions">
      <input type="text" id="searchSales" placeholder="Search sales by customer..." oninput="searchSales()" />
      <select id="statusFilter" class="select-status">
        <option value="">All Status</option>
        <option value="Paid">Paid</option>
        <option value="Not Paid">Not Paid</option>
        <option value="Partial Paid">Partial Paid</option>
      </select>
      <button class="button" onclick="addSale()">+ Add Sale</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>S.N</th>
          <th>Date</th>
          <th>Customer Name</th>
          <th>Total (Rs.)</th>
          <th>Payment Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="salesTable">
        <!-- Sales rows will be added here -->
      </tbody>
    </table>
  </main>

  <script>
    function addSale() {
      localStorage.removeItem('editSaleIdx');
      window.location.href = "sales.php";
    }

    function createSaleRowHTML(date, customer, total, partialPaid, status, idx) {
      return `
        <td></td>
        <td>${date}</td>
        <td>${customer}</td>
        <td>Rs. ${total}</td>
        <td>${status}</td>
        <td>
          <button class="edit-btn" onclick="editSale(this)">Edit</button>
          <button class="delete-btn" onclick="deleteSale(this)">Delete</button>
          <button class="view-btn" onclick="viewSale(${idx})">View</button>
        </td>
      `;
    }

    // Load sales from localStorage and render
    function loadSalesFromStorage() {
      const sales = JSON.parse(localStorage.getItem('salesData') || '[]');
      const table = document.getElementById('salesTable');
      table.innerHTML = '';
      // Check if only a specific sale should be shown
      const urlParams = new URLSearchParams(window.location.search);
      const showOnlySaleIdx = localStorage.getItem('showOnlySaleIdx');
      let filteredSales = sales;
      if (urlParams.has('sale') && showOnlySaleIdx !== null) {
        const idx = parseInt(showOnlySaleIdx, 10);
        if (!isNaN(idx) && sales[idx]) {
          filteredSales = [sales[idx]];
        }
        // Optionally, remove the flag after use
        localStorage.removeItem('showOnlySaleIdx');
      }
      // Show most recent first unless filtered
      (filteredSales.length === 1 ? filteredSales : filteredSales.slice().reverse()).forEach((sale, i) => {
        // Compute the original index in the sales array
        const idx = sales.length - 1 - i;
        const tr = document.createElement('tr');
        tr.setAttribute('data-sale-idx', idx);
        tr.innerHTML = createSaleRowHTML(
          sale.date || '',
          sale.customer || '',
          sale.total || '',
          sale.partialPaidAmount || '',
          sale.paidStatus || '',
          idx
        );
        // Add red shadow and blink if not paid
        if ((sale.paidStatus || '').trim().toLowerCase() === 'not paid') {
          tr.classList.add('not-paid-blink');
        }
        // Add green shadow and blink if partial paid
        if ((sale.paidStatus || '').trim().toLowerCase() === 'partial paid') {
          tr.classList.add('partial-paid-blink');
        }
        table.appendChild(tr);
      });
      updateRowNumbers();
    }

    function editSale(btn) {
      // When the Edit button is clicked in the sales table:
      // 1. Get the sale index from the row's data-sale-idx attribute.
      // 2. Store this index in localStorage as 'editSaleIdx'.
      // 3. Redirect the user to 'sales.php' (the invoice entry page).
      const row = btn.closest('tr');
      const index = row.getAttribute('data-sale-idx');
      localStorage.setItem('editSaleIdx', index);
      window.location.href = "sales.php";
    }

    function updateRowNumbers() {
      const rows = document.querySelectorAll('#salesTable tr');
      rows.forEach((row, i) => {
        row.cells[0].innerText = i + 1;
      });
    }

    function deleteSale(btn) {
      const row = btn.closest('tr');
      const table = document.getElementById('salesTable');
      const rows = Array.from(table.children);
      const reversedIndex = rows.indexOf(row);
      let sales = JSON.parse(localStorage.getItem('salesData') || '[]');
      const idx = sales.length - 1 - reversedIndex;

      // Restore stock for deleted sale
      const sale = sales[idx];
      if (sale && sale.items) {
        let products = JSON.parse(localStorage.getItem('products') || '[]');
        sale.items.forEach(item => {
          const prodIdx = products.findIndex(
            p => (p.name || p.product || '').trim().toLowerCase() === (item.product || '').trim().toLowerCase()
          );
          if (prodIdx !== -1) {
            if (products[prodIdx].quantity !== undefined) {
              products[prodIdx].quantity = (parseInt(products[prodIdx].quantity) || 0) + (parseInt(item.qty) || 0);
            } else if (products[prodIdx].stock !== undefined) {
              products[prodIdx].stock = (parseInt(products[prodIdx].stock) || 0) + (parseInt(item.qty) || 0);
            }
          }
        });
        localStorage.setItem('products', JSON.stringify(products));
      }

      if (confirm("Are you sure you want to delete this sale?")) {
        sales.splice(idx, 1);
        localStorage.setItem('salesData', JSON.stringify(sales));
        row.remove();
        updateRowNumbers();
        // Redirect to dashboard and force reload (bypass cache)
        window.location.href = 'dashboard.php?reload=' + Date.now();
      }
    }

    function viewSale(idx) {
      window.location.href = `bill.php?sale=${idx}`;
    }

    // Populate customer select from sales data
    function populateCustomerFilter() {
      const select = document.getElementById('customerFilter');
      // Remove all except first option
      while (select.options.length > 1) select.remove(1);
      const sales = JSON.parse(localStorage.getItem('salesData') || '[]');
      const customers = [...new Set(sales.map(s => s.customer).filter(Boolean))];
      customers.forEach(cust => {
        const opt = document.createElement('option');
        opt.value = cust;
        opt.text = cust;
        select.appendChild(opt);
      });
    }

    // Filter sales by search and paid status
    function searchSales() {
      const filter = document.getElementById('searchSales').value.toLowerCase();
      const selectedStatus = document.getElementById('statusFilter').value;
      const rows = document.querySelectorAll('#salesTable tr');
      rows.forEach(row => {
        const customer = row.cells[2].innerText.toLowerCase();
        const paidStatus = row.cells[4].innerText.trim();
        const matchName = customer.includes(filter);
        const matchStatus = !selectedStatus || paidStatus === selectedStatus;
        row.style.display = (matchName && matchStatus) ? '' : 'none';
      });
      updateRowNumbers();
    }

    // Load sales from localStorage and render
    function loadSalesFromStorage() {
      const sales = JSON.parse(localStorage.getItem('salesData') || '[]');
      const table = document.getElementById('salesTable');
      table.innerHTML = '';
      // Check if only a specific sale should be shown
      const urlParams = new URLSearchParams(window.location.search);
      const showOnlySaleIdx = localStorage.getItem('showOnlySaleIdx');
      let filteredSales = sales;
      if (urlParams.has('sale') && showOnlySaleIdx !== null) {
        const idx = parseInt(showOnlySaleIdx, 10);
        if (!isNaN(idx) && sales[idx]) {
          filteredSales = [sales[idx]];
        }
        // Optionally, remove the flag after use
        localStorage.removeItem('showOnlySaleIdx');
      }
      // Show most recent first unless filtered
      (filteredSales.length === 1 ? filteredSales : filteredSales.slice().reverse()).forEach((sale, i) => {
        // Compute the original index in the sales array
        const idx = sales.length - 1 - i;
        const tr = document.createElement('tr');
        tr.setAttribute('data-sale-idx', idx);
        tr.innerHTML = createSaleRowHTML(
          sale.date || '',
          sale.customer || '',
          sale.total || '',
          sale.partialPaidAmount || '',
          sale.paidStatus || '',
          idx
        );
        // Add red shadow and blink if not paid
        if ((sale.paidStatus || '').trim().toLowerCase() === 'not paid') {
          tr.classList.add('not-paid-blink');
        }
        // Add green shadow and blink if partial paid
        if ((sale.paidStatus || '').trim().toLowerCase() === 'partial paid') {
          tr.classList.add('partial-paid-blink');
        }
        table.appendChild(tr);
      });
      updateRowNumbers();
    }

    // Deduct stock when a sale is saved (to be called from sales.php after saving a sale)
    function deductStockForSale(sale) {
      if (!sale.items) return;
      let products = JSON.parse(localStorage.getItem('products') || '[]');
      sale.items.forEach(item => {
        const idx = products.findIndex(
          p => ((p.name || p.product || '').trim().toLowerCase() === (item.product || '').trim().toLowerCase())
        );
        if (idx !== -1) {
          if (products[idx].quantity !== undefined) {
            products[idx].quantity = Math.max(0, (parseInt(products[idx].quantity) || 0) - (parseInt(item.qty) || 0));
          } else if (products[idx].stock !== undefined) {
            products[idx].stock = Math.max(0, (parseInt(products[idx].stock) || 0) - (parseInt(item.qty) || 0));
          }
        }
      });
      localStorage.setItem('products', JSON.stringify(products));
    }

    window.addEventListener('DOMContentLoaded', function() {
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
      loadSalesFromStorage();
      document.getElementById('statusFilter').addEventListener('change', searchSales);
      var vatBtn = document.getElementById('vatMenuBtn');
      if (vatBtn) {
        vatBtn.addEventListener('click', function(e) {
          e.preventDefault();
          window.location.href = 'vat.php';
        });
      }
    });

    // Password popup for Setting (global, reusable)
    function showPasswordPopup() {
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

    document.addEventListener('DOMContentLoaded', function() {
      // ...existing code...
      var settingBtn = document.getElementById('settingMenuBtn');
      if (settingBtn) {
        settingBtn.addEventListener('click', function(e) {
          e.preventDefault();
          showPasswordPopup();
        });
      }
      // ...existing code...
    });

    // When selecting a product for details, stock, or any operation, always match by name, size, and category:
    // Example usage in your logic (replace old find/findIndex):
    // let found = products.find(p => p.name === selectedName && (p.size || '') === (selectedSize || '') && (p.category || '') === (selectedCategory || ''));
    // This ensures correct handling for products with same name but different size/category.
  </script>
</body>
</html>
</html>
