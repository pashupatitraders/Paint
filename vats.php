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
    /* Reduce width of date selection box */
    #date {
      width: 60%;
      min-width: 120px;
      max-width: 200px;
    }
    /* Increase width of Bill No box from left side */
    #billNoEntry {
      width: 180px;
      min-width: 120px;
      max-width: 250px;
      margin-left: -100px;
    }
    /* Move Bill No heading to -100px */
    label[for="billNoEntry"] {
      margin-left: -100px;
    }
    /* Increase width of Particular box to the left side */
    #particular {
      width: 370px;
      min-width: 160px;
      max-width: 350px;
      margin-left: -105px;
    }
    /* Move Particular heading to -105px */
    label[for="particular"] {
      margin-left: -105px;
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
    <h1>Vat Records</h1>

    <form id="customerInfoForm" class="input-form" onsubmit="return false;">
      <div class="input-group" style="flex-direction: row; align-items: flex-end; gap: 10px;">
        <div style="display: flex; flex-direction: column;">
          <label for="customerName">Year</label>
          <input id="customerName" type="number" inputmode="numeric" pattern="\d{4}" placeholder="Enter year" style="width:100px; min-width:80px; max-width:120px;" minlength="4" maxlength="4" title="Enter a 4-digit year" oninput="if(this.value.length>4)this.value=this.value.slice(0,4);" />
        </div>
        <div style="display: flex; flex-direction: column;">
          <label for="address">Month</label>
          <select id="address" style="padding:8px 12px; border-radius:6px; border:1px solid #ccc; font-size:14px;">
            <option value="baisakh">Baisakh</option>
            <option value="jestha">Jestha</option>
            <option value="ashadh">Ashadh</option>
            <option value="shrawan">Shrawan</option>
            <option value="bhadra">Bhadra</option>
            <option value="ashwin">Ashwin</option>
            <option value="kartik">Kartik</option>
            <option value="mangsir">Mangsir</option>
            <option value="poush">Poush</option>
            <option value="magh">Magh</option>
            <option value="falgun">Falgun</option>
            <option value="chaitra">Chaitra</option>
          </select>
        </div>
        <div style="display: flex; flex-direction: column;">
          <label for="billType">Bill Type</label>
          <select id="billType" style="padding:8px 12px; border-radius:6px; border:1px solid #ccc; font-size:14px;">
            <option value="customer">Customer</option>
            <option value="supplier">Supplier</option>
          </select>
        </div>
      </div>
    </form>

    <form id="ledgerEntryForm" class="ledger-entry-form" onsubmit="return false;">
      <div>
        <label for="date">Date</label>
        <input id="date" type="text" placeholder="YYYY-MM-DD" autocomplete="off" maxlength="10" oninput="limitDayInput(this)" />
      </div>
      <div style="max-width:120px;">
        <label for="billNoEntry">Bill No</label>
        <input id="billNoEntry" type="text" placeholder="Bill no" style="width:100%;" />
      </div>
      <div>
        <label for="particular">Particular</label>
        <input id="particular" type="text" placeholder="Description" />
      </div>
      <div>
        <label for="debit">PAN</label>
        <input id="debit" type="text" placeholder="PAN" />
      </div>
      <div>
        <label for="credit">Amount</label>
        <input id="credit" type="text" placeholder="Amount" />
      </div>
      <button onclick="addLedgerEntry()">Add Entry</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>S.N</th>
          <th>Date</th>
          <th>Bill</th>
          <th>Particular</th>
          <th>PAN</th>
          <th>Amount</th>
          <th>Vat 13%</th>
          <th>Total Amount</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="ledgerTable"></tbody>
      <tfoot>
        <tr class="footer-totals">
          <td colspan="5" style="text-align:right;">Total</td>
          <td id="totalAmountCol">0.00</td>
          <td id="totalVatCol">0.00</td>
          <td id="totalGrandCol">0.00</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
    <div style="text-align:right; font-weight:bold; margin-top:10px;">
      Total Amount: <span id="grandTotalAmount">0.00</span>
    </div>

    <div class="control-buttons">
      <button class="save-btn" onclick="saveLedger()">Save</button>
      <button class="cancel-btn" onclick="window.location.href='vat.php'">Cancel</button>
    </div>
  </main>

  <script>
    let balance = 0;
    let editingRow = null;

    // Month mapping for MM value
    const monthMap = {
      baisakh: '01',
      jestha: '02',
      ashadh: '03',
      shrawan: '04',
      bhadra: '05',
      ashwin: '06',
      kartik: '07',
      mangsir: '08',
      poush: '09',
      magh: '10',
      falgun: '11',
      chaitra: '12'
    };

    // Helper to update date prefix
    function updateDatePrefix() {
      const year = document.getElementById('customerName').value;
      const month = document.getElementById('address').value;
      const dateInput = document.getElementById('date');
      if (year.length === 4 && monthMap[month]) {
        const prefix = `${year}/${monthMap[month]}/`;
        // If user already typed a day, preserve it
        let day = '';
        if (dateInput.value.startsWith(prefix)) {
          day = dateInput.value.slice(prefix.length, prefix.length + 2);
        }
        dateInput.value = prefix + day;
        setTimeout(() => {
          // Move cursor to end
          dateInput.setSelectionRange(dateInput.value.length, dateInput.value.length);
        }, 0);
      }
    }

    // Limit day input to two digits after prefix
    function limitDayInput(input) {
      const year = document.getElementById('customerName').value;
      const month = document.getElementById('address').value;
      const prefix = (year.length === 4 && monthMap[month]) ? `${year}/${monthMap[month]}/` : '';
      if (!prefix) return;
      if (input.value.startsWith(prefix)) {
        let day = input.value.slice(prefix.length).replace(/\D/g, '').slice(0, 2);
        input.value = prefix + day;
      }
    }

    // Prevent editing/removing prefix in date input
    function protectDatePrefix(e) {
      const year = document.getElementById('customerName').value;
      const month = document.getElementById('address').value;
      const prefix = (year.length === 4 && monthMap[month]) ? `${year}/${monthMap[month]}/` : '';
      const input = e.target;
      if (!prefix) return;
      // Always restore prefix if user tries to delete it
      if (!input.value.startsWith(prefix)) {
        // Only keep the day part
        let day = input.value.replace(/\D/g, '').slice(-2);
        input.value = prefix + day;
      }
      // Prevent cursor from moving before prefix
      const minPos = prefix.length;
      if (input.selectionStart < minPos) {
        input.setSelectionRange(minPos, minPos);
      }
    }

    document.getElementById('customerName').addEventListener('input', updateDatePrefix);
    document.getElementById('address').addEventListener('change', updateDatePrefix);
    document.getElementById('date').addEventListener('input', function(e) {
      protectDatePrefix(e);
      limitDayInput(e.target);
    });
    document.getElementById('date').addEventListener('keydown', function(e) {
      const year = document.getElementById('customerName').value;
      const month = document.getElementById('address').value;
      const prefix = (year.length === 4 && monthMap[month]) ? `${year}/${monthMap[month]}/` : '';
      if (!prefix) return;
      // Prevent backspace/delete in prefix
      const pos = this.selectionStart;
      if ((e.key === 'Backspace' && pos <= prefix.length) ||
          (e.key === 'ArrowLeft' && pos <= prefix.length) ||
          (e.key === 'Delete' && pos < prefix.length)) {
        e.preventDefault();
        this.setSelectionRange(prefix.length, prefix.length);
      }
    });

    function addLedgerEntry() {
      const date = document.getElementById('date').value;
      const billNo = document.getElementById('billNoEntry').value.trim();
      const particular = document.getElementById('particular').value.trim();
      let pan = document.getElementById('debit').value.trim();
      const amountStr = document.getElementById('credit').value.trim();
      const amount = parseFloat(amountStr) || 0;

      // If PAN is empty, mark as N/A
      if (!pan) pan = "N/A";

      // Validate required fields (remove pan from required)
      if (!date || !billNo || !particular || !amountStr || isNaN(amount) || amount <= 0) {
        alert("Please fill all fields and enter a valid amount.");
        return;
      }

      // Calculate VAT and Total Amount
      const vat = (amount * 0.13).toFixed(2);
      const totalAmount = (amount + parseFloat(vat)).toFixed(2);

      if (editingRow) {
        // Update the existing row instead of adding a new one
        editingRow.cells[1].innerText = date;
        editingRow.cells[2].innerText = billNo;
        editingRow.cells[3].innerText = particular;
        editingRow.cells[4].innerText = pan;
        editingRow.cells[5].innerText = amount.toFixed(2);
        editingRow.cells[6].innerText = vat;
        editingRow.cells[7].innerText = totalAmount;
        editingRow = null;
        document.querySelector('#ledgerEntryForm button').innerText = 'Add Entry';
      } else {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td></td>
          <td>${date}</td>
          <td>${billNo}</td>
          <td>${particular}</td>
          <td>${pan}</td>
          <td>${amount.toFixed(2)}</td>
          <td>${vat}</td>
          <td>${totalAmount}</td>
          <td>
            <button class="delete-btn delete-btn-danger" onclick="deleteLedgerEntry(this)">Delete</button>
            <button class="delete-btn edit-btn" onclick="editLedgerEntry(this)">Edit</button>
          </td>`;
        document.getElementById('ledgerTable').appendChild(tr);
      }

      updateRowNumbers();
      updateTotals();

      // Clear form fields except date prefix
      const year = document.getElementById('customerName').value;
      const month = document.getElementById('address').value;
      const prefix = (year.length === 4 && monthMap[month]) ? `${year}/${monthMap[month]}/` : '';
      document.getElementById('date').value = prefix;
      document.getElementById('billNoEntry').value = '';
      document.getElementById('particular').value = '';
      document.getElementById('debit').value = '';
      document.getElementById('credit').value = '';
    }

    function updateRowNumbers() {
      const rows = document.querySelectorAll('#ledgerTable tr');
      rows.forEach((row, i) => row.cells[0].innerText = i + 1);
    }

    function updateTotals() {
      let amountTotal = 0, vatTotal = 0, totalAmountTotal = 0;
      document.querySelectorAll('#ledgerTable tr').forEach(row => {
        amountTotal += parseFloat(row.cells[5].innerText) || 0;
        vatTotal += parseFloat(row.cells[6].innerText) || 0;
        totalAmountTotal += parseFloat(row.cells[7].innerText) || 0;
      });
      // Update new total columns in tfoot
      document.getElementById('totalAmountCol').innerText = amountTotal.toFixed(2);
      document.getElementById('totalVatCol').innerText = vatTotal.toFixed(2);
      document.getElementById('totalGrandCol').innerText = totalAmountTotal.toFixed(2);
      // Remove or comment out the old total below the table if present
      // document.getElementById('grandTotalAmount').innerText = totalAmountTotal.toFixed(2);
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
      // Fill form fields with the row's data
      document.getElementById('date').value = editingRow.cells[1].innerText;
      document.getElementById('billNoEntry').value = editingRow.cells[2].innerText;
      document.getElementById('particular').value = editingRow.cells[3].innerText;
      document.getElementById('debit').value = editingRow.cells[4].innerText === "N/A" ? "" : editingRow.cells[4].innerText;
      document.getElementById('credit').value = editingRow.cells[5].innerText;
      // Re-apply date prefix if needed
      updateDatePrefix();
      // Change button text to indicate update mode
      document.querySelector('#ledgerEntryForm button').innerText = 'Update Entry';
    }

    // --- Prefill form and table if editing an existing VAT record ---
    window.addEventListener('DOMContentLoaded', function() {
      const params = new URLSearchParams(window.location.search);
      const editIdx = params.get('edit');
      if (editIdx !== null) {
        let vats = JSON.parse(localStorage.getItem('vats') || '[]');
        const vatData = vats[parseInt(editIdx)];
        if (vatData) {
          // Fill top form
          document.getElementById('customerName').value = vatData.year || '';
          document.getElementById('address').value = vatData.month || '';
          document.getElementById('billType').value = vatData.billType || '';
          updateDatePrefix();

          // Clear table
          document.getElementById('ledgerTable').innerHTML = '';

          // Fill table with entries
          (vatData.entries || []).forEach(entry => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td></td>
              <td>${entry.date}</td>
              <td>${entry.billNo}</td>
              <td>${entry.particular}</td>
              <td>${entry.pan}</td>
              <td>${entry.amount}</td>
              <td>${entry.vat}</td>
              <td>${entry.totalAmount}</td>
              <td>
                <button class="delete-btn delete-btn-danger" onclick="deleteLedgerEntry(this)">Delete</button>
                <button class="delete-btn edit-btn" onclick="editLedgerEntry(this)">Edit</button>
              </td>`;
            document.getElementById('ledgerTable').appendChild(tr);
          });
          updateRowNumbers();
          updateTotals();

          // Store the edit index for save
          window._vatEditIndex = parseInt(editIdx);
        }
      }
    });

    // --- Update saveLedger to handle update vs. add ---
    function saveLedger() {
      // Gather info from the top form
      const year = document.getElementById('customerName').value.trim();
      const month = document.getElementById('address').value.trim();
      const billType = document.getElementById('billType').value.trim();

      // Gather all table rows
      const rows = document.querySelectorAll('#ledgerTable tr');
      if (!year || !month || !billType || rows.length === 0) {
        alert("Please fill Year, Month, Bill Type and add at least one entry.");
        return;
      }

      // Collect all entries
      const entries = [];
      rows.forEach(row => {
        entries.push({
          date: row.cells[1].innerText,
          billNo: row.cells[2].innerText,
          particular: row.cells[3].innerText,
          pan: row.cells[4].innerText,
          amount: row.cells[5].innerText,
          vat: row.cells[6].innerText,
          totalAmount: row.cells[7].innerText
        });
      });

      // Prepare the object to save
      const vatData = {
        year,
        month,
        billType,
        entries
      };

      let vats = JSON.parse(localStorage.getItem('vats') || '[]');
      if (typeof window._vatEditIndex === 'number') {
        // Update existing record
        vats[window._vatEditIndex] = vatData;
      } else {
        // Add new record
        vats.push(vatData);
      }
      localStorage.setItem('vats', JSON.stringify(vats));

      alert("Saved! You will be redirected to VAT records.");
      window.location.href = "vat.php";
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
