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
  <title>Invoice Entry</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
      color: #222;
      padding: 30px 40px;
    }
    h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #1a202c;
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 600;
    }
    input, select, button {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      margin-bottom: 20px;
    }
    input[type="date"] {
      width: 65%;
    }
    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
      align-items: center;
    }
    .form-row > div {
      flex: 1;
    }
    .product-search {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    .product-search input {
      flex: unset;
      width: 700px; /* Increase this value as needed */
      min-width: 0;
      margin-left: -223px; /* Shift the box to the left, adjust as needed */
    }
    .search-btn {
      background: #4fc3f7;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
    }
    .search-btn:hover {
      background: #3bb0e0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 6px 15px; /* reduced vertical padding from 12px to 6px */
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background-color: #4fc3f7;
      color: #fff;
      font-weight: 600;
      font-size: 14px;
    }
    input.inline-input {
      width: 70px;
      padding: 5px;
      font-size: 14px;
      text-align: center;
    }
  </style>
</head>
<body>

  <h1>Invoice Entry</h1>

  <!-- Name, Address, Contact Row -->
  <div class="form-row" style="gap: 8px;">
    <div>
      <label>Name:</label>
      <input type="text" placeholder="Enter name" style="width:97%;" />
    </div>
    <div>
      <label>Address:</label>
      <input type="text" placeholder="Enter address" style="width:97%;" />
    </div>
    <div>
      <label>Contact:</label>
      <input type="text" placeholder="Enter contact number" style="width:97%;" maxlength="10" pattern="[0-9]{0,10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" />
    </div>
  </div>

  <!-- Date & Product Row -->
  <div class="form-row">
    <div>
      <label>Date:</label>
      <input type="date" />
    </div>
    <div>
      <label>Select Product:</label>
      <div class="product-search">
        <input list="productList" id="productInput" placeholder="Search and select product..." />
        <button class="search-btn" onclick="addProduct()">Add Product</button>
      </div>
      <datalist id="productList">
        <!-- Options will be populated by JS -->
      </datalist>
    </div>
  </div>

  <!-- Product Table -->
  <table id="productTable">
    <thead>
      <tr>
        <th>S.N</th>
        <th>Product</th>
        <th>Stock</th>
        <th>CP</th>
        <th>SP</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <!-- Product Details Box (shown after adding a product) -->
  <div id="productDetailsBox" style="display:none; margin-top:20px; background:#e3f2fd; border-radius:8px; padding:16px; max-width:400px;">
    <h3 style="margin-top:0;">Product Details</h3>
    <div id="productDetailsContent"></div>
  </div>

  <!-- Discount and Payment Section -->
  <div style="display: flex; flex-direction: row; gap: 8px; justify-content: flex-end; align-items: flex-end; margin-top: 30px;">
    <!-- Total Amount -->
    <div style="width: 180px;">
      <label style="font-weight:600;">Total Amount</label>
      <input type="text" id="totalAmountBox" readonly style="width:100%; background:#f5f7fa; border:1px solid #ccc; border-radius:6px; padding:10px; font-size:16px; font-weight:600; text-align:right; margin-bottom:0;">
    </div>
    <!-- Discount % -->
    <div style="width: 120px;">
      <label>Discount %</label>
      <input type="number" id="discountPercent" min="0" max="100" placeholder="%" style="width:100%; margin-bottom:0;" />
    </div>
    <!-- Discount Amount -->
    <div style="width: 140px;">
      <label>Discount Amount</label>
      <input type="number" id="discountAmount" min="0" placeholder="Rs." style="width:100%; margin-bottom:0;" />
    </div>
    <!-- Payment Status -->
    <div style="width: 140px;">
      <label>Payment Status</label>
      <select id="paymentStatus" style="width:100%; margin-bottom:0;">
        <option value="Paid">Paid</option>
        <option value="Not Paid">Not Paid</option>
        <option value="Partial Paid">Partial Paid</option>
      </select>
    </div>
    <!-- Partial Paid Amount (hidden by default) -->
    <div id="partialPaidBox" style="width: 140px; display:none;">
      <label>Partial Paid Amount</label>
      <input type="number" id="partialPaidAmount" min="0" placeholder="Rs." style="width:100%; margin-bottom:0;" />
    </div>
    <!-- Grand Total -->
    <div style="width: 180px;">
      <label style="font-weight:600;">Grand Total</label>
      <input type="text" id="grandTotalBox" readonly style="width:100%; background:#f5f7fa; border:1px solid #ccc; border-radius:6px; padding:10px; font-size:16px; font-weight:600; text-align:right; margin-bottom:0;">
    </div>
  </div>

  <!-- Save and Cancel Buttons below Grand Total -->
  <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
    <button class="button" id="saveBtn" style="margin-right: 10px; background: #4caf50; color: #fff;" onclick="saveInvoice()">Save</button>
    <button class="button" style="background: #f44336; color: #fff;" onclick="window.location.href='sale.php'">Cancel</button>
  </div>

  <script>
    let sn = 1;
    const stockData = {
      "Laptop": { stock: 10, cp: 50000, sp: 60000 },
      "Smartphone": { stock: 20, cp: 15000, sp: 18000 },
      "Charger": { stock: 50, cp: 300, sp: 500 },
      "Keyboard": { stock: 30, cp: 700, sp: 1000 },
      "Mouse": { stock: 40, cp: 400, sp: 600 },
      "CCTV Camera": { stock: 15, cp: 3000, sp: 3500 },
      "RAM": { stock: 25, cp: 2000, sp: 2500 },
      "SSD 512GB": { stock: 12, cp: 4000, sp: 4800 },
      "Ink Bottle": { stock: 60, cp: 200, sp: 300 }
    };

    // --- Discount and Grand Total Logic ---
    const discountPercent = document.getElementById('discountPercent');
    const discountAmount = document.getElementById('discountAmount');
    const totalAmountBox = document.getElementById('totalAmountBox');
    const grandTotalBox = document.getElementById('grandTotalBox');
    const paymentStatus = document.getElementById('paymentStatus');
    const partialPaidBox = document.getElementById('partialPaidBox');
    const partialPaidAmount = document.getElementById('partialPaidAmount');

    // Calculate total amount from table
    function getTotalAmount() {
      let total = 0;
      document.querySelectorAll('#productTable tbody tr').forEach(row => {
        const totalCell = row.querySelector('td:nth-child(7)');
        if (totalCell && totalCell.innerText.startsWith('Rs.')) {
          total += Number(totalCell.innerText.replace('Rs. ', '')) || 0;
        }
      });
      return total;
    }

    // Sync Discount % and Discount Amount
    function syncDiscountFields(source) {
      const total = getTotalAmount();
      let percent = Number(discountPercent.value) || 0;
      let amount = Number(discountAmount.value) || 0;

      if (source === 'percent') {
        amount = total * (percent / 100);
        discountAmount.value = amount.toFixed(2);
      } else if (source === 'amount') {
        percent = total === 0 ? 0 : (amount / total) * 100;
        discountPercent.value = percent.toFixed(2);
      }
      updateGrandTotal();
    }

    // Update Grand Total and Partial Paid logic
    function updateGrandTotal() {
      const total = getTotalAmount();
      totalAmountBox.value = "Rs. " + total.toFixed(2);

      const discount = Number(discountAmount.value) || 0;
      let grandTotal = total - discount;
      if (grandTotal < 0) grandTotal = 0;

      // Subtract partial paid from grand total if payment status is Partial Paid
      if (paymentStatus && paymentStatus.value === "Partial Paid") {
        const partial = Number(partialPaidAmount.value) || 0;
        grandTotal = grandTotal - partial;
        if (grandTotal < 0) grandTotal = 0;
      }

      grandTotalBox.value = "Rs. " + grandTotal.toFixed(2);
    }

    // Discount field events
    if (discountPercent) {
      discountPercent.addEventListener('input', function() {
        syncDiscountFields('percent');
      });
    }
    if (discountAmount) {
      discountAmount.addEventListener('input', function() {
        syncDiscountFields('amount');
      });
    }

    // Payment status logic
    if (paymentStatus) {
      paymentStatus.addEventListener('change', function() {
        if (this.value === "Partial Paid") {
          partialPaidBox.style.display = "";
        } else {
          partialPaidBox.style.display = "none";
          partialPaidAmount.value = "";
        }
        updateGrandTotal();
      });
    }
    if (partialPaidAmount) {
      partialPaidAmount.addEventListener('input', updateGrandTotal);
    }

    // Update totals when table changes
    function updateAllTotals() {
      syncDiscountFields('percent');
    }

    // Helper: clamp value to integer (allow 0 for qty)
    function clampInt(val, min, max) {
      let n = parseInt(val, 10);
      if (isNaN(n)) n = min;
      if (n < min) n = min;
      if (typeof max === "number" && n > max) n = max;
      return n;
    }

    // Add product row with editable SP and QTY (contenteditable)
    function addProduct() {
      const inputVal = document.getElementById("productInput").value;
      let products = [];
      try {
        products = JSON.parse(localStorage.getItem('products') || '[]');
      } catch (e) {
        products = [];
      }
      // Extract name, size, and category from input value (e.g. 'A | Size: 1ltr | Category: X')
      let selectedName = inputVal.split('|')[0].trim();
      let selectedSize = '';
      let selectedCategory = '';
      let sizeMatch = inputVal.match(/Size:\s*([^|]+)/i);
      if (sizeMatch) selectedSize = sizeMatch[1].trim();
      let catMatch = inputVal.match(/Category:\s*([^|]+)/i);
      if (catMatch) selectedCategory = catMatch[1].trim();
      // Find product by name, size, and category
      let found = products.find(p =>
        p.name === selectedName &&
        (p.size || '') === (selectedSize || '') &&
        (p.category || '') === (selectedCategory || '')
      );
      if (!found) {
        alert("Please select a valid product");
        return;
      }
      const product = found.name;
      const size = found.size || '';
      const category = found.category || '';
      const data = found;
      const quantity = 1;
      const table = document.getElementById("productTable").querySelector("tbody");
      const row = document.createElement("tr");
      const spId = `sp-${sn}`;
      const qtyId = `qty-${sn}`;
      const totalId = `total-${sn}`;
      const maxStock = data.stock !== undefined ? data.stock : (data.quantity !== undefined ? data.quantity : 0);
      row.innerHTML = `
        <td></td>
        <td>${product}
          ${size ? `<div style='font-size:12px;color:#888;'>Size: ${size}</div>` : ''}
          ${category ? `<div style='font-size:12px;color:#888;'>Category: ${category}</div>` : ''}
        </td>
        <td>${maxStock}</td>
        <td>Rs. ${data.cp !== undefined ? data.cp : (data.cost !== undefined ? data.cost : '')}</td>
        <td id="${spId}" style="color:#1db954;font-weight:bold;cursor:pointer;" onclick="editCell(this, 'sp', ${sn}, ${maxStock})">${data.sp !== undefined ? data.sp : (data.sell !== undefined ? data.sell : '')}</td>
        <td id="${qtyId}" style="color:#1976d2;font-weight:bold;cursor:pointer;" onclick="editCell(this, 'qty', ${sn}, ${maxStock})">${quantity}</td>
        <td id="${totalId}">Rs. ${(data.sp !== undefined ? data.sp : (data.sell !== undefined ? data.sell : 0)) * quantity}</td>
        <td><button class="delete-btn" onclick="deleteProductRow(this)">Delete</button></td>
      `;
      if (table.firstChild) {
        table.insertBefore(row, table.firstChild);
      } else {
        table.appendChild(row);
      }
      document.getElementById("productInput").value = "";
      sn++;
      setTimeout(() => {
        validateAndUpdate(spId, qtyId, totalId, maxStock);
        updateAllTotals();
        checkInvalidRows();
      }, 0);
      const rows = document.querySelectorAll('#productTable tbody tr');
      rows.forEach((row, i) => {
        row.cells[0].innerText = i + 1;
      });
      showProductDetails(data);
    }

    // Add editCell function for inline editing
    function editCell(cell, type, rowId, maxStock) {
      if (cell.querySelector('input')) return; // already editing
      const oldValue = cell.innerText;
      const input = document.createElement('input');
      input.type = 'number';
      input.value = oldValue;
      input.style.width = '60px';
      input.style.fontWeight = 'bold';
      input.style.textAlign = 'center';
      input.style.border = '1px solid #ccc';
      input.style.background = '#fff';
      input.style.color = type === 'sp' ? '#1db954' : '#1976d2';
      input.onblur = function() {
        let val = input.value;
        if (val === "" || isNaN(val)) val = (type === 'sp' ? oldValue : "0");
        cell.innerText = val;
        cell.style.color = type === 'sp' ? '#1db954' : '#1976d2';
        updateTotal(rowId);
      };
      input.onkeydown = function(e) {
        if (e.key === "Enter" || e.key === "Tab") {
          input.blur();
        }
      };
      cell.innerText = "";
      cell.appendChild(input);
      input.focus();
      input.select();
    }

    // Patch validateAndUpdate to use cell text for SP and QTY
    function validateAndUpdate(spId, qtyId, totalId, maxStock) {
      const spCell = document.getElementById(spId);
      const qtyCell = document.getElementById(qtyId);
      const totalCell = document.getElementById(totalId);
      let sp = parseFloat(spCell.innerText) || 0;
      let qty = parseFloat(qtyCell.innerText) || 0;
      if (qty > maxStock) {
        qtyCell.style.background = "#ffeaea";
        totalCell.innerText = "Invalid (Stock)";
      } else {
        qtyCell.style.background = "";
        totalCell.innerText = 'Rs. ' + (sp * qty).toFixed(2);
        setTimeout(updateAllTotals, 0);
      }
      checkInvalidRows();
    }

    // Patch updateTotal to use cell text
    function updateTotal(rowId) {
      const spCell = document.getElementById(`sp-${rowId}`);
      const qtyCell = document.getElementById(`qty-${rowId}`);
      const totalCell = document.getElementById(`total-${rowId}`);
      const row = spCell.closest('tr');
      const maxStock = parseFloat(row.cells[2].innerText) || 0;
      validateAndUpdate(`sp-${rowId}`, `qty-${rowId}`, `total-${rowId}`, maxStock);
    }

    // Patch prefillEditSale to use colored text cells for SP and QTY
    function prefillEditSale() {
      const editIdx = localStorage.getItem('editSaleIdx');
      if (editIdx === null) return;
      let sales = [];
      try {
        sales = JSON.parse(localStorage.getItem('salesData') || '[]');
      } catch (e) {
        sales = [];
      }
      const sale = sales[editIdx];
      if (!sale) return;

      // Do NOT restore or add to stock here. Only update the UI for editing.
      // Prefill form fields
      document.querySelector('input[placeholder="Enter name"]').value = sale.customer || '';
      document.querySelector('input[placeholder="Enter address"]').value = sale.address || '';
      document.querySelector('input[placeholder="Enter contact number"]').value = sale.contact || '';
      document.querySelector('input[type="date"]').value = sale.date || '';
      paymentStatus.value = sale.paidStatus || 'Paid';

      // Prefill discount fields
      discountPercent.value = sale.discountPercent !== undefined ? sale.discountPercent : '';
      discountAmount.value = sale.discountAmount !== undefined ? sale.discountAmount : '';

      // Prefill partial paid amount if exists and show/hide box
      if (sale.paidStatus === "Partial Paid") {
        partialPaidBox.style.display = "";
        partialPaidAmount.value = sale.partialPaidAmount || '';
      } else {
        partialPaidBox.style.display = "none";
        partialPaidAmount.value = "";
      }

      // Prefill product table
      const table = document.getElementById("productTable").querySelector("tbody");
      table.innerHTML = '';
      sn = 1;
      if (Array.isArray(sale.items)) {
        sale.items.forEach(item => {
          const row = document.createElement("tr");
          const spId = `sp-${sn}`;
          const qtyId = `qty-${sn}`;
          const totalId = `total-${sn}`;
          row.innerHTML = `
            <td>${sn}</td>
            <td>${item.product}
              ${item.size ? `<div style='font-size:12px;color:#888;'>Size: ${item.size}</div>` : ''}
              ${item.category ? `<div style='font-size:12px;color:#888;'>Category: ${item.category}</div>` : ''}
            </td>
            <td>${item.stock}</td>
            <td>${item.cp}</td>
            <td id="${spId}" style="color:#1db954;font-weight:bold;cursor:pointer;" onclick="editCell(this, 'sp', ${sn}, ${item.stock})">${item.sp}</td>
            <td id="${qtyId}" style="color:#1976d2;font-weight:bold;cursor:pointer;" onclick="editCell(this, 'qty', ${sn}, ${item.stock})">${item.qty}</td>
            <td id="${totalId}">${item.total}</td>
            <td><button class="delete-btn" onclick="deleteProductRow(this)">Delete</button></td>
          `;
          table.appendChild(row);
          setTimeout(() => {
            const maxStock = parseFloat(item.stock) || 0;
            validateAndUpdate(spId, qtyId, totalId, maxStock);
            checkInvalidRows();
          }, 0);
          sn++;
        });
      }
      updateAllTotals();
    }

    // Patch saveInvoice to use cell text for SP and QTY
    function saveInvoice() {
      // Get form data
      const customerName = document.querySelector('input[placeholder="Enter name"]').value;
      const address = document.querySelector('input[placeholder="Enter address"]').value;
      const contact = document.querySelector('input[placeholder="Enter contact number"]').value;
      let dateInput = document.querySelector('input[type="date"]');
      let date = dateInput.value;
      // If date is empty, set to today
      if (!date) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        date = `${yyyy}-${mm}-${dd}`;
        dateInput.value = date;
      }
      const total = grandTotalBox.value.replace('Rs. ', '');
      const paidStatus = paymentStatus.value;
      const partialPaid = paidStatus === "Partial Paid" ? (Number(partialPaidAmount.value) || 0) : 0;

      // Get discount fields
      const discountPercentVal = discountPercent.value;
      const discountAmountVal = discountAmount.value;

      // Get product items from table
      const items = [];
      document.querySelectorAll('#productTable tbody tr').forEach(row => {
        // Extract product name, size, and category from the cell
        let productCell = row.cells[1];
        let productName = '';
        let productSize = '';
        let productCategory = '';
        if (productCell) {
          // Get the first text node (product name)
          if (productCell.childNodes.length > 0 && productCell.childNodes[0].nodeType === Node.TEXT_NODE) {
            productName = productCell.childNodes[0].textContent.trim();
          } else {
            productName = productCell.textContent.split('\n')[0].trim();
          }
          // Get the size and category from the divs if present
          let divs = productCell.querySelectorAll('div');
          divs.forEach(div => {
            if (div.textContent.trim().toLowerCase().startsWith('size:')) {
              productSize = div.textContent.replace(/size:/i, '').trim();
            } else if (div.textContent.trim().toLowerCase().startsWith('category:')) {
              productCategory = div.textContent.replace(/category:/i, '').trim();
            }
          });
        }
        items.push({
          product: productName,
          size: productSize,
          category: productCategory,
          stock: row.cells[2].innerText,
          cp: row.cells[3].innerText,
          sp: row.cells[4].innerText,
          qty: row.cells[5].innerText,
          total: row.cells[6].innerText
        });
      });

      if (!customerName || !address || !contact || !date || items.length === 0) {
        alert('Please fill all required fields and add at least one product');
        return;
      }

      let sales = JSON.parse(localStorage.getItem('salesData') || '[]');
      const editIdx = localStorage.getItem('editSaleIdx');
      const saleObj = {
        customer: customerName,
        address: address,
        contact: contact,
        date: date,
        items: items,
        total: total,
        paidStatus: paidStatus,
        partialPaidAmount: partialPaid,
        discountPercent: discountPercentVal,
        discountAmount: discountAmountVal
      };

      // --- New logic: If editing and nothing changed, act as cancel ---
      if (editIdx !== null && sales[editIdx]) {
        const old = sales[editIdx];
        function itemsEqual(a, b) {
          if (!Array.isArray(a) || !Array.isArray(b) || a.length !== b.length) return false;
          for (let i = 0; i < a.length; i++) {
            const ai = a[i], bi = b[i];
            if (
              ai.product !== bi.product ||
              (ai.size || '') !== (bi.size || '') ||
              (ai.category || '') !== (bi.category || '') ||
              String(ai.stock) !== String(bi.stock) ||
              String(ai.cp) !== String(bi.cp) ||
              String(ai.sp) !== String(bi.sp) ||
              String(ai.qty) !== String(bi.qty) ||
              String(ai.total) !== String(bi.total)
            ) return false;
          }
          return true;
        }
        if (
          (old.customer || '') === (saleObj.customer || '') &&
          (old.address || '') === (saleObj.address || '') &&
          (old.contact || '') === (saleObj.contact || '') &&
          (old.date || '') === (saleObj.date || '') &&
          (old.total || '') === (saleObj.total || '') &&
          (old.paidStatus || '') === (saleObj.paidStatus || '') &&
          (String(old.partialPaidAmount || '') === String(saleObj.partialPaidAmount || '')) &&
          (String(old.discountPercent || '') === String(saleObj.discountPercent || '')) &&
          (String(old.discountAmount || '') === String(saleObj.discountAmount || '')) &&
          itemsEqual(old.items, saleObj.items)
        ) {
          // No changes, act as cancel
          localStorage.removeItem('editSaleIdx');
          window.location.href = 'sale.php';
          return;
        }
      }

      // Deduct stock from products in localStorage (match by name, size, and category)
      let products = JSON.parse(localStorage.getItem('products') || '[]');
      items.forEach(item => {
        const idx = products.findIndex(p => p.name === item.product && (p.size || '') === (item.size || '') && (p.category || '') === (item.category || ''));
        if (idx !== -1) {
          let currentStock = products[idx].quantity !== undefined ? parseInt(products[idx].quantity) : (products[idx].stock !== undefined ? parseInt(products[idx].stock) : 0);
          let qtyToDeduct = parseInt(item.qty) || 0;
          let newStock = currentStock - qtyToDeduct;
          if (newStock < 0) newStock = 0;
          if (products[idx].quantity !== undefined) {
            products[idx].quantity = newStock;
          } else if (products[idx].stock !== undefined) {
            products[idx].stock = newStock;
          }
        }
      });
      localStorage.setItem('products', JSON.stringify(products));

      if (editIdx !== null && sales[editIdx]) {
        sales[editIdx] = saleObj;
        localStorage.removeItem('editSaleIdx');
      } else {
        sales.push(saleObj);
      }

      localStorage.setItem('salesData', JSON.stringify(sales));
      window.location.href = 'sale.php';
    }

    // Populate productList datalist from products saved in product.php
    function populateProductList() {
      const datalist = document.getElementById('productList');
      datalist.innerHTML = '';
      let products = [];
      try {
        products = JSON.parse(localStorage.getItem('products') || '[]');
      } catch (e) {
        products = [];
      }
      // Update datalist to show name, size, and category in label
      products.forEach(product => {
        if (product.name) {
          const option = document.createElement('option');
          let label = product.name;
          if (product.size) label += ` | Size: ${product.size}`;
          if (product.category) label += ` | Category: ${product.category}`;
          option.value = label;
          datalist.appendChild(option);
        }
      });
    }

    // Calculate and show total sales and revenue for the current week
