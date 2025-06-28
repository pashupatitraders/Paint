<?php
session_start();

// Handle login logic when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $defaultUsername = 'admin';
    $defaultPassword = '12345';

    $enteredUsername = $_POST['username'] ?? '';
    $enteredPassword = $_POST['pass'] ?? '';

    if ($enteredUsername === $defaultUsername && $enteredPassword === $defaultPassword) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $enteredUsername;
        header("Location: dashboard.php");
        exit();
    } elseif ($enteredUsername === 'bishal' && $enteredPassword === '1126') {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = 'Developer';
        header("Location: dashboard.php"); // or developer_dashboard.php if you have one
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login V16</title>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<style>
		/* Reset and basic styles */
		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body, html {
			height: 100%;
			font-family: 'Poppins', sans-serif;
			background: #f2f2f2;
		}

		.limiter {
			width: 100%;
			margin: 0 auto;
		}

		.container-login100 {
			width: 100vw;
			height: 100vh;
			background: #666666;
			display: flex;
			justify-content: center;
			align-items: center;
			padding: 15px;
		}

		.wrap-login100 {
			width: 960px;
			background: #fff;
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			border-radius: 10px;
			overflow: hidden;
			box-shadow: 0 5px 15px rgba(0,0,0,0.3);
			animation: blink-shadow 1s infinite alternate;
		}

		@keyframes blink-shadow {
			0% {
				box-shadow: 0 5px 15px rgba(0,0,0,0.3), 0 0 0 0 #4fc3f7;
			}
			100% {
				box-shadow: 0 5px 15px rgba(0,0,0,0.3), 0 0 30px 10px #4fc3f7;
			}
		}

		.login100-pic {
			width: 50%;
			background: url('https://colorlib.com/etc/lf/Login_v16/images/img-01.png') no-repeat center;
			background-size: cover;
		}

		.login100-form {
			width: 50%;
			padding: 55px 55px 37px 55px;
			display: flex;
			flex-direction: row;
			align-items: center;
			gap: 24px;
			position: relative; /* Needed for absolute positioning of image */
		}

		.login-form-img {
			position: absolute;
			left: -350px; /* Move image to the left of the form */
			top: 50%;
			transform: translateY(-50%);
			flex: none;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-left: 0; /* Remove previous negative margin */
		}

		.login-form-img img {
			max-width: 350px;
			max-height: 350px;
			object-fit: contain;
			border-radius: 10px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.08);
			margin: 0;
		}

		.login-form-fields {
			flex: 1 1 0;
		}

		.login100-form-title {
			font-size: 30px;
			color: #333333;
			line-height: 1.2;
			text-align: center;
			font-weight: 700;
			margin-bottom: 30px;
		}

		.wrap-input100 {
			position: relative;
			width: 100%;
			border-bottom: 2px solid #d9d9d9;
			margin-bottom: 37px;
		}

		.input100 {
			font-size: 16px;
			color: #666666;
			line-height: 1.2;
			display: block;
			width: 100%;
			height: 45px;
			background: transparent;
			padding: 0 5px 0 0;
			border: none;
			outline: none;
		}

		.focus-input100 {
			position: absolute;
			display: block;
			width: 100%;
			height: 100%;
			bottom: -2px;
			left: 0;
			pointer-events: none;
			border-bottom: 2px solid #adadad;
			transition: all 0.4s;
		}

		.input100:focus + .focus-input100 {
			border-color: #4fc3f7;
			box-shadow: 0 2px 8px #4fc3f7;
		}

		.login100-form-btn {
			font-size: 16px;
			color: #fff;
			line-height: 1.2;
			text-transform: uppercase;
			width: 100%;
			height: 50px;
			border-radius: 25px;
			background: #4fc3f7;
			display: flex;
			justify-content: center;
			align-items: center;
			border: none;
			outline: none;
			cursor: pointer;
			transition: all 0.4s;
			font-weight: 700;
		}

		.login100-form-btn:hover {
			background: #039be5;
		}

		.text-center {
			text-align: center;
		}

		.text-center p {
			font-size: 14px;
			color: #999999;
		}

		.text-center a {
			color: #4fc3f7;
			text-decoration: none;
			font-weight: 700;
		}

		@media screen and (max-width: 768px) {
			.wrap-login100 {
				flex-direction: column;
				width: 100%;
			}

			.login100-pic, .login100-form {
				width: 100%;
			}

			.login100-pic {
				height: 200px;
				background-position: center top;
			}
			.login100-form {
				flex-direction: column;
				gap: 0;
				padding: 30px 20px;
				align-items: stretch;
			}
			.login-form-img {
				justify-content: flex-start;
				margin-top: 20px;
			}
		}
	</style>
