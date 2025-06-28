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
    main {
      padding: 30px 40px;
    }
    main h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #1a202c;
    }
    .input-form, .ledger-entry-form {
      display: flex;
      gap: 20px;
      margin-bottom: 25px;
      flex-wrap: wrap;
      align-items: flex-end;
    }
    .input-group, .ledger-entry-form div {
      display: flex;
      flex-direction: column;
      flex: 1 1 200px;
    }
    label {
      font-weight: 600;
      margin-bottom: 6px;
      font-size: 14px;
      color: #333;
    }
    input {
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 6px 12px rgba(0,0,0,0.08);
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 20px;
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
    .delete-btn {
      padding: 4px 8px;
      font-size: 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin: 2px;
      color: #fff;
    }
    .edit-btn { background: #2196f3; }
    .delete-btn-danger { background: #f44336; }
    .button-row {
      display: flex;
      gap: 10px;
    }
    .footer-totals {
      font-weight: bold;
      background: #e3f2fd;
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
    .ledger-entry-form button {
      background: #4fc3f7;
      color: white;
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      padding: 8px 16px;
      border: none;
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
    <h1>Ledger Records</h1>

    <form id="customerInfoForm" class="input-form" onsubmit="return false;">
      <div class="input-group">
        <label for="customerName">Customer Name</label>
        <input id="customerName" type="text" placeholder="Enter name" />
      </div>
      <div class="input-group">
        <label for="address">Address</label>
        <input id="address" type="text" placeholder="Enter address" />
      </div>
      <div class="input-group">
        <label for="contact">Contact</label>
        <input id="contact" type="text" placeholder="Enter contact number" maxlength="10" pattern="[0-9]{0,10}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" />
      </div>
    </form>

    <form id="ledgerEntryForm" class="ledger-entry-form" onsubmit="return false;">
      <div>
        <label>Date:</label>
        <input
          type="text"
          id="ledgerDateInput"
          placeholder="YYYY/MM/DD"
          maxlength="10"
          style="width: 100%;"
          oninput="formatLedgerDateInput(this)"
          autocomplete="off"
          readonly
          onclick="enableLedgerDateInput()"
        />
      </div>
      <div>
        <label for="particular">Particular</label>
        <input id="particular" type="text" placeholder="Description" />
      </div>
      <div>
        <label for="debit">Debit</label>
        <input id="debit" type="number" step="0.01" placeholder="0.00" />
      </div>
      <div>
        <label for="credit">Credit</label>
        <input id="credit" type="number" step="0.01" placeholder="0.00" />
      </div>
      <button type="button" id="addEntryBtn" onclick="addLedgerEntry()" style="background:#4fc3f7;color:#fff;font-weight:600;border-radius:6px;cursor:pointer;padding:8px 16px;border:none;">Add Entry</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>S.N</th>
          <th>Date</th>
          <th>Particular</th>
          <th>Debit (Rs.)</th>
          <th>Credit (Rs.)</th>
          <th>Balance (Rs.)</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="ledgerTable"></tbody>
      <tfoot>
        <tr class="footer-totals">
          <td colspan="3">Total</td>
          <td id="totalDebit">0.00</td>
          <td id="totalCredit">0.00</td>
          <td id="finalBalance">0.00</td>
          <td></td>
        </tr>
      </tfoot>
    </table>

    <div class="control-buttons">
      <button class="save-btn" onclick="saveLedger()">Save</button>
      <button class="cancel-btn" onclick="cancelLedger()">Cancel</button>
    </div>
  </main>

  <script>
    let balance = 0;
    let editingRow = null;

    function updateRowNumbers() {
      const rows = document.querySelectorAll('#ledgerTable tr');
      rows.forEach((row, i) => row.cells[0].innerText = i + 1);
    }

    function updateTotals() {
      let debitTotal = 0, creditTotal = 0;
      document.querySelectorAll('#ledgerTable tr').forEach(row => {
        debitTotal += parseFloat(row.cells[3].innerText) || 0;
        creditTotal += parseFloat(row.cells[4].innerText) || 0;
      });
      document.getElementById('totalDebit').innerText = debitTotal.toFixed(2);
      document.getElementById('totalCredit').innerText = creditTotal.toFixed(2);
      document.getElementById('finalBalance').innerText = balance.toFixed(2);
    }

    function addLedgerEntry() {
      // Use the correct input id for date
      const date = document.getElementById('ledgerDateInput').value;
      const particular = document.getElementById('particular').value.trim();
      const debitInput = document.getElementById('debit');
      const creditInput = document.getElementById('credit');
      const debit = parseFloat(debitInput.value) || 0;
      const credit = parseFloat(creditInput.value) || 0;

      // Only allow one of debit or credit to be entered
      if ((debit > 0 && credit > 0) || (debit === 0 && credit === 0)) {
        alert("Please enter either debit or credit, not both.");
        return;
      }

      if (!date || !particular) {
        alert("Please enter valid date and particular.");
        return;
      }

      if (editingRow) {
        const oldDebit = parseFloat(editingRow.cells[3].innerText) || 0;
        const oldCredit = parseFloat(editingRow.cells[4].innerText) || 0;
        balance -= oldCredit - oldDebit;
        balance += credit - debit;

        editingRow.cells[1].innerText = date;
        editingRow.cells[2].innerText = particular;
        editingRow.cells[3].innerText = debit ? debit.toFixed(2) : '';
        editingRow.cells[4].innerText = credit ? credit.toFixed(2) : '';
        editingRow.cells[5].innerText = balance.toFixed(2);
        editingRow = null;
        document.querySelector('#ledgerEntryForm button').innerText = 'Add Entry';
      } else {
        balance += credit - debit;

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td></td>
          <td>${date}</td>
          <td>${particular}</td>
          <td>${debit ? debit.toFixed(2) : ''}</td>
          <td>${credit ? credit.toFixed(2) : ''}</td>
          <td>${balance.toFixed(2)}</td>
          <td>
            <button class="delete-btn delete-btn-danger" onclick="deleteLedgerEntry(this)">Delete</button>
            <button class="delete-btn edit-btn" onclick="editLedgerEntry(this)">Edit</button>
          </td>`;
        // Insert at the top to show most recent first
        const ledgerTable = document.getElementById('ledgerTable');
        if (ledgerTable.firstChild) {
          ledgerTable.insertBefore(tr, ledgerTable.firstChild);
        } else {
          ledgerTable.appendChild(tr);
        }
      }

      updateRowNumbers();
      updateTotals();

      // Clear the correct input id for date
      document.getElementById('ledgerDateInput').value = '';
      document.getElementById('particular').value = '';
      debitInput.value = '';
      creditInput.value = '';
      debitInput.disabled = false;
      creditInput.disabled = false;
    }

    function deleteLedgerEntry(button) {
      const row = button.closest('tr');
      const debit = parseFloat(row.cells[3].innerText) || 0;
      const credit = parseFloat(row.cells[4].innerText) || 0;
      balance -= credit - debit;
      row.remove();
      recalcBalances();
      updateRowNumbers();
      updateTotals();
    }

    function recalcBalances() {
      const rows = document.querySelectorAll('#ledgerTable tr');
      let runningBalance = 0;
      rows.forEach(row => {
        const debit = parseFloat(row.cells[3].innerText) || 0;
        const credit = parseFloat(row.cells[4].innerText) || 0;
        runningBalance += credit - debit;
        row.cells[5].innerText = runningBalance.toFixed(2);
      });
      balance = runningBalance;
    }

    function editLedgerEntry(button) {
      editingRow = button.closest('tr');
      document.getElementById('ledgerDateInput').value = editingRow.cells[1].innerText;
      document.getElementById('particular').value = editingRow.cells[2].innerText;
      document.getElementById('debit').value = editingRow.cells[3].innerText || '';
      document.getElementById('credit').value = editingRow.cells[4].innerText || '';
      document.querySelector('#ledgerEntryForm button').innerText = 'Update Entry';

      // Enable/disable inputs accordingly while editing
      if (document.getElementById('debit').value.trim() !== '' && parseFloat(document.getElementById('debit').value) > 0) {
        document.getElementById('credit').disabled = true;
      } else if (document.getElementById('credit').value.trim() !== '' && parseFloat(document.getElementById('credit').value) > 0) {
        document.getElementById('debit').disabled = true;
      } else {
        document.getElementById('debit').disabled = false;
        document.getElementById('credit').disabled = false;
      }
    }

    function saveLedger() {
      // Gather customer info
      const customerName = document.getElementById('customerName').value.trim();
      const address = document.getElementById('address').value.trim();
      const contact = document.getElementById('contact').value.trim();
      if (!customerName) {
        alert("Customer name is required.");
        return;
      }
      // Gather ledger entries
      const rows = document.querySelectorAll('#ledgerTable tr');
      const ledger = [];
      rows.forEach(row => {
        ledger.push({
          date: row.cells[1].innerText,
          particular: row.cells[2].innerText,
          debit: row.cells[3].innerText,
          credit: row.cells[4].innerText,
          balance: row.cells[5].innerText
        });
      });

      let ledgers = JSON.parse(localStorage.getItem('ledgers') || '[]');
      // Check if editing an existing entry
      const params = new URLSearchParams(window.location.search);
      let editIndex = null;
      if (params.has('edit')) {
        editIndex = parseInt(params.get('edit'), 10);
      }
      if (editIndex !== null && !isNaN(editIndex) && ledgers[editIndex]) {
        ledgers[editIndex] = { customerName, address, contact, ledger };
      } else {
        ledgers.push({ customerName, address, contact, ledger });
      }
      localStorage.setItem('ledgers', JSON.stringify(ledgers));
      window.location.href = "ledgerm.php";
    }

    function cancelLedger() {
      // Always redirect to main page, do not clear localStorage
      window.location.href = "ledgerm.php";
    }

    window.onload = function() {
      // Check if editing
      const params = new URLSearchParams(window.location.search);
      if (params.has('edit')) {
        const editIndex = parseInt(params.get('edit'), 10);
        const ledgers = JSON.parse(localStorage.getItem('ledgers') || '[]');
        const entry = ledgers[editIndex];
        if (entry) {
          document.getElementById('customerName').value = entry.customerName || '';
          document.getElementById('address').value = entry.address || '';
          document.getElementById('contact').value = entry.contact || '';
          // Populate ledger table
          document.getElementById('ledgerTable').innerHTML = '';
          let runningBalance = 0;
          entry.ledger.forEach((e, i) => {
            const debit = parseFloat(e.debit) || 0;
            const credit = parseFloat(e.credit) || 0;
            runningBalance += credit - debit;
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${i + 1}</td>
              <td>${e.date}</td>
              <td>${e.particular}</td>
              <td>${e.debit}</td>
              <td>${e.credit}</td>
              <td>${runningBalance.toFixed(2)}</td>
              <td>
                <button class="delete-btn delete-btn-danger" onclick="deleteLedgerEntry(this)">Delete</button>
                <button class="delete-btn edit-btn" onclick="editLedgerEntry(this)">Edit</button>
              </td>`;
            document.getElementById('ledgerTable').appendChild(tr);
          });
          balance = runningBalance;
          updateRowNumbers();
          updateTotals();
        }
      } else {
        // Reset balance for new entry
        balance = 0;
        editingRow = null;
        document.getElementById('ledgerTable').innerHTML = '';
        updateRowNumbers();
        updateTotals();
      }
    };

    // Disable the opposite input if one has value
    document.getElementById('debit').addEventListener('input', function() {
      const creditInput = document.getElementById('credit');
      if (this.value.trim() !== '' && parseFloat(this.value) > 0) {
        creditInput.value = '';
        creditInput.disabled = true;
      } else {
        creditInput.disabled = false;
      }
    });

    document.getElementById('credit').addEventListener('input', function() {
      const debitInput = document.getElementById('debit');
      if (this.value.trim() !== '' && parseFloat(this.value) > 0) {
        debitInput.value = '';
        debitInput.disabled = true;
      } else {
        debitInput.disabled = false;
      }
    });

    // Patch: Always set year from settings and allow manual MM/DD typing only
    function enableLedgerDateInput() {
      const year = localStorage.getItem('profileFiscalYear') || '2082';
      const input = document.getElementById('ledgerDateInput');
      if (!input.value.startsWith(year)) {
        input.value = year + '/';
        setTimeout(() => {
          input.setSelectionRange(input.value.length, input.value.length);
        }, 0);
      }
      input.readOnly = false;
    }

    function formatLedgerDateInput(input) {
      const year = localStorage.getItem('profileFiscalYear') || '2082';
      let val = input.value.replace(/[^0-9]/g, '');
      // Always start with year
      val = year + val.slice(year.length);
      input.value = year;
      // Add month
      if (val.length > 4) {
        let mm = val.slice(4, 6);
        if (mm.length > 0) {
          let mmNum = parseInt(mm, 10);
          if (mmNum > 12) mm = '12';
          if (mm.length === 1 && mmNum > 1) mm = '0' + mm;
          input.value += '/' + mm;
        }
      }
      // Add day
      if (val.length > 6) {
        let dd = val.slice(6, 8);
        if (dd.length > 0) {
          let ddNum = parseInt(dd, 10);
          if (ddNum > 32) dd = '32';
          if (dd.length === 1 && ddNum > 3) dd = '0' + dd;
          input.value += '/' + dd;
        }
      }
      // Prevent editing year
      if (input.selectionStart < 4) {
        input.setSelectionRange(4, 4);
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Set year from settings on load
      const year = localStorage.getItem('profileFiscalYear') || '2082';
      const dateInput = document.getElementById('ledgerDateInput');
      if (dateInput) {
        dateInput.value = year + '/';
        dateInput.readOnly = true;
        dateInput.setSelectionRange(dateInput.value.length, dateInput.value.length);
      }

      // Update profile photo and name from localStorage
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
