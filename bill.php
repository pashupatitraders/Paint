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
    <h1 style="text-align:left;margin-top:0;margin-bottom:20px;">INVOICE</h1>
    
    <!-- Removed John Smith header -->

    <div class="row addresses">
      <div>
        <p><strong>BILL TO</strong></p>
        <p id="billToName"></p>
        <p id="billToAddress1"></p>
        <p id="billToAddress2"></p>
      </div>
      <div>
        <p><strong>INVOICE </strong> <span id="invoiceNo"></span></p>
        <p><strong>INVOICE DATE</strong> <span id="invoiceDate"></span></p>
        <p><strong>VAT NO</strong> <span id="vatNo"></span></p>
        
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>S.N</th>
          <th>DESCRIPTION</th>
          <th>SIZE</th>
          <th>UNIT PRICE</th>
          <th>QUANTITY</th>
          <th>AMOUNT</th>
        </tr>
      </thead>
      <tbody id="billItemsBody">
        <!-- Bill items will be dynamically inserted here -->
      </tbody>
    </table>

    <div class="total-section">
      <p>Subtotal: </p>
      <p>Discount %: </p>
      <p>Discount Amount: </p>
      <p>Status: <strong id="status-text"></strong></p>
      <p id="amount-paid-line" style="display:none;">Amount Paid: </p>
      <p><strong>TOTAL: </strong></p>
    </div>

    <div class="button-container">
      <button onclick="printInvoice()">Print</button>
      <button onclick="downloadPDF()">Download PDF</button>
      <button class="cancel-button" onclick="cancelAction()">Cancel</button>
    </div>
    <div style="clear: both;"></div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script>
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
            // Try to get size from item.size, or if not present, from product name (e.g., "ProductName 1 ltr")
            let sizeValue = '';
            if (item.size) {
              sizeValue = item.size;
            } else if (item.product) {
              // Try to extract size from product name if formatted like "ProductName 1 ltr"
              const sizeMatch = item.product.match(/(\d+\s*(ltr|ml|kg|gm|g|pcs|piece|pieces|packet|pack|dozen|meter|m|cm|mm|inch|ft|feet|liter|litre))/i);
              if (sizeMatch) {
                sizeValue = sizeMatch[0];
              }
            }
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${i + 1}</td>
              <td>
                ${item.product ? item.product : ''}
                ${item.category ? `<div style='font-size:12px;color:#888;'>Category: ${item.category}</div>` : ''}
              </td>
              <td>
                ${sizeValue}
              </td>
              <td>${item.sp ? item.sp : ''}</td>
              <td>${item.qty ? item.qty : ''}</td>
              <td>${item.total ? item.total : ''}</td>
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
        // Calculate subtotal: subtotal = total + (partialPaidAmount if present) + (discountAmount if present)
        let subtotal = parseFloat(entry.total) || 0;
        if (entry.partialPaidAmount) {
          subtotal += parseFloat(entry.partialPaidAmount) || 0;
        }
        if (entry.discountAmount) {
          subtotal += parseFloat(entry.discountAmount) || 0;
        }
        document.querySelector('.total-section p:nth-child(1)').innerHTML = 'Subtotal: ' + subtotal.toFixed(2);
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
    function cancelAction() {
      window.history.back();
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
    });
    // When rendering bill items, show product size below product name if available
    function renderBillItems(items) {
      const tbody = document.getElementById('billItemsBody');
      tbody.innerHTML = '';
      items.forEach((item, idx) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${idx + 1}</td>
          <td>
            ${item.product || ''}
            ${item.size ? `<div style="font-size:12px;color:#888;">${item.size}</div>` : ''}
          </td>
          <td>${item.stock || ''}</td>
          <td>${item.cp || ''}</td>
          <td>${item.sp || ''}</td>
          <td>${item.qty || ''}</td>
          <td>${item.total || ''}</td>
        `;
        tbody.appendChild(tr);
      });
    }
  </script>
</body>
</html>
