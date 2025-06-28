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

    /* Header */
    header {
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

    /* Main Content */
    main {
      padding: 30px 40px;
    }
    main h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #1a202c;
    }

    /* Search & Add Button */
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

    /* Table */
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

    /* Buttons inside table */
    .edit-btn, .delete-btn {
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

    /* Modal Form */
    #saleForm {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }
    #saleForm form {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      min-width: 320px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    #saleForm input, #saleForm select {
      width: 100%;
      padding: 8px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }
    #saleForm .form-actions {
      text-align: right;
    }
    #saleForm .cancel-btn {
      background: #ccc;
      color: #000;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-right: 10px;
    }
    #saleForm .save-btn {
      background: #4fc3f7;
      color: #fff;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 600;
    }

    .control-buttons {
      margin-top: 20px;
      text-align: right;
    }
    .save-btn {
      background: #4caf50;
      color: #fff;
      border: none;
      border-radius: 4px;
      padding: 10px 20px;
      cursor: pointer;
      font-weight: 600;
    }
    .save-btn:hover {
      background: #43a047;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
      <img id="profilePhotoImg" src="https://i.pravatar.cc/40" alt="User" />
      <span id="profileNameSpan"></span>
      <a href="setting.php" title="Settings" style="margin-left:16px; color:#4fc3f7; font-size:20px; display:flex; align-items:center;">
        <i class="fas fa-cog"></i>
      </a>
    </div>
  </header>

  <!-- Main Sales Content -->
  <main>
    <h1>Stock Data</h1>

    <div class="search-add" style="display:flex;align-items:center;gap:10px;margin-bottom:20px;max-width:600px;">
      <input type="text" id="searchSales" placeholder="Search product..." oninput="searchSales()" style="flex:1;padding:8px 12px;border-radius:6px;border:1px solid #ccc;font-size:14px;">
    </div>
    <table>
      <thead>
        <tr>
          <th>S.N</th>
          <th>Product Name</th>
          <th>Size</th>
          <th>Category</th>
          <th>Stock</th>
          <th>Add</th>
          <th>Remove</th>
        </tr>
      </thead>
      <tbody id="salesTable">
        <!-- Sales rows will be added here -->
      </tbody>
    </table>
    <div class="control-buttons" style="margin-top: 20px; text-align: right;">
      <button class="save-btn" style="background:#4caf50;color:#fff;border:none;border-radius:6px;padding:8px 18px;font-weight:600;cursor:pointer;margin-right:10px;" onclick="saveStockChanges()">Save</button>
      <button class="cancel-btn" style="background:#f44336;color:#fff;border:none;border-radius:6px;padding:8px 18px;font-weight:600;cursor:pointer;" onclick="cancelStockChanges()">Cancel</button>
    </div>
  </main>
<script>
    let editingSale = null;

    function getSalesData() {
      return JSON.parse(localStorage.getItem('salesData') || '[]');
    }

    function setSalesData(data) {
      localStorage.setItem('salesData', JSON.stringify(data));
    }

    function renderSalesTable() {
      const products = JSON.parse(localStorage.getItem('products') || '[]');
      const tbody = document.getElementById('salesTable');
      tbody.innerHTML = '';
      products.forEach((p, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${i + 1}</td>
          <td>${p.name || ''}</td>
          <td>${p.size || ''}</td>
          <td>${p.category || ''}</td>
          <td class="stock-cell">${p.quantity !== undefined ? p.quantity : ''}</td>
          <td><input type="number" min="0" class="add-stock" style="width:60px;"></td>
          <td><input type="number" min="0" class="remove-stock" style="width:60px;"></td>
        `;
        tbody.appendChild(tr);
      });
    }

    function toggleSaleForm() {
      const form = document.getElementById('saleForm');
      form.style.display = form.style.display === 'flex' ? 'none' : 'flex';
      if (form.style.display === 'none') {
        clearForm();
      }
    }

    function clearForm() {
      document.getElementById('customerName').value = '';
      document.getElementById('productName').value = '';
      document.getElementById('saleDate').value = '';
      document.getElementById('quantity').value = '';
      document.getElementById('totalPrice').value = '';
      editingSale = null;
      document.getElementById('formTitle').innerText = 'Add Sale';
    }

    function submitSaleForm(e) {
      e.preventDefault();
      const customerName = document.getElementById('customerName').value.trim();
      const productName = document.getElementById('productName').value.trim();
      const saleDate = document.getElementById('saleDate').value;
      const quantity = document.getElementById('quantity').value;
      const totalPrice = document.getElementById('totalPrice').value;

      let sales = getSalesData();

      if (editingSale) {
        const rowIndex = Array.from(document.getElementById('salesTable').children).indexOf(editingSale);
        sales[rowIndex] = {
          customer: customerName,
          product: productName,
          date: saleDate,
          qty: quantity,
          price: totalPrice,
          address: '',
          total: totalPrice,
          paidStatus: 'Unpaid'
        };
        editingSale = null;
      } else {
        sales.push({
          customer: customerName,
          product: productName,
          date: saleDate,
          qty: quantity,
          price: totalPrice,
          address: '',
          total: totalPrice,
          paidStatus: 'Unpaid'
        });
      }

      setSalesData(sales);
      renderSalesTable();
      toggleSaleForm();
    }

    function createSaleRowHTML(sale, index) {
      return `
        <td>${index + 1}</td>
        <td>${sale.customer}</td>
        <td>${sale.address || '-'}</td>
        <td>Rs. ${sale.total}</td>
        <td>${sale.paidStatus}</td>
        <td>
          <button class="edit-btn" onclick="editSale(${index})">Edit</button>
          <button class="delete-btn" onclick="deleteSale(${index})">Delete</button>
        </td>
      `;
    }

    function updateRowNumbers() {
      const rows = document.querySelectorAll('#salesTable tr');
      rows.forEach((row, i) => {
        row.cells[0].innerText = i + 1;
      });
    }

    function editSale(index) {
      editingSale = document.getElementById('salesTable').children[index];
      document.getElementById('customerName').value = editingSale.cells[1].innerText;
      document.getElementById('productName').value = editingSale.cells[2].innerText;
      document.getElementById('saleDate').value = '';
      document.getElementById('quantity').value = '';
      document.getElementById('totalPrice').value = editingSale.cells[3].innerText.replace('Rs. ', '');
      document.getElementById('formTitle').innerText = 'Edit Sale';
      toggleSaleForm();
    }

    function deleteSale(index) {
      const row = document.getElementById('salesTable').children[index];
      const rowIndex = Array.from(document.getElementById('salesTable').children).indexOf(row);
      let sales = getSalesData();
      sales.splice(rowIndex, 1);
      setSalesData(sales);
      renderSalesTable();
    }

    function saveStockChanges() {
      let products = JSON.parse(localStorage.getItem('products') || '[]');
      const rows = document.querySelectorAll('#salesTable tr');
      rows.forEach((row, idx) => {
        const addInput = row.querySelector('.add-stock');
        const removeInput = row.querySelector('.remove-stock');
        const addVal = parseInt(addInput.value) || 0;
        const removeVal = parseInt(removeInput.value) || 0;
        let stock = products[idx].quantity !== undefined ? parseInt(products[idx].quantity) : 0;
        stock = stock + addVal - removeVal;
        if (stock < 0) stock = 0;
        products[idx].quantity = stock;
        // Update the cell visually
        row.querySelector('.stock-cell').innerText = stock;
        // Clear input fields
        addInput.value = '';
        removeInput.value = '';
      });
      localStorage.setItem('products', JSON.stringify(products));
      alert('Stock updated successfully!');
      window.location.href = 'product.php';
    }

    function cancelStockChanges() {
      window.location.href = 'product.php';
    }

    function searchSales() {
      const filter = document.getElementById('searchSales').value.toLowerCase();
      const rows = document.querySelectorAll('#salesTable tr');
      rows.forEach(row => {
        const name = row.cells[1].innerText.toLowerCase();
        row.style.display = name.includes(filter) ? '' : 'none';
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      renderSalesTable();
      // Filter to show only selected product if ?show=idx is present
      const params = new URLSearchParams(window.location.search);
      const showIdx = params.get('show');
      if (showIdx !== null) {
        const rows = document.querySelectorAll('#salesTable tr');
        rows.forEach((row, i) => {
          if (i !== parseInt(showIdx, 10)) {
            row.style.display = 'none';
          } else {
            row.style.display = '';
          }
        });
      }
    });

    // Update profile photo and name from localStorage
    document.addEventListener('DOMContentLoaded', function() {
      const profilePhoto = localStorage.getItem('profilePhoto');
      const img = document.getElementById('profilePhotoImg');
      if (profilePhoto && img) {
        img.src = profilePhoto;
      }
      const profileName = localStorage.getItem('profileName');
      const span = document.getElementById('profileNameSpan');
      if (profileName && span) {
        span.innerText = profileName;
      }
    });
  </script>
</body>
</html>
