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
  <title>Dashboard - MyInventory</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: #f5f7fa;
      color: #222;
    }
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
    .user-profile {
      display: flex;
      align-items: center;
      cursor: pointer;
    }
    .user-profile img {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      margin-right: 12px;
    }
    .user-profile span {
      font-size: 16px;
      color: #1e2a38;
    }
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
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-bottom: 40px;
    }
    .card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .card i {
      font-size: 22px;
      margin-bottom: 8px;
      color: #4fc3f7;
    }
    .card h3 {
      margin: 0;
      font-size: 16px;
      color: #888;
    }
    .card p {
      font-size: 22px;
      font-weight: bold;
      margin: 5px 0 0;
      color: #1e2a38;
    }
    .card.low-stock-blink {
      animation: blink-red 1s linear infinite;
      border: 2px solid #f44336;
      box-shadow: 0 0 16px 2px #f44336;
    }
    @keyframes blink-red {
      0%, 100% { box-shadow: 0 0 16px 2px #f44336; background: #fff; }
      50% { box-shadow: 0 0 32px 8px #ffcdd2; background: #ffeaea; }
    }
    .pending-blink {
      border: 2px solid #ff9800;
      box-shadow: 0 0 16px 2px #ff9800;
      animation: blink-orange 1s linear infinite;
    }
    @keyframes blink-orange {
      0%, 100% { box-shadow: 0 0 16px 2px #ff9800; }
      50% { box-shadow: 0 0 32px 8px #ffe0b2; }
    }
    .blue-blink {
      border: 2px solid #2196f3;
      box-shadow: 0 0 16px 2px #90caf9;
      animation: blink-blue 1s linear infinite;
    }
    @keyframes blink-blue {
      0%, 100% { box-shadow: 0 0 16px 2px #90caf9; }
      50% { box-shadow: 0 0 32px 8px #e3f2fd; }
    }
    .charts {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 30px;
    }
    .chart-box {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .chart-box h3 {
      margin-bottom: 15px;
      font-size: 18px;
      color: #1e2a38;
    }
    canvas {
      width: 100%;
      height: 300px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table th, table td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    table th {
      background: #f0f0f0;
      color: #333;
    }
    .placeholder-card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      min-height: 100px;
      border: none;
    }
  </style>
</head>
<body>

  <aside class="sidebar">
    <div class="logo">MyInventory</div>
    <nav>
      <a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="product.php"><i class="fas fa-box-open"></i> Products</a>
      <a href="sale.php"><i class="fas fa-shopping-cart"></i> Sales</a>
      <a href="ledgerm.php"><i class="fas fa-file-invoice-dollar"></i> Ledger</a>
      <a href="vat.php" id="vatMenuBtn"><i class="fas fa-file-invoice-dollar"></i>Vat</a>
      <a href="#" id="settingMenuBtn"><i class="fas fa-cog"></i> Setting</a>
    </nav>
  </aside>

  <header>
    <div class="user-profile">
      <img src="https://via.placeholder.com/40?text=Logo" alt="User" id="profilePhotoImg" style="width:36px;height:36px;border-radius:50%;margin-right:12px;">
      <span id="profileNameSpan"></span>
    </div>
  </header>

  <main>
    <h1>Dashboard</h1>
    <div class="dashboard-cards">
      <div class="card">
        <i class="fas fa-eye"></i>
        <h3>Total Sales Amount</h3>
        <p id="totalSalesAmount">0</p>
      </div>
      <div class="card">
        <i class="fas fa-box"></i>
        <h3>Total Products</h3>
        <p id="totalProductsCount">0</p>
      </div>
      <div class="card">
        <i class="fas fa-users"></i>
        <h3>Total Stock Value</h3>
        <p id="totalStockValue">0</p>
      </div>
      <div class="card" id="lowStockCard">
        <i class="fas fa-exclamation-triangle"></i>
        <h3>Low Stock Items</h3>
        <p id="lowStockCount">0</p>
      </div>
      <div class="card" id="nepalTimeCard">
        <i class="fas fa-clock"></i>
        <h3>Nepal Time</h3>
        <p id="nepalTime">--:--:-- <span id="nepaliDate" style="font-size:14px;color:#1976d2;margin-left:12px;"></span></p>
      </div>
      <div class="card">
        <i class="fas fa-money-bill-wave"></i>
        <h3>Pending Payments</h3>
        <p id="pendingPayments">0</p>
      </div>
      <div class="card" id="totalProfitCard">
        <i class="fas fa-chart-line"></i>
        <h3>Total Profit</h3>
        <p id="totalProfit">0</p>
      </div>
      <div class="card" id="todaySalesCard">
        <i class="fas fa-calendar-day"></i>
        <h3>Today's Sales</h3>
        <p id="todaySales">0</p>
      </div>
    </div>

    <div class="charts">
      <div class="chart-box">
        <h3>Total Revenue vs Sales</h3>
        <canvas id="lineChart"></canvas>
      </div>
      <div class="chart-box">
        <h3>Profit This Week</h3>
        <canvas id="barChart"></canvas>
      </div>
      <div class="chart-box">
        <h3>Low Stock Products</h3>
        <table>
          <thead>
            <tr><th>Product</th><th>Quantity</th></tr>
          </thead>
          <tbody id="lowStockTable"></tbody>
        </table>
      </div>
      <div class="chart-box">
        <h3>Recent Sales</h3>
        <table>
          <thead>
            <tr><th>Date</th><th>Customer</th><th>Total</th></tr>
          </thead>
          <tbody id="recentSalesTable"></tbody>
        </table>
      </div>
    </div>
  </main>

  <script>
    // Always reload salesData from localStorage on page load
    window.addEventListener('pageshow', function(event) {
      if (event.persisted) {
        window.location.reload();
      }
    });

    const lineChart = new Chart(document.getElementById('lineChart'), {
      type: 'line',
      data: { labels: [], datasets: [] },
      options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });

    const barChart = new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: { labels: [], datasets: [{ label: 'Sales', data: [], backgroundColor: '#1976d2' }, { label: 'Revenue', data: [], backgroundColor: '#4fc3f7' }] },
      options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
    });

    window.addEventListener('DOMContentLoaded', () => {
      const products = JSON.parse(localStorage.getItem('products') || '[]');
      const sales = JSON.parse(localStorage.getItem('salesData') || '[]');

      // Calculate total products from all products (not linked to sales/profit)
      document.getElementById('totalProductsCount').innerText = products.length;

      let totalStockValue = 0, lowStock = [];
      products.forEach(p => {
        const total = parseFloat(p.total) || 0;
        totalStockValue += total;
        // Compare with lowStock value if present, else default to 5
        const lowStockVal = (typeof p.lowStock === 'number' ? p.lowStock : parseInt(p.lowStock)) || 5;
        if ((parseInt(p.quantity) || 0) <= lowStockVal) {
          lowStock.push(p);
        }
      });
      document.getElementById('totalStockValue').innerText = totalStockValue.toFixed(2);
      document.getElementById('lowStockCount').innerText = lowStock.length;
      document.getElementById('lowStockTable').innerHTML = lowStock.map((p, idx) =>
        `<tr class="low-stock-row" data-product-index="${products.indexOf(p)}" style="cursor:pointer;">
          <td>${p.name}${p.size ? ' (' + p.size + ')' : ''}</td><td>${p.quantity}</td>
        </tr>`
      ).join('');
      // Animate low stock card if any low stock
      const lowStockCard = document.getElementById('lowStockCard');
      if (lowStock.length > 0) {
        lowStockCard.classList.add('low-stock-blink');
        lowStockCard.classList.remove('blue-blink');
      } else {
        lowStockCard.classList.remove('low-stock-blink');
        lowStockCard.classList.add('blue-blink');
      }

      // Add click event to low stock rows to redirect to stock page with product index
      document.querySelectorAll('.low-stock-row').forEach(row => {
        row.addEventListener('click', function() {
          const idx = this.getAttribute('data-product-index');
          if (idx !== null) {
            window.location.href = `stock.php?show=${idx}`;
          }
        });
      });

      // Remove all graph data dependencies from pending payments and total sales calculations
      let totalSales = 0, totalRevenue = 0, pending = 0;
      // Only calculate pending payments and total sales for the cards, not for the graph
      sales.forEach(s => {
        const paidStatus = (s.paidStatus || '').trim().toLowerCase();
        totalSales += parseFloat(s.total) || 0;
        if (paidStatus === 'not paid' || paidStatus === 'partial paid') {
          if (paidStatus === 'not paid') {
            pending += parseFloat(s.total) || 0;
          } else if (paidStatus === 'partial paid') {
            if (s.due !== undefined) {
              pending += parseFloat(s.due) || 0;
            } else if (s.total !== undefined && s.partialPaidAmount !== undefined) {
              pending += Math.max(0, (parseFloat(s.total) || 0) - (parseFloat(s.partialPaidAmount) || 0));
            }
          }
        }
      });
      document.getElementById('totalSalesAmount').innerText = totalSales.toFixed(2);
      document.getElementById('pendingPayments').innerText = pending.toFixed(2);

      // Graph data is now calculated independently
      const salesPerMonth = Array(12).fill(0), revenuePerMonth = Array(12).fill(0);
      const now = new Date();
      const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
      const salesPerDay = Array(7).fill(0), revenuePerDay = Array(7).fill(0);
      sales.forEach(s => {
        const d = new Date(s.date);
        const total = parseFloat(s.total) || 0;
        const discount = parseFloat(s.discountAmount) || 0;
        const grand = Math.max(0, total - discount);
        salesPerMonth[d.getMonth()] += 1;
        revenuePerMonth[d.getMonth()] += grand;
        if (d >= startOfWeek) {
          salesPerDay[d.getDay()] += 1;
          revenuePerDay[d.getDay()] += grand;
        }
      });

      lineChart.data.labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      lineChart.data.datasets = [
        { label: 'Revenue', data: revenuePerMonth, borderColor: '#4fc3f7', backgroundColor: 'rgba(79,195,247,0.2)', fill: true, tension: 0.4 },
        { label: 'Sales', data: salesPerMonth, borderColor: '#1976d2', backgroundColor: 'rgba(25,118,210,0.2)', fill: true, tension: 0.4 }
      ];
      lineChart.update();

      barChart.data.labels = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
      barChart.data.datasets[0].data = [salesPerDay[1],salesPerDay[2],salesPerDay[3],salesPerDay[4],salesPerDay[5],salesPerDay[6],salesPerDay[0]];
      barChart.data.datasets[1].data = [revenuePerDay[1],revenuePerDay[2],revenuePerDay[3],revenuePerDay[4],revenuePerDay[5],revenuePerDay[6],revenuePerDay[0]];
      barChart.update();

      // Recent Sales Table
      const recentSales = sales.slice(-5).reverse();
      document.getElementById('recentSalesTable').innerHTML = recentSales.map((sale, i) =>
        `<tr class="recent-sale-row" data-sale-index="${sales.length - 1 - i}" style="cursor:pointer;">
          <td>${sale.date || ''}</td><td>${sale.customer || ''}</td><td>${sale.total || ''}</td>
        </tr>`
      ).join('');
      // Add click event to recent sales rows to redirect to sale page with correct index
      document.querySelectorAll('.recent-sale-row').forEach(row => {
        row.addEventListener('click', function() {
          const idx = this.getAttribute('data-sale-index');
          if (idx !== null) {
            localStorage.setItem('showOnlySaleIdx', idx);
            window.location.href = `sale.php?sale=${idx}`;
          }
        });
      });

      // Calculate total profit for paid status: Profit = (SP - CP) * Qty for all items in all paid sales
      // For partial paid, only count the paid portion (partialPaidAmount)
      let totalProfit = 0;
      sales.forEach(sale => {
        const paidStatus = (sale.paidStatus || '').trim().toLowerCase();
        if ((paidStatus === 'paid' || paidStatus === 'partial paid') && sale.items && Array.isArray(sale.items)) {
          let saleProfit = 0;
          let saleTotal = 0;
          sale.items.forEach(item => {
            let cp = 0, sp = 0, qty = 0;
            if (item.cp !== undefined && !isNaN(parseFloat(item.cp))) cp = parseFloat(item.cp);
            else if (item.purchasePrice !== undefined && !isNaN(parseFloat(item.purchasePrice))) cp = parseFloat(item.purchasePrice);
            else if (item.cost !== undefined && !isNaN(parseFloat(item.cost))) cp = parseFloat(item.cost);

            if (item.sp !== undefined && !isNaN(parseFloat(item.sp))) sp = parseFloat(item.sp);
            else if (item.sell !== undefined && !isNaN(parseFloat(item.sell))) sp = parseFloat(item.sell);
            else if (item.price !== undefined && !isNaN(parseFloat(item.price))) sp = parseFloat(item.price);
            else if (item.sellingPrice !== undefined && !isNaN(parseFloat(item.sellingPrice))) sp = parseFloat(item.sellingPrice);

            if (item.qty !== undefined && !isNaN(parseFloat(item.qty))) qty = parseFloat(item.qty);

            if (!isNaN(cp) && !isNaN(sp) && !isNaN(qty)) {
              saleProfit += (sp - cp) * qty;
              saleTotal += sp * qty;
            }
          });
          if (paidStatus === 'partial paid') {
            // Only count profit for the partial paid amount
            let paidAmount = parseFloat(sale.partialPaidAmount) || 0;
            let saleTotalAmount = saleTotal > 0 ? saleTotal : (parseFloat(sale.total) || 0);
            let paidRatio = (saleTotalAmount > 0) ? (paidAmount / saleTotalAmount) : 0;
            totalProfit += saleProfit * paidRatio;
          } else {
            totalProfit += saleProfit;
          }
        }
      });
      document.getElementById('totalProfit').innerText = totalProfit.toFixed(2);

      // Calculate today's sales (sum of total for sales with today's date)
      const today = new Date();
      const todayStr = today.toISOString().slice(0, 10);
      let todaySales = 0;
      sales.forEach(sale => {
        if (sale.date && sale.date.slice(0, 10) === todayStr) {
          todaySales += parseFloat(sale.total) || 0;
        }
      });
      document.getElementById('todaySales').innerText = todaySales.toFixed(2);

      // Add blinking effect to pending payments card if there is any pending amount
      // Support both dashboard card and separate card (if moved)
      let pendingPaymentsCard = document.getElementById('pendingPayments').parentElement;
      if (pendingPaymentsCard && !pendingPaymentsCard.classList.contains('card')) {
        // If not a dashboard card, look for the card by id (for custom placement)
        pendingPaymentsCard = document.getElementById('pendingPaymentsCard') || pendingPaymentsCard;
      }
      if (pendingPaymentsCard) {
        if (pending > 0) {
          pendingPaymentsCard.classList.add('pending-blink');
          pendingPaymentsCard.classList.remove('blue-blink');
        } else {
          pendingPaymentsCard.classList.remove('pending-blink');
          pendingPaymentsCard.classList.add('blue-blink');
        }
      }

      // Add blue blinking effect to specific dashboard cards
      const blueBlinkIds = [
        'totalSalesAmount',
        'totalProductsCount',
        'totalStockValue',
        'nepalTime',
        'totalProfit',
        'todaySales'
      ];
      blueBlinkIds.forEach(id => {
        const el = document.getElementById(id);
        if (el && el.parentElement) {
          el.parentElement.classList.add('blue-blink');
        }
      });
    });

    // Simple BS date converter for 2000-2089 (approximate, for display only)
    function getNepaliDateAD(adDate) {
      // Use a static offset for demonstration: BS = AD + 56 years, 8 months, 17 days
      // This is not 100% accurate for all dates, but works for dashboard display
      const offsetYear = 56, offsetMonth = 8, offsetDay = 17;
      let y = adDate.getFullYear() + offsetYear;
      let m = adDate.getMonth() + 1 + offsetMonth;
      let d = adDate.getDate() + offsetDay;

      while (m > 12) { m -= 12; y += 1; }
      // Days in month (approximate, not leap-year aware)
      const daysInMonth = [31,32,31,32,31,32,31,31,30,29,30,30];
      while (d > daysInMonth[(m-1)%12]) {
        d -= daysInMonth[(m-1)%12];
        m += 1;
        if (m > 12) { m = 1; y += 1; }
      }
      return `${y}/${String(m).padStart(2,'0')}/${String(d).padStart(2,'0')}`;
    }

    function updateNepalTime() {
      const now = new Date();
      // Nepal is UTC+5:45
      const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
      const nepal = new Date(utc + (5 * 60 + 45) * 60000);
      const h = String(nepal.getHours()).padStart(2, '0');
      const m = String(nepal.getMinutes()).padStart(2, '0');
      const s = String(nepal.getSeconds()).padStart(2, '0');
      document.getElementById('nepalTime').childNodes[0].nodeValue = `${h}:${m}:${s} `;
      // Nepali date
      const bsDate = getNepaliDateAD(nepal);
      const nepaliDateSpan = document.getElementById('nepaliDate');
      if (nepaliDateSpan) nepaliDateSpan.innerText = bsDate;
    }
    setInterval(updateNepalTime, 1000);
    updateNepalTime();

    // Password popup for Setting
    function showPasswordPopup() {
      // Remove if already exists
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
        // Both customer and developer password should work
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
      // Show profile photo if available
      const profilePhoto = localStorage.getItem('profilePhoto');
      const img = document.getElementById('profilePhotoImg');
      if (profilePhoto && img) {
        img.src = profilePhoto;
      } else if (img) {
        img.src = 'https://via.placeholder.com/40?text=Logo';
      }
      img.style.display = '';
      // Show profile name if available
      const profileName = localStorage.getItem('profileName');
      const span = document.getElementById('profileNameSpan');
      if (profileName && span) {
        span.innerText = profileName;
      }

      var settingBtn = document.getElementById('settingMenuBtn');
      if (settingBtn) {
        settingBtn.addEventListener('click', function(e) {
          e.preventDefault();
          showPasswordPopup();
        });
      }
    });

    // ...existing code...
  </script>
</body>
</html>
</body>
</html>
