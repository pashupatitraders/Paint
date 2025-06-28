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
  <title>Invoice</title>
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

    h1 {
      color: #1e4e79;
      margin-bottom: 20px;
    }

    .header, .addresses, .invoice-details {
      margin-bottom: 20px;
    }

    .header p, .addresses p, .invoice-details p {
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
    }

    .right {
      text-align: right;
    }

    .total-section {
      float: right;
      margin-top: 10px;
      font-size: 16px;
    }

    .total-section p {
      margin: 5px 0;
    }

    .button-container {
      margin-top: 20px;
      text-align: right;
    }

    .button-container button {
      background-color: #1e4e79;
      color: white;
      border: none;
      padding: 10px 20px;
      margin-left: 10px;
      cursor: pointer;
      font-size: 16px;
    }

    .button-container button.cancel-button {
      background-color: #f44336;
    }

    @media print {
      .button-container {
        display: none !important;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="background"></div>

    <div style="display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
      <div class="logo-circle" id="companyLogo" style="width:100px;height:100px;border-radius:50%;border:2px solid #ccc;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fff;margin-right:24px;">
        <img id="companyLogoImg" src="" alt="Company Logo" style="max-width:90%;max-height:90%;object-fit:contain;" />
      </div>
      <div class="profile-info" style="text-align:center;flex:1;">
        <div id="profileNameDisplay" style="font-size:35.2px;font-weight:700;color:#1e4e79;line-height:1.1;"></div>
        <div id="profileAddressDisplay" style="font-size:16px;color:#1e4e79;margin-top:2px;"></div>
        <div id="profileContactDisplay" style="font-size:16px;color:#1e4e79;margin-top:2px;"></div>
      </div>
    </div>
    <h1 style="text-align:left;margin-top:0;margin-bottom:20px;">VAT BILL</h1>
    
    <!-- Removed John Smith header -->

    <div class="row addresses">
      <div>
        <p><strong>BILL</strong></p>
        <p id="billToYear"></p>
        <p id="billToMonth"></p>
        <p id="billToType"></p>
      </div>
      <div>
        <p><strong>BILL DATE</strong> <span id="invoiceDate"></span></p>
        <p><strong>VAT NO</strong> <span id="vatNo"></span></p>
        
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>S.N</th>
          <th>DATE</th>
          <th>BILL NO</th>
          <th>PARTICULAR</th> <!-- New column -->
          <th>AMOUNT</th>
          <th>VAT 13%</th>
          <th>TOTAL AMOUNT</th>
        </tr>
      </thead>
      <tbody>
        <!-- Invoice entries will be dynamically inserted here -->
      </tbody>
    </table>


    <div class="button-container">
      <button onclick="printInvoice()">Print</button>
      <button onclick="downloadPDF()">Download PDF</button>
      <button class="cancel-button" onclick="window.location.href='vat.php'">Cancel</button>
    </div>
    <div style="clear: both;"></div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
    // Helper to get query param
    function getQueryParam(name) {
      const url = new URL(window.location.href);
      return url.searchParams.get(name);
    }

    // --- VAT BILL LOGIC ---
    const vatIdx = getQueryParam('vat');
    if (vatIdx !== null) {
      const vats = JSON.parse(localStorage.getItem('vats') || '[]');
      const entry = vats[parseInt(vatIdx, 10)];
      if (entry) {
        // Fill bill info
        document.getElementById('billToYear').innerText = "Year: " + (entry.year || '');
        document.getElementById('billToMonth').innerText = "Month: " + (entry.month || '');
        document.getElementById('billToType').innerText = "Type: " + (entry.billType || '');
        document.getElementById('invoiceDate').innerText = entry.entries && entry.entries[0] ? entry.entries[0].date : '';
        // VAT No is already set from localStorage

        // Fill table
        const tbody = document.querySelector('.table tbody');
        tbody.innerHTML = '';
        let totalAmount = 0, totalVat = 0, totalGrand = 0;
        if (Array.isArray(entry.entries)) {
          entry.entries.forEach((row, i) => {
            totalAmount += parseFloat(row.amount) || 0;
            totalVat += parseFloat(row.vat) || 0;
            totalGrand += parseFloat(row.totalAmount) || 0;
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${i + 1}</td>
              <td>${row.date || ''}</td>
              <td>${row.billNo || ''}</td>
              <td>${row.particular || ''}</td>
              <td>${row.amount || ''}</td>
              <td>${row.vat || ''}</td>
              <td>${row.totalAmount || ''}</td>
            `;
            tbody.appendChild(tr);
          });
          // Add total row
          const trTotal = document.createElement('tr');
          trTotal.innerHTML = `
            <td colspan="4" style="text-align:right;font-weight:bold;">Total</td>
            <td style="font-weight:bold;">${totalAmount.toFixed(2)}</td>
            <td style="font-weight:bold;">${totalVat.toFixed(2)}</td>
            <td style="font-weight:bold;">${totalGrand.toFixed(2)}</td>
          `;
          tbody.appendChild(trTotal);
        }
      }
    }

    // Show Amount Paid only if status is "PARTIAL PAID" (case insensitive)
    function getQueryParam(name) {
      const url = new URL(window.location.href);
      return url.searchParams.get(name);
    }
    const saleIdx = getQueryParam('sale');
    if (saleIdx !== null) {
      const sales = JSON.parse(localStorage.getItem('salesData') || '[]');
      const entry = sales[saleIdx];
      if (entry) {
        document.getElementById('billToName').innerText = entry.customer || '';
        // Split address into two lines if possible
        if (entry.address) {
          const addressLines = entry.address.split(',');
          document.getElementById('billToAddress1').innerText = addressLines[0] || '';
          document.getElementById('billToAddress2').innerText = addressLines.slice(1).join(',').trim();
        }
        // Fill invoice table
        const tbody = document.querySelector('.table tbody');
        tbody.innerHTML = '';
        if (Array.isArray(entry.items)) {
          entry.items.forEach((item, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${i + 1}</td>
              <td>${item.product || ''}</td>
              <td>${item.sp || ''}</td>
              <td>${item.qty || ''}</td>
              <td>${item.total || ''}</td>
            `;
            tbody.appendChild(tr);
          });
        }
        // Fill invoice date
        document.getElementById('invoiceDate').innerText = entry.date || '';
        // Set invoice number as 3-digit, starting from 001
        const invoiceNo = (parseInt(saleIdx, 10) + 1).toString().padStart(3, '0');
        document.getElementById('invoiceNo').innerText = invoiceNo;
        // Fill totals and status
        document.querySelector('.total-section p:nth-child(1)').innerHTML = 'Subtotal: ' + (entry.total ? entry.total : '');
        document.querySelector('.total-section p:nth-child(2)').innerHTML = 'Discount %: ' + (entry.discountPercent ? entry.discountPercent : '');
        document.querySelector('.total-section p:nth-child(3)').innerHTML = 'Discount Amount: ' + (entry.discountAmount ? entry.discountAmount : '');
        document.getElementById('status-text').innerText = entry.paidStatus || '';
        if (entry.paidStatus && entry.paidStatus.toLowerCase() === 'partial paid') {
          document.getElementById('amount-paid-line').style.display = 'block';
          document.getElementById('amount-paid-line').innerText = 'Amount Paid: ' + (entry.partialPaidAmount ? entry.partialPaidAmount : '');
        } else {
          document.getElementById('amount-paid-line').style.display = 'none';
        }
        document.querySelector('.total-section p:last-child').innerHTML = '<strong>TOTAL: ' + (entry.total ? entry.total : '') + '</strong>';
      }
    }
    function printInvoice() {
      document.querySelector('.button-container').style.display = 'none';
      window.print();
      setTimeout(() => {
        document.querySelector('.button-container').style.display = '';
      }, 500);
    }
    async function downloadPDF() {
      const buttonContainer = document.querySelector('.button-container');
      if (buttonContainer) buttonContainer.style.display = 'none';
      const { jsPDF } = window.jspdf;
      const element = document.querySelector('.container');
      const canvas = await html2canvas(element, { scale: 2 });
      const imgData = canvas.toDataURL("image/png");
      const pdf = new jsPDF("p", "pt", "a4");
      const pageWidth = pdf.internal.pageSize.getWidth();
      const ratio = canvas.width / canvas.height;
      const pdfHeight = pageWidth / ratio;
      pdf.addImage(imgData, "PNG", 0, 0, pageWidth, pdfHeight);
      pdf.save("invoice.pdf");
      if (buttonContainer) buttonContainer.style.display = '';
    }
    // Show company logo from localStorage if available
    window.addEventListener('DOMContentLoaded', function() {
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
      document.getElementById('vatNo').innerText = vat;
      // Set bill date at right to today
      const today = new Date();
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0');
      const dd = String(today.getDate()).padStart(2, '0');
      const todayStr = `${yyyy}-${mm}-${dd}`;
      const billDateElem = document.getElementById('billDate');
      if (billDateElem) {
        billDateElem.innerText = todayStr;
      }
    });
  </script>
</body>
</html>
  </script>
</body>
</html>
  </script>
</body>
</html>
