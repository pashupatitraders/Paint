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
  <title>Product Entry - MyInventory</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
      color: #222;
    }
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
      width: 36px;
      height: 36px;
      border-radius: 50%;
      margin-right: 12px;
    }
    main {
      padding: 30px 40px;
    }
    main h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #1a202c;
    }

    .product-entry-form {
      display: grid;
      grid-template-columns: 1.5fr 1.5fr 1.5fr 0.5fr;
      gap: 16px;
      margin-bottom: 30px;
    }

    .product-entry-form > div:nth-child(n+5):nth-child(-n+8) {
      width: 97%;
      min-width: 0;
      max-width: 97%;
      margin-right: 0;
    }

    .product-entry-form div {
      display: flex;
      flex-direction: column;
    }

    label {
      font-weight: 600;
      margin-bottom: 6px;
      font-size: 14px;
      color: #333;
    }

    input,
    .select-box {
      padding: 10px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      width: 100%;
      min-height: 40px;
      background: #fff;
    }

    .select-box {
      display: flex;
      align-items: center;
      justify-content: space-between;
      cursor: pointer;
    }

    .control-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }

    .control-buttons button {
      padding: 10px 18px;
      font-weight: 600;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .save-btn { background: #4caf50; color: white; }
    .cancel-btn { background: #f44336; color: white; }

    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.2);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background: #fff;
      border-radius: 8px;
      padding: 20px;
      max-height: 60vh;
      max-width: 500px; /* Increased width for better UI */
      min-width: 350px;  /* Ensures a wider modal */
      width: 100%;
      overflow-y: auto;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
    }

    .modal-content ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .modal-content li {
      padding: 8px 12px;
      cursor: pointer;
    }

    .modal-content li:hover {
      background: #f1f1f1;
    }

    .modal-content button {
      margin-top: 12px;
      background: #f44336;
      color: white;
      border: none;
      padding: 6px 16px;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <header>
    <div class="user-profile" style="display: flex; align-items: center; gap: 10px;">
      <img id="profilePhotoImg" src="https://i.pravatar.cc/40" alt="User" />
      <span id="profileNameSpan"></span>
      <a href="setting.php" title="Settings" style="margin-left:16px; color:#4fc3f7; font-size:20px; display:flex; align-items:center;">
        <i class="fas fa-cog"></i>
      </a>
    </div>
  </header>

  <main>
    <h1>Product Entry</h1>
    <form class="product-entry-form" onsubmit="return false;">
      <!-- First row: Product Name, Category, Size, (empty for alignment) -->
      <div>
        <label>Product Name</label>
        <input id="productName" type="text" placeholder="Enter product name" />
      </div>
      <div>
        <label>Category</label>
        <div id="categoryBox" class="select-box" tabindex="0">
          <span id="selectedCategory">Select category</span>
          <span>&#9662;</span>
        </div>
      </div>
      <div>
        <label>Size</label>
        <div id="sizeBox" class="select-box" tabindex="0" style="flex:1;">
          <span id="selectedSize">Select size</span>
          <span>&#9662;</span>
        </div>
      </div>
      <div></div>
      <!-- Second row: Cost Price, Selling Price, Quantity, (empty) -->
      <div>
        <label>Cost Price</label>
        <input id="costPrice" type="number" step="0.01" placeholder="0.00" oninput="suggestSP()" />
      </div>
      <div>
        <label>Selling Price</label>
        <input id="sellingPrice" type="number" step="0.01" placeholder="0.00" />
        <div id="spSuggestion" style="font-size:13px;color:#1976d2;margin-top:2px;"></div>
      </div>
      <div>
        <label>Quantity</label>
        <input id="quantity" type="number" placeholder="0" />
      </div>
      <div></div>
      <!-- Third row: Low Stock, Total (left aligned) -->
      <div>
        <label>Low Stock</label>
        <input id="lowStock" type="number" placeholder="0" />
      </div>
      <div>
        <label for="total">Total</label>
        <input id="total" type="number" step="0.01" placeholder="0.00" readonly style="background:#4fc3f7; font-weight:bold;" />
      </div>
    </form>

    <div class="control-buttons">
      <button class="save-btn" onclick="doneProduct()">Add Size</button>
      <button class="save-btn" onclick="saveAndClearProduct()">Add New </button>
      <button class="save-btn" onclick="saveProduct()">Save</button>
      <button class="cancel-btn" onclick="cancelProduct()">Cancel</button>
    </div>
  </main>

  <!-- Category Modal -->
  <div id="categoryModal" class="modal">
    <div class="modal-content">
      <strong>Select Category</strong>
      <input type="text" id="categorySearch" placeholder="Search category..." style="width:100%;margin-bottom:10px;padding:8px 10px;border-radius:5px;border:1px solid #ccc;">
      <ul id="categoryList"></ul>
      <button onclick="closeCategoryModal()">Close</button>
    </div>
  </div>

  <!-- Size Modal -->
  <div id="sizeModal" class="modal">
    <div class="modal-content">
      <strong>Select Size</strong>
      <div style="display: flex; align-items: center; gap: 6px; margin-bottom:10px;">
        <input type="text" id="sizeSearch" placeholder="Search size..." style="flex:1;padding:8px 10px;border-radius:5px;border:1px solid #ccc;min-height:40px;height:40px;">
        <button id="addSizeBtn" type="button" title="Add new size"
          style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid #ccc;border-radius:6px;font-size:22px;font-weight:bold;cursor:pointer;padding:0;color:#222;box-sizing:border-box;position:relative;top:-2%;">
          +
        </button>
      </div>
      <ul id="sizeList"></ul>
      <button onclick="closeSizeModal()">Close</button>
    </div>
  </div>

  <script>
    const categoryBox = document.getElementById('categoryBox');
    const sizeBox = document.getElementById('sizeBox');

    function renderCategoryList(filter = "") {
      const list = document.getElementById('categoryList');
      list.innerHTML = '';
      const categories = JSON.parse(localStorage.getItem('categories') || '[]');
      const filtered = filter
        ? categories.filter(cat => cat.toLowerCase().includes(filter.toLowerCase()))
        : categories;
      if (filtered.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No categories available';
        li.style.color = '#888';
        list.appendChild(li);
      } else {
        filtered.forEach(cat => {
          const li = document.createElement('li');
          li.textContent = cat;
          li.onclick = () => {
            document.getElementById('selectedCategory').textContent = cat;
            closeCategoryModal();
          };
          list.appendChild(li);
        });
      }
    }

    function openCategoryModal() {
      renderCategoryList();
      document.getElementById('categoryModal').style.display = 'flex';
      document.getElementById('categorySearch').value = '';
    }

    document.getElementById('categorySearch').addEventListener('input', function() {
      renderCategoryList(this.value);
    });

    function getCustomSizes() {
      return JSON.parse(localStorage.getItem('customSizes') || '[]');
    }
    function setCustomSizes(sizes) {
      localStorage.setItem('customSizes', JSON.stringify(sizes));
    }

    function renderSizeList(filter = "") {
      const defaultSizes = ['Small', 'Medium', 'Large', 'XL', 'XXL'];
      const customSizes = getCustomSizes();
      // Merge and deduplicate, custom sizes after default
      const sizes = [...defaultSizes, ...customSizes.filter(s => !defaultSizes.includes(s))];
      const list = document.getElementById('sizeList');
      list.innerHTML = '';
      const filtered = filter
        ? sizes.filter(size => size.toLowerCase().includes(filter.toLowerCase()))
        : sizes;
      if (filtered.length === 0) {
        const li = document.createElement('li');
        li.textContent = 'No sizes available';
        li.style.color = '#888';
        list.appendChild(li);
      } else {
        filtered.forEach(size => {
          const li = document.createElement('li');
          li.textContent = size;
          li.onclick = () => {
            document.getElementById('selectedSize').textContent = size;
            closeSizeModal();
          };
          list.appendChild(li);
        });
      }
    }

    function openSizeModal() {
      renderSizeList();
      document.getElementById('sizeModal').style.display = 'flex';
      document.getElementById('sizeSearch').value = '';
    }

    document.getElementById('sizeSearch').addEventListener('input', function() {
      renderSizeList(this.value);
    });

    function closeCategoryModal() {
      document.getElementById('categoryModal').style.display = 'none';
    }

    function closeSizeModal() {
      document.getElementById('sizeModal').style.display = 'none';
    }

    function getProducts() {
      return JSON.parse(localStorage.getItem('products') || '[]');
    }

    function setProducts(products) {
      localStorage.setItem('products', JSON.stringify(products));
    }

    function updateTotal() {
      const sell = parseFloat(document.getElementById('sellingPrice').value) || 0;
      const qty = parseInt(document.getElementById('quantity').value) || 0;
      document.getElementById('total').value = (sell * qty).toFixed(2);
    }

    function saveProduct(showAlert = true, redirect = true) {
      const name = document.getElementById('productName').value.trim();
      const category = document.getElementById('selectedCategory').textContent.trim();
      let size = document.getElementById('selectedSize').textContent.trim();
      const cost = parseFloat(document.getElementById('costPrice').value) || 0;
      const sell = parseFloat(document.getElementById('sellingPrice').value) || 0;
      const quantity = parseInt(document.getElementById('quantity').value) || 0;
      const lowStock = parseInt(document.getElementById('lowStock').value) || 0;
      const total = parseFloat(document.getElementById('total').value) || 0;

      if (!name || !category || category === 'Select category' || cost <= 0 || sell <= 0 || quantity <= 0) {
        if (showAlert) alert("Please fill all fields correctly.");
        return false;
      }

      // If size not selected, set as N/A
      if (!size || size === 'Select size') {
        size = 'N/A';
      }

      let products = getProducts();
      const data = { name, category, size, cost, sell, quantity, total, lowStock };

      const editIdx = localStorage.getItem('editProductIdx');
      if (editIdx !== null) {
        products[parseInt(editIdx)] = data;
        localStorage.removeItem('editProductIdx');
      } else {
        products.push(data);
      }
      setProducts(products);
      if (redirect) window.location.href = 'product.php';
      return true;
    }

    function cancelProduct() {
      localStorage.removeItem('editProductIdx');
      window.location.href = 'product.php';
    }

    function doneProduct() {
      // Only clear if saveProduct returns true (valid data)
      if (saveProduct(true, false)) {
        alert("Product saved and you can now add another product size for the same product!");
        // Keep product name and category, clear all other fields
        document.getElementById('selectedSize').textContent = 'Select size';
        document.getElementById('costPrice').value = '';
        document.getElementById('sellingPrice').value = '';
        document.getElementById('lowStock').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('total').value = '';
        document.getElementById('sizeBox').focus();
      }
    }

    function saveAndClearProduct() {
      if (saveProduct(true, false)) {
        // Clear all fields for new entry, keep focus on product name
        document.getElementById('productName').value = '';
        document.getElementById('selectedCategory').textContent = 'Select category';
        document.getElementById('selectedSize').textContent = 'Select size';
        document.getElementById('costPrice').value = '';
        document.getElementById('sellingPrice').value = '';
        document.getElementById('lowStock').value = '';
        document.getElementById('quantity').value = '';
        document.getElementById('total').value = '';
        document.getElementById('productName').focus();
      }
    }

    // --- Add Size Button Logic ---
    document.getElementById('addSizeBtn').addEventListener('click', function() {
      const input = document.getElementById('sizeSearch');
      let newSize = '';
      // If modal is open, use the search input, else prompt
      if (document.getElementById('sizeModal').style.display === 'flex') {
        newSize = input.value.trim();
      } else {
        newSize = prompt('Enter new size:');
        if (newSize) newSize = newSize.trim();
      }
      if (!newSize) {
        alert('Please enter a size name.');
        return;
      }
      const defaultSizes = ['Small', 'Medium', 'Large', 'XL', 'XXL'];
      let customSizes = getCustomSizes();
      // Prevent duplicates (case-insensitive)
      if (
        defaultSizes.some(s => s.toLowerCase() === newSize.toLowerCase()) ||
        customSizes.some(s => s.toLowerCase() === newSize.toLowerCase())
      ) {
        alert('This size already exists.');
        return;
      }
      customSizes.push(newSize);
      setCustomSizes(customSizes);
      // If modal is open, re-render list and clear search
      if (document.getElementById('sizeModal').style.display === 'flex') {
        document.getElementById('sizeSearch').value = '';
        renderSizeList();
      }
      alert('Size added!');
    });

    document.addEventListener('DOMContentLoaded', () => {
      categoryBox.addEventListener('click', openCategoryModal);
      sizeBox.addEventListener('click', openSizeModal);
      document.getElementById('sellingPrice').addEventListener('input', updateTotal);
      document.getElementById('quantity').addEventListener('input', updateTotal);

      const idx = localStorage.getItem('editProductIdx');
      if (idx !== null) {
        const p = getProducts()[parseInt(idx)];
        if (p) {
          document.getElementById('productName').value = p.name;
          document.getElementById('selectedCategory').textContent = p.category;
          document.getElementById('selectedSize').textContent = p.size;
          document.getElementById('costPrice').value = p.cost;
          document.getElementById('sellingPrice').value = p.sell;
          document.getElementById('quantity').value = p.quantity;
          document.getElementById('lowStock').value = p.lowStock;
          document.getElementById('total').value = p.total;
        }
      }
    });

    function suggestSP() {
      const cp = parseFloat(document.getElementById('costPrice').value) || 0;
      if (cp > 0) {
        const suggested = (cp * 1.2).toFixed(2);
        document.getElementById('spSuggestion').textContent = `Suggested SP (20% profit): ${suggested}`;
      } else {
        document.getElementById('spSuggestion').textContent = '';
      }
    }

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
