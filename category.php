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
  <title>Category - MyInventory</title>
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
      max-width: 600px;
      margin: 0 auto;
    }
    main h1 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #1a202c;
    }

    .input-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 15px;
    }
    label {
      font-weight: 600;
      margin-bottom: 6px;
      font-size: 14px;
      color: #333;
    }
    input[type="text"] {
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      outline-offset: 2px;
      outline-color: #4fc3f7;
      transition: outline-color 0.3s;
    }
    input[type="text"]:focus {
      outline-color: #2196f3;
      border-color: #2196f3;
    }
    button, .action-btn {
      background: #4fc3f7;
      color: white;
      font-weight: 600;
      border-radius: 6px;
      border: none;
      padding: 10px 18px;
      cursor: pointer;
      font-size: 14px;
      user-select: none;
      transition: background-color 0.3s;
    }
    button:hover, .action-btn:hover {
      background: #2196f3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      box-shadow: 0 6px 12px rgba(0,0,0,0.08);
      border-radius: 10px;
      overflow: hidden;
      margin-top: 30px;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
      font-size: 14px;
    }
    th {
      background: #4fc3f7;
      color: #fff;
      font-weight: 600;
    }
    tr:hover {
      background: #f1f1f1;
    }
    .action-btn {
      padding: 6px 12px;
      font-size: 13px;
      margin-right: 8px;
      border-radius: 5px;
    }
    .edit-btn {
      background: #2196f3;
    }
    .delete-btn {
      background: #f44336;
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

  <!-- Main Content -->
  <main>
    <h1>Category Management</h1>

    <div class="input-group">
      <label for="categoryInput">Category:</label>
      <input type="text" id="categoryInput" placeholder="Enter category name" />
    </div>
    <button id="addUpdateBtn" onclick="addOrUpdateCategory()">Add Category</button>
    <button id="cancelBtn" onclick="cancelCategory()" style="margin-left:10px;">Cancel</button>

    <table>
      <thead>
        <tr>
          <th>S.N</th>
          <th>Category Type</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="categoryTable">
        <!-- Categories will appear here -->
      </tbody>
    </table>
  </main>

  <script>
    let editingRow = null;

    function getCategories() {
      return JSON.parse(localStorage.getItem('categories') || '[]');
    }

    function setCategories(categories) {
      localStorage.setItem('categories', JSON.stringify(categories));
    }

    function renderCategories() {
      const tableBody = document.getElementById('categoryTable');
      tableBody.innerHTML = '';
      const categories = getCategories();
      categories.forEach((cat, idx) => {
        const newRow = document.createElement('tr');

        // S.N cell
        const snCell = document.createElement('td');
        snCell.innerText = idx + 1;
        newRow.appendChild(snCell);

        // Category Type cell
        const categoryCell = document.createElement('td');
        categoryCell.innerText = cat;
        newRow.appendChild(categoryCell);

        // Action cell
        const actionCell = document.createElement('td');

        const editBtn = document.createElement('button');
        editBtn.className = 'action-btn edit-btn';
        editBtn.innerText = 'Edit';
        editBtn.onclick = () => editCategory(newRow);

        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'action-btn delete-btn';
        deleteBtn.innerText = 'Delete';
        deleteBtn.onclick = () => deleteCategory(newRow);

        actionCell.appendChild(editBtn);
        actionCell.appendChild(deleteBtn);

        newRow.appendChild(actionCell);

        tableBody.appendChild(newRow);
      });
    }

    function sortCategories() {
      const categories = getCategories();
      categories.sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
      setCategories(categories);
      renderCategories();
    }

    function updateSerialNumbers() {
      const rows = document.querySelectorAll('#categoryTable tr');
      rows.forEach((row, index) => {
        row.cells[0].innerText = index + 1;
      });
    }

    function addOrUpdateCategory() {
      const input = document.getElementById('categoryInput');
      const category = input.value.trim();
      if (!category) {
        alert('Please enter a category name.');
        return;
      }

      let categories = getCategories();

      if (editingRow) {
        // Update existing row
        const oldCategory = editingRow.cells[1].innerText;
        const idx = categories.indexOf(oldCategory);
        if (idx !== -1) categories[idx] = category;
        editingRow = null;
        document.getElementById('addUpdateBtn').innerText = 'Add Category';
      } else {
        // Add new category if not duplicate
        if (categories.includes(category)) {
          alert('Category already exists.');
          return;
        }
        categories.push(category);
      }

      setCategories(categories);
      input.value = '';
      input.focus();
      sortCategories();
      updateSerialNumbers();

      // No redirect here
    }

    function editCategory(row) {
      const input = document.getElementById('categoryInput');
      input.value = row.cells[1].innerText;
      editingRow = row;
      document.getElementById('addUpdateBtn').innerText = 'Update Category';
    }

    function deleteCategory(row) {
      if (confirm('Are you sure you want to delete this category?')) {
        const category = row.cells[1].innerText;
        let categories = getCategories();
        categories = categories.filter(cat => cat !== category);
        setCategories(categories);
        if (editingRow === row) {
          editingRow = null;
          document.getElementById('categoryInput').value = '';
          document.getElementById('addUpdateBtn').innerText = 'Add Category';
        }
        sortCategories();
        updateSerialNumbers();
      }
    }

    function cancelCategory() {
      // Redirect to product.php without saving
      window.location.href = 'product.php';
    }

    // On page load, render categories from localStorage
    window.onload = function() {
      renderCategories();
    };

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