function getStartOfWeek(date) {
  const d = new Date(date);
  const day = d.getDay(); // 0=Sun, 1=Mon, ...
  d.setHours(0,0,0,0);
  d.setDate(d.getDate() - day); // Go to Sunday
  return d;
}
function getEndOfWeek(date) {
  const d = getStartOfWeek(date);
  d.setDate(d.getDate() + 6); // Saturday
  d.setHours(23,59,59,999);
  return d;
}
function getWeeklySalesAndRevenue() {
  const sales = JSON.parse(localStorage.getItem('salesData') || '[]');
  const now = new Date();
  const weekStart = getStartOfWeek(now);
  const weekEnd = getEndOfWeek(now);
  let totalSalesAmount = 0;
  let totalRevenue = 0;
  sales.forEach(sale => {
    if (!sale.date) return;
    const saleDate = new Date(sale.date);
    if (saleDate >= weekStart && saleDate <= weekEnd) {
      let saleTotal = parseFloat(sale.total) || 0;
      let discount = parseFloat(sale.discountAmount) || 0;
      let grandTotal = saleTotal - discount;
      if (grandTotal < 0) grandTotal = 0;
      totalSalesAmount += saleTotal;
      totalRevenue += grandTotal;
    }
  });
  return { totalSalesAmount, totalRevenue };
}
// Example usage (update your dashboard with these values):
// const weekStats = getWeeklySalesAndRevenue();
// document.getElementById('totalSalesAmount').innerText = weekStats.totalSalesAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
// document.getElementById('totalStockValue').innerText = weekStats.totalRevenue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

    // Call on page load
    document.addEventListener('DOMContentLoaded', function() {
      populateProductList();
      updateAllTotals();
      prefillEditSale();
      checkInvalidRows();
    });
    function checkInvalidRows() {
      const saveBtn = document.getElementById('saveBtn');
      let invalid = false;
      document.querySelectorAll('#productTable tbody tr').forEach(row => {
        if (row.cells[6].innerText === "Invalid (Stock)") invalid = true;
      });
      if (saveBtn) saveBtn.disabled = invalid;
    }

function deleteProductRow(btn) {
  const row = btn.closest('tr');
  if (row) {
    row.remove();
    // Optionally, update totals or re-number S.N.
    const rows = document.querySelectorAll('#productTable tbody tr');
    rows.forEach((row, i) => {
      if (row.cells[0]) row.cells[0].innerText = i + 1;
    });
    if (typeof updateAllTotals === 'function') updateAllTotals();
  }
}
  </script>

</body>
</html>
