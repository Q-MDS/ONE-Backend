<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link rel="stylesheet" href="<?php echo base_url('res/css/main.css'); ?>"> <?php // Adjust path as needed ?>
</head>
<body>
    <div class="delete-container">
        <h2>Delete Your Account</h2>

        <div class="warning-message">
            <strong>Warning:</strong> This action is permanent and cannot be undone. All your data associated with this account will be permanently removed.
        </div>

        <?php // Display any feedback messages from the controller (optional)
        if ($this->session->flashdata('error_message')) {
            echo '<div class="message error">' . $this->session->flashdata('error_message') . '</div>';
        }
        if ($this->session->flashdata('success_message')) {
            echo '<div class="message success">' . $this->session->flashdata('success_message') . '</div>';
        }
        ?>

        <form id="deleteAccountForm" action="<?php echo site_url('account/delete_account'); ?>" method="post">
            <?php // Include CSRF token if you're using CodeIgniter's CSRF protection
            $csrf = array(
                'name' => $this->security->get_csrf_token_name(),
                'hash' => $this->security->get_csrf_hash()
            );
            ?>
            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

            <div class="form-group confirmation-group">
                <label for="confirmation">To confirm deletion, please type "DELETE" in the box below:</label>
                <input type="text" id="confirmation" name="confirmation_text" required autocomplete="off">
                <small>This field is case-sensitive.</small>
            </div>

            <div class="form-group">
                <button type="submit" id="deleteButton" class="delete-button" disabled>Delete My Account Permanently</button>
            </div>
            <div class="form-group cancel-link">
                 <a href="<?php echo site_url('verify'); ?>">Cancel and return to my account</a> <?php // Adjust link as needed ?>
            </div>
        </form>
    </div>

    <script src="<?php echo base_url('res/js/delete_account.js'); ?>"></script> <?php // Adjust path as needed ?>
</body>
</html>
