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
  <title>Ledger - MyInventory</title>
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
    /* Sidebar */
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

    /* Header */
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

    /* Main Content */
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

    /* Search & Add Button */
    .search-add {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
      max-width: 1000px;
    }
    .search-add input {
      flex: 1;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .search-add a.button {
      background: #4fc3f7;
      color: #fff;
      border: none;
      border-radius: 6px;
      padding: 8px 18px;
      margin-left: 10px;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      user-select: none;
    }
    .search-add a.button:hover {
      background: #3bb0e0;
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
    .edit-btn, .delete-btn, .view-btn {
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
    .view-btn {
      background: #4caf50;
      color: #fff;
    }
    .view-btn:hover { background: #449d48; }

  </style>
</head>
<body>

  <!-- Sidebar Menu -->
  <aside class="sidebar">
    <div class="logo">MyInventory</div>
    <nav>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="product.php"><i class="fas fa-box-open"></i> Products</a>
      <a href="sale.php"><i class="fas fa-shopping-cart"></i> Sales</a>
      <a href="ledgerm.php"><i class="fas fa-file-invoice-dollar"></i> Ledger</a>
      <a href="vat.php" id="vatMenuBtn" class="active"><i class="fas fa-receipt"></i> Vat</a>
      <a href="setting.php" id="settingMenuBtn"><i class="fas fa-cog"></i> Setting</a>
    </nav>
  </aside>

  <!-- Header -->
  <header>
    <div class="user-profile">
      <span></span>
    </div>
  </header>

  <!-- Main Ledger Content -->
  <main>
    <h1>Vat Records</h1>

    <div class="search-add">
      <input type="text" id="searchLedger" placeholder="Search ledger by name or address..." oninput="searchLedger()" />
      <select id="typeFilter" style="margin: 0 10px; border-radius: 6px; border: 1px solid #ccc; padding: 8px 12px; font-size: 14px;" onchange="loadVats()">
        <option value="all">All</option>
        <option value="customer">Customer</option>
        <option value="supplier">Supplier</option>
      </select>
      <a href="ledgerb.php" class="button" id="addBillBtn">+ Add Bill</a>
    </div>

    <table>
      <thead>
        <tr>
          <th>S.N</th>
          <th>Year</th>
          <th>Month</th>
          <th>Type</th>
          <th>Total</th>
          <th>Action</th>
          <th>View</th>
        </tr>
      </thead>
      <tbody id="ledgerTable">
        <!-- Ledger rows start empty -->
      </tbody>
    </table>
  </main>

  <script>
    function loadLedgers() {
      const ledgers = JSON.parse(localStorage.getItem('ledgers') || '[]');
      const tbody = document.getElementById('ledgerTable');
      tbody.innerHTML = '';
      ledgers.forEach((entry, idx) => {
        // Calculate total from ledger entries
        let total = 0;
        if (entry.ledger && entry.ledger.length > 0) {
          const last = entry.ledger[entry.ledger.length - 1];
          total = last.balance || 0;
        }
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${idx + 1}</td>
          <td>${entry.customerName}</td>
          <td>${entry.address}</td>
          <td>${total}</td>
          <td>
            <button class="edit-btn" onclick="editLedger(${idx})">Edit</button>
            <button class="delete-btn" onclick="deleteLedger(${idx})">Delete</button>
          </td>
          <td>
            <button class="view-btn" onclick="window.location.href='bills.php?ledger=${idx}'">View</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
    }

    function updateRowNumbers() {
      // Not needed, handled in loadLedgers
    }

    function editLedger(idx) {
      window.location.href = `ledgerb.php?edit=${idx}`;
    }

    function deleteLedger(idx) {
      if (confirm('Are you sure you want to delete this ledger entry?')) {
        let ledgers = JSON.parse(localStorage.getItem('ledgers') || '[]');
        ledgers.splice(idx, 1);
        localStorage.setItem('ledgers', JSON.stringify(ledgers));
        loadLedgers();
      }
    }

    function viewLedgerRow(idx) {
      const ledgers = JSON.parse(localStorage.getItem('ledgers') || '[]');
      const entry = ledgers[idx];
      if (!entry) return;

      // Simple box for Name, Address, Contact
      let infoSection = `
        <div style="margin-bottom:18px;padding:16px 20px;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.07);display:inline-block;">
          <div style="margin-bottom:6px;"><strong>Name:</strong> ${entry.customerName || ''}</div>
          <div style="margin-bottom:6px;"><strong>Address:</strong> ${entry.address || ''}</div>
          <div><strong>Contact:</strong> ${entry.contact || ''}</div>
        </div>
      `;

      // Ledger details table
      let ledgerHtml = '';
      entry.ledger.forEach((row, i) => {
        ledgerHtml += `<tr>
          <td>${i + 1}</td>
          <td>${row.date}</td>
          <td>${row.particular}</td>
          <td>${row.debit}</td>
          <td>${row.credit}</td>
          <td>${row.balance}</td>
        </tr>`;
      });

      let ledgerTable = `
        <table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
          <thead>
            <tr>
              <th>S.N</th>
              <th>Date</th>
              <th>Particular</th>
              <th>Debit</th>
              <th>Credit</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody>
            ${ledgerHtml}
          </tbody>
        </table>
      `;

      const printWindow = window.open('', '', 'width=900,height=700');
      printWindow.document.write(`
        <html>
        <head>
          <title>View Ledger</title>
          <style>
            body { font-family: 'Inter', sans-serif; background: #f5f7fa; color: #222; padding: 20px; }
            h2 { margin-top: 0; }
            table { border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ccc; padding: 8px 12px; }
            th { background: #4fc3f7; color: #fff; }
            tr:nth-child(even) { background: #f9f9f9; }
            button { margin-right: 10px; padding: 8px 16px; border: none; border-radius: 4px; background: #4fc3f7; color: #fff; font-weight: 600; cursor: pointer; }
            button:hover { background: #3bb0e0; }
          </style>
        </head>
        <body>
          <h2>Ledger Details</h2>
          ${infoSection}
          ${ledgerTable}
          <button onclick="window.print()">Print</button>
          <button onclick="window.close()">Close</button>
        </body>
        </html>
      `);
      printWindow.document.close();
    }

    function searchLedger() {
      const filter = document.getElementById('searchLedger').value.toLowerCase();
      const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
      const rows = document.querySelectorAll('#ledgerTable tr');
      rows.forEach(row => {
        const name = row.cells[1].innerText.toLowerCase();
        const address = row.cells[2].innerText.toLowerCase();
        const type = row.cells[3].innerText.toLowerCase();
        if ((name.includes(filter) || address.includes(filter)) && (typeFilter === 'all' || type === typeFilter)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    function loadVats() {
      const vats = JSON.parse(localStorage.getItem('vats') || '[]');
      const tbody = document.getElementById('ledgerTable');
      tbody.innerHTML = '';

      const typeFilter = (document.getElementById('typeFilter')?.value || 'all').toLowerCase();

      // Show most recent first
      for (let i = vats.length - 1; i >= 0; i--) {
        const entry = vats[i];
        if (typeFilter !== 'all' && entry.billType.toLowerCase() !== typeFilter) {
          continue;
        }
        let total = 0;
        if (entry.entries && entry.entries.length > 0) {
          total = entry.entries.reduce((sum, e) => sum + (parseFloat(e.totalAmount) || 0), 0);
        }
        // Capitalize first letter of month and type
        const monthDisplay = entry.month ? entry.month.charAt(0).toUpperCase() + entry.month.slice(1).toLowerCase() : '';
        const typeDisplay = entry.billType ? entry.billType.charAt(0).toUpperCase() + entry.billType.slice(1).toLowerCase() : '';
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${vats.length - i}</td>
          <td>${entry.year}</td>
          <td>${monthDisplay}</td>
          <td>${typeDisplay}</td>
          <td>${total.toFixed(2)}</td>
          <td>
            <button class="edit-btn" onclick="editVat(${i})">Edit</button>
            <button class="delete-btn" onclick="deleteVat(${i})">Delete</button>
          </td>
          <td>
            <button class="view-btn" onclick="viewVat(${i})">View</button>
          </td>
        `;
        tbody.appendChild(tr);
      }
    }

    function editVat(idx) {
      // Redirect to vats.php with the index as a query parameter for editing
      window.location.href = `vats.php?edit=${idx}`;
    }

    function deleteVat(idx) {
      if (confirm('Are you sure you want to delete this VAT record?')) {
        let vats = JSON.parse(localStorage.getItem('vats') || '[]');
        vats.splice(idx, 1);
        localStorage.setItem('vats', JSON.stringify(vats));
        loadVats();
      }
    }

    function viewVat(idx) {
      // Add overlay to disable the page before redirect
      const overlay = document.createElement('div');
      overlay.style.position = 'fixed';
      overlay.style.top = '0';
      overlay.style.left = '0';
      overlay.style.width = '100vw';
      overlay.style.height = '100vh';
      overlay.style.background = 'rgba(255,255,255,0.6)';
      overlay.style.zIndex = '9999';
      overlay.style.cursor = 'wait';
      document.body.appendChild(overlay);
      // Redirect to vatbill.php with the index as a query parameter
      window.location.href = `vatbill.php?vat=${idx}`;
    }

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
      document.getElementById('addBillBtn').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = 'vats.php';
      });
      var settingBtn = document.getElementById('settingMenuBtn');
      if (settingBtn) {
        settingBtn.addEventListener('click', function(e) {
          e.preventDefault();
          showPasswordPopup();
        });
      }
      populateYearSelect();
      // Call loadVats() instead of loadLedgers()
      loadVats();
    });

    // Populate year select (partyTypeRight) with 2050-3000, select 2082 by default if not set
    function populateYearSelect() {
      const yearSelect = document.getElementById('partyTypeRight');
      if (!yearSelect) return;
      yearSelect.innerHTML = '';
      const currentYear = localStorage.getItem('selectedYear') || '2082';
      for (let y = 2050; y <= 3000; y++) {
        const opt = document.createElement('option');
        opt.value = y;
        opt.textContent = y;
        if (currentYear == y) opt.selected = true;
        yearSelect.appendChild(opt);
      }
      // Save selection on change
      yearSelect.addEventListener('change', function() {
        localStorage.setItem('selectedYear', this.value);
      });
    }
  </script>

</body>
</html>
</html>
</html>
</html>
</html>
  </script>
</body>
</html>
</html>
</html>
</html>
</html>
</html>