</head>
<body>

	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<div class="login100-pic"></div>

				<form class="login100-form" method="post">
					<div class="login-form-img">
						<img src="login.png" alt="Login" />
					</div>
					<div class="login-form-fields">
						<span class="login100-form-title">Member Login</span>
						<?php if (isset($error)) {
							echo "<script>alert('$error');</script>";
						} ?>
						<div class="wrap-input100">
							<input class="input100" type="text" id="username" name="username" placeholder="Username" required />
							<span class="focus-input100"></span>
						</div>
						<div class="wrap-input100">
							<input class="input100" type="password" id="password" name="pass" placeholder="Password" required />
							<span class="focus-input100"></span>
						</div>
						<div class="container-login100-form-btn">
							<button class="login100-form-btn" type="submit" id="loginBtn">Login</button>
						</div>
						<div class="text-center p-t-12" style="margin-top: 20px;">
							<p>Forgot <a href="#" id="forgotLink">Username / Password?</a></p>
						</div>

						
					</div>
				</form>
				<!-- Popup Modal for Developer -->
				<div id="devModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); z-index:999; align-items:center; justify-content:center;">
					<div style="background:#fff; border-radius:10px; padding:32px 24px; max-width:350px; margin:auto; box-shadow:0 8px 32px rgba(0,0,0,0.2); position:relative;">
						<span style="font-size:20px; font-weight:600; color:#333;">Developer Setup</span>
						<form id="devForm" style="margin-top:18px;">
							<div style="margin-bottom:14px;">
								<input type="text" placeholder="New Username" required style="width:100%;padding:8px;border-radius:5px;border:1px solid #ccc;">
							</div>
							<div style="margin-bottom:14px;">
								<input type="password" placeholder="New Password" required minlength="4" style="width:100%;padding:8px;border-radius:5px;border:1px solid #ccc;">
							</div>
							<div style="margin-bottom:14px;">
								<input type="password" placeholder="Confirm Password" required minlength="4" style="width:100%;padding:8px;border-radius:5px;border:1px solid #ccc;">
							</div>
							<div style="margin-bottom:18px;">
								<input type="text" placeholder="Mobile No" required style="width:100%;padding:8px;border-radius:5px;border:1px solid #ccc;">
							</div>
							<button type="submit" style="width:100%;background:#4fc3f7;color:#fff;padding:10px 0;border:none;border-radius:5px;font-weight:600;cursor:pointer;">Submit</button>
						</form>
						<button onclick="closeDevModal()" style="position:absolute;top:8px;right:12px;background:none;border:none;font-size:22px;cursor:pointer;color:#888;">&times;</button>
					</div>
				</div>
				<!-- Popup Modal for Forgot -->
				<div id="forgotModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); z-index:1000; align-items:center; justify-content:center;">
					<div style="background:#fff; border-radius:10px; padding:32px 24px; max-width:350px; margin:auto; box-shadow:0 8px 32px rgba(0,0,0,0.2); position:relative;">
						<span style="font-size:20px; font-weight:600; color:#333;">Forgot Username/Password</span>
						<form id="forgotForm" style="margin-top:18px;">
							<div style="margin-bottom:18px;">
								<input type="text" id="forgotPhone" placeholder="Enter your Mobile No" required style="width:100%;padding:8px;border-radius:5px;border:1px solid #ccc;">
							</div>
							<button type="submit" style="width:100%;background:#4fc3f7;color:#fff;padding:10px 0;border:none;border-radius:5px;font-weight:600;cursor:pointer;">Verify</button>
						</form>
						<button onclick="closeForgotModal()" style="position:absolute;top:8px;right:12px;background:none;border:none;font-size:22px;cursor:pointer;color:#888;">&times;</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
document.getElementById('loginBtn').onclick = function(e) {
		const username = document.getElementById('username').value.trim();
		const password = document.getElementById('password').value.trim();
		if (username === 'bishal' && password === '1126') {
			e.preventDefault();
			document.getElementById('devModal').style.display = 'flex';
		}
		// else: allow normal form submit for PHP validation
	};

function closeDevModal() {
	document.getElementById('devModal').style.display = 'none';
}

// Handle developer popup form submission
document.getElementById('devForm').onsubmit = function(e) {
	e.preventDefault();
	const inputs = this.querySelectorAll('input');
	const newUsername = inputs[0].value.trim();
	const newPassword = inputs[1].value.trim();
	const confirmPassword = inputs[2].value.trim();
	const mobileNo = inputs[3].value.trim();

	if (!newUsername || !newPassword || !confirmPassword || !mobileNo) {
		alert('All fields are required.');
		return;
	}
	if (newPassword.length < 4 || confirmPassword.length < 4) {
		alert('Password must be at least 4 characters.');
		return;
	}
	if (newPassword !== confirmPassword) {
		alert('Passwords do not match.');
		return;
	}

	// Set new credentials for customer and store in localStorage for persistence
	localStorage.setItem('customerUsername', newUsername);
	localStorage.setItem('customerPassword', confirmPassword); // use confirmPassword as new password
	localStorage.setItem('customerPhone', mobileNo);

	// Update the username and password fields in the form
	document.getElementById('username').value = newUsername;
	document.getElementById('password').value = confirmPassword;

	alert('Customer credentials updated! Only the new username and password will work for customer login.');
	closeDevModal();
};

// Forgot Username/Password popup logic
document.getElementById('forgotLink').onclick = function(e) {
	e.preventDefault();
	document.getElementById('forgotModal').style.display = 'flex';
};
function closeForgotModal() {
	document.getElementById('forgotModal').style.display = 'none';
}
document.getElementById('forgotForm').onsubmit = function(e) {
	e.preventDefault();
	const enteredPhone = document.getElementById('forgotPhone').value.trim();
	const storedPhone = localStorage.getItem('customerPhone') || '';
	if (enteredPhone && enteredPhone === storedPhone) {
		document.getElementById('forgotModal').style.display = 'none';
		document.getElementById('devModal').style.display = 'flex';
	} else {
		alert('Mobile number does not match our records.');
	}
};
	</script>

</body>
</html>
