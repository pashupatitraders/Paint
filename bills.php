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
  <title>Ledger</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      background: #fff;
      color: #333;
    }

    .container {
      width: 800px;
      margin: auto;
      padding: 40px;
      position: relative;
    }

    .background {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background: linear-gradient(120deg, #cde3d3 0%, #bcd7e3 40%, #f2f2f2 80%);
      z-index: -1;
      opacity: 0.3;
    }

    .header-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    h1 {
      color: #1e4e79;
      margin: 0;
      font-size: 32px;
    }

    .vat-no {
      font-weight: bold;
      font-size: 16px;
      color: #1e4e79;
    }

    .addresses {
      margin-bottom: 20px;
    }

    .addresses p {
      margin: 2px 0;
    }

    .row {
      display: flex;
      justify-content: space-between;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }

    .table th, .table td {
      border-bottom: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    .table th {
      color: #1e4e79;
      font-weight: bold;
      font-size: 16px;
    }

    tfoot td {
      font-weight: bold;
      color: #1e4e79;
      border-top: 2px solid #1e4e79;
    }

    .button-container {
      margin-top: 30px;
      text-align: center;
    }

    button {
      padding: 10px 20px;
      margin: 5px;
      font-size: 16px;
      background-color: #1e4e79;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #163b5c;
    }

    .cancel-button {
      background-color: #aaa;
    }

    .cancel-button:hover {
      background-color: #888;
    }

    @media print {
      .button-container {
        display: none !important;
      }
    }
  </style>
</head>
<body>
  <div class="container" id="ledger">
    <div class="background"></div>

    <div class="profile-info" style="text-align:center;margin-bottom:10px;">
      <div style="display:flex;align-items:center;justify-content:center;">
        <div class="logo-circle" id="companyLogo" style="width:100px;height:100px;border-radius:50%;border:2px solid #ccc;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fff;margin-right:24px;">
          <img id="companyLogoImg" src="" alt="Company Logo" style="max-width:90%;max-height:90%;object-fit:contain;" />
        </div>
        <div style="flex:1;">
          <div id="profileNameDisplay" style="font-size:35.2px;font-weight:700;color:#1e4e79;line-height:1.1;"></div>
          <div id="profileAddressDisplay" style="font-size:16px;color:#1e4e79;margin-top:2px;"></div>
          <div id="profileContactDisplay" style="font-size:16px;color:#1e4e79;margin-top:2px;"></div>
        </div>
      </div>
    </div>

    <div class="header-row">
      <h1>LEDGER</h1>
      <div class="vat-no">VAT NO: 2412/2019</div>
    </div>

    <div class="row addresses" id="customerInfoBox">
      <div>
        <p id="customerName"></p>
        <p id="customerAddress"></p>
        <p id="customerContact"></p>
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>DATE</th>
          <th>DESCRIPTION</th>
          <th>DEBIT</th>
          <th>CREDIT</th>
          <th>AMOUNT</th>
        </tr>
      </thead>
      <tbody id="ledgerEntriesBody">
        <!-- Ledger entries will be dynamically inserted here -->
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2">Total</td>
          <td id="totalDebit"></td>
          <td id="totalCredit"></td>
          <td id="totalAmount"></td>
        </tr>
      </tfoot>
    </table>

    <div class="button-container">
      <button onclick="window.print()">Print</button>
      <button onclick="downloadPDF()">Download PDF</button>
      <button class="cancel-button" onclick="cancelAction()">Cancel</button>
    </div>
  </div>

  <!-- JS Libraries -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
    async function downloadPDF() {
      const buttonContainer = document.querySelector('.button-container');
      if (buttonContainer) buttonContainer.style.display = 'none';
      const { jsPDF } = window.jspdf;
      const element = document.getElementById("ledger");
      const canvas = await html2canvas(element, { scale: 2 });
      const imgData = canvas.toDataURL("image/png");
      const pdf = new jsPDF("p", "pt", "a4");
      const pageWidth = pdf.internal.pageSize.getWidth();
      const ratio = canvas.width / canvas.height;
      const pdfHeight = pageWidth / ratio;
      pdf.addImage(imgData, "PNG", 0, 0, pageWidth, pdfHeight);
      pdf.save("ledger.pdf");
      if (buttonContainer) buttonContainer.style.display = '';
    }

    function cancelAction() {
      window.history.back();
    }

    // Helper to parse YYYY/MM/DD as a comparable number
    function parseNepaliDate(str) {
      // Expects format: YYYY/MM/DD
      if (!str) return 0;
      const parts = str.split('/');
      if (parts.length !== 3) return 0;
      const y = parseInt(parts[0], 10) || 0;
      const m = parseInt(parts[1], 10) || 0;
      const d = parseInt(parts[2], 10) || 0;
      return y * 10000 + m * 100 + d;
    }

    // Ledger data population from localStorage
    function getQueryParam(name) {
      const url = new URL(window.location.href);
      return url.searchParams.get(name);
    }
    const ledgerIdx = getQueryParam('ledger');
    if (ledgerIdx !== null) {
      const ledgers = JSON.parse(localStorage.getItem('ledgers') || '[]');
      const entry = ledgers[ledgerIdx];
      if (entry) {
        document.getElementById('customerName').innerText = entry.customerName || '';
        document.getElementById('customerAddress').innerText = entry.address || '';
        document.getElementById('customerContact').innerText = entry.contact || '';
        // Fill ledger entries in ascending date order
        const tbody = document.getElementById('ledgerEntriesBody');
        let totalDebit = 0, totalCredit = 0, totalAmount = 0;
        if (Array.isArray(entry.ledger)) {
          // Sort by date ascending
          const sortedLedger = entry.ledger.slice().sort((a, b) => parseNepaliDate(a.date) - parseNepaliDate(b.date));
          sortedLedger.forEach((row) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${row.date || ''}</td>
              <td>${row.particular || ''}</td>
              <td>${row.debit || ''}</td>
              <td>${row.credit || ''}</td>
              <td>${row.balance || ''}</td>
            `;
            tbody.appendChild(tr);
            totalDebit += Number(row.debit) || 0;
            totalCredit += Number(row.credit) || 0;
            totalAmount = row.balance || totalAmount;
          });
        }
        document.getElementById('totalDebit').innerText = totalDebit ? totalDebit : '';
        document.getElementById('totalCredit').innerText = totalCredit ? totalCredit : '';
        document.getElementById('totalAmount').innerText = totalAmount ? totalAmount : '';
      }
    }

    window.addEventListener('DOMContentLoaded', function() {
      // Show company logo from localStorage if available
      const profilePhoto = localStorage.getItem('profilePhoto');
      if (profilePhoto) {
        document.getElementById('companyLogoImg').src = profilePhoto;
      } else {
        document.getElementById('companyLogoImg').src = 'https://via.placeholder.com/100?text=Logo';
      }
      // Show profile info at top
      document.getElementById('profileNameDisplay').innerText = localStorage.getItem('profileName') || '';
      document.getElementById('profileAddressDisplay').innerText = localStorage.getItem('profileAddress') || '';
      document.getElementById('profileContactDisplay').innerText = localStorage.getItem('profileContact') || '';
      // Show VAT number from localStorage if available
      var vat = localStorage.getItem('profileVatNo') || '';
      document.querySelector('.vat-no').innerText = 'VAT NO: ' + vat;
    });
  </script>
</body>
</html>
