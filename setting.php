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
    .form-row {
      display: flex;
      gap: 8px;
      margin-bottom: 20px;
      align-items: center;
    }
    .form-row > div {
      flex: 1;
    }
    .button {
      cursor: pointer;
      border: none;
      border-radius: 6px;
      padding: 10px 20px;
      font-weight: 600;
      font-size: 14px;
    }
    .button.save {
      background: #4caf50;
      color: #fff;
      margin-right: 10px;
    }
    .button.cancel {
      background: #f44336;
      color: #fff;
    }
    /* Logo circle container */
    .logo-circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      border: 2px solid #ccc;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      margin-bottom: 6px;
    }
    .logo-circle img {
      max-width: 90%;
      max-height: 90%;
      object-fit: contain;
    }
    .logo-vat-container {
      display: flex;
      align-items: flex-start;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 25px;
    }
    .logo-upload-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex-shrink: 0;
    }
    /* Hide default file input */
    #logoInput {
      display: none;
    }
    /* Custom browse button */
    .browse-btn {
      background: #4fc3f7;
      color: white;
      border: none;
      padding: 6px 14px;
      font-weight: 600;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      margin-top: 6px;
    }
    .browse-btn:hover {
      background: #3bb0e0;
    }
    .vat-currency-section {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .vat-currency-section > div {
      width: 100%;
    }
  </style>
</head>
<body>
  <h1>Profile</h1>

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
      <input type="text" placeholder="Enter contact number" style="width:97%;" />
    </div>
  </div>

  <!-- Logo circle and VAT No + Currency stacked -->
  <div class="logo-vat-container">
    <div class="logo-upload-container">
      <div class="logo-circle" id="logoCircle">
        <img src="https://via.placeholder.com/100?text=Logo" alt="Company Logo" id="logoImage" />
      </div>
      <label class="browse-btn" for="logoInput">Browse File</label>
      <input type="file" id="logoInput" accept="image/*" />
    </div>

    <div class="vat-currency-section">
      <div style="display: flex; gap: 16px;">
        <div style="flex:1;">
          <label for="vatNo">VAT No:</label>
          <input type="text" id="vatNo" placeholder="Enter VAT number" />
        </div>
        <div style="flex:1; position: relative; left: -370px;">
          <label for="fiscalYear">Year:</label>
          <input id="yearInput" type="text" maxlength="4" pattern="\d{4}" placeholder="Year" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,4)" />
        </div>
      </div>
    </div>
  </div>

  <!-- Save and Cancel Buttons -->
  <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
    <button class="button save" id="saveBtn">Save</button>
    <button class="button cancel" onclick="window.location.href='dashboard.php'">Cancel</button>
  </div>

  <!-- When you click the Save button in the setting page: -->
<!-- 1. The form fields (such as profile name, address, contact, VAT number, fiscal year, and optionally profile photo) are read. -->
<!-- 2. Their values are saved to localStorage under keys like 'profileName', 'profileAddress', 'profileContact', 'profileVatNo', 'profileFiscalYear', etc. -->
<!-- 3. The page may show a success message or redirect, depending on your implementation. -->
<!-- 4. Other pages (like ledger, bills, etc.) will use these values from localStorage for display and logic. -->

  <script>
    const logoInput = document.getElementById('logoInput');
    const logoImage = document.getElementById('logoImage');
    let tempImageData = null;
    logoInput.addEventListener('change', (event) => {
      const file = event.target.files[0];
      if (!file) return;
      // Check file size (5MB max)
      const maxSize = 5 * 1024 * 1024; // 5 MB
      if (file.size > maxSize) {
        alert('Image is too large. Please select an image less than 5MB.');
        logoInput.value = '';
        return;
      }
      const reader = new FileReader();
      reader.onload = function(e) {
        tempImageData = e.target.result;
        logoImage.src = tempImageData;
        document.getElementById('logoCircle').querySelector('img').src = tempImageData;
      };
      reader.readAsDataURL(file);
    });

    // Prefill form fields from localStorage if available
    window.addEventListener('DOMContentLoaded', function() {
      const profileName = localStorage.getItem('profileName');
      if (profileName) {
        document.querySelector('input[placeholder="Enter name"]').value = profileName;
      }
      const address = localStorage.getItem('profileAddress');
      if (address) {
        document.querySelector('input[placeholder="Enter address"]').value = address;
      }
      const contact = localStorage.getItem('profileContact');
      if (contact) {
        document.querySelector('input[placeholder="Enter contact number"]').value = contact;
      }
      const vatNo = localStorage.getItem('profileVatNo');
      if (vatNo) {
        document.getElementById('vatNo').value = vatNo;
      }
      const fiscalYear = localStorage.getItem('profileFiscalYear');
      if (fiscalYear) {
        document.getElementById('yearInput').value = fiscalYear;
      }
      const profilePhoto = localStorage.getItem('profilePhoto');
      if (profilePhoto) {
        document.getElementById('logoImage').src = profilePhoto;
        document.getElementById('logoCircle').querySelector('img').src = profilePhoto;
      }
      // Attach save event to button to ensure it always works
      document.getElementById('saveBtn').onclick = function(e) {
        e.preventDefault();
        saveInvoice();
      };
    });

    function saveInvoice() {
      const customerName = document.querySelector('input[placeholder="Enter name"]').value;
      const address = document.querySelector('input[placeholder="Enter address"]').value;
      const contact = document.querySelector('input[placeholder="Enter contact number"]').value;
      const vatNo = document.getElementById('vatNo').value;
      const fiscalYear = document.getElementById('yearInput').value;
      // Save all fields to localStorage
      localStorage.setItem('profileName', customerName);
      localStorage.setItem('profileAddress', address);
      localStorage.setItem('profileContact', contact);
      localStorage.setItem('profileVatNo', vatNo);
      localStorage.setItem('profileFiscalYear', fiscalYear);
      // Save profile photo to localStorage if uploaded and only on Save
      if (tempImageData && !logoImage.src.includes('placeholder.com')) {
        localStorage.setItem('profilePhoto', tempImageData);
      } else if (logoImage.src && !logoImage.src.includes('placeholder.com')) {
        // If already set from previous session
        localStorage.setItem('profilePhoto', logoImage.src);
      }
      if (!customerName || !address) {
        alert('Please fill all required fields');
        return;
      }
      setTimeout(function() {
        window.location.href = 'dashboard.php';
      }, 50);
    }

    // Optionally, validate on form submit or blur
    document.getElementById('yearInput').addEventListener('blur', function() {
      if (this.value.length !== 4) {
        alert('Year must be a 4-digit number.');
        this.focus();
      }
    });
  </script>

</body>
</html>
