<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account: Please Login</title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>res/css/verify.css">
</head>
<body>
    <div class="login-container">
        <form id="loginForm" class="login-form" action="verify/validate" method="post">
            <h2>Login</h2>
            <div class="form-group">
                <label for="cred_1">Email:</label>
                <input type="email" id="cred_1" name="cred_1" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="cred_2" name="cred_2" required>
            </div>
            <button type="submit" class="login-button">Login</button>
            <p id="message" class="message"></p>
			<div class="message"><?php echo $this->session->flashdata('message'); ?></div>
        </form>
    </div>

    <script src="main.js"></script>
</body>
</html>
