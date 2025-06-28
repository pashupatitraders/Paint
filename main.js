const { app, BrowserWindow, ipcMain } = require('electron');
const path = require('path');

let win;

function createWindow() {
  win = new BrowserWindow({
    width: 1200,
    height: 800,
    webPreferences: {
      nodeIntegration: true,
      contextIsolation: false,
    },
  });

  // Show login page first
  win.loadFile('login.php');
}

// Open login window
app.whenReady().then(createWindow);

// After login success, show main app
ipcMain.on('login-success', () => {
  win.loadFile('index.php');
});

app.on('window-all-closed', () => {
  if (process.platform !== 'darwin') app.quit();
});
