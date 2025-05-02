document.addEventListener('DOMContentLoaded', () => {
    const confirmationInput = document.getElementById('confirmation');
    const deleteButton = document.getElementById('deleteButton');
    const deleteForm = document.getElementById('deleteAccountForm');
    const requiredText = "DELETE"; // The exact text required

    // Function to enable/disable the delete button
    const checkConfirmation = () => {
        if (confirmationInput.value === requiredText) {
            deleteButton.disabled = false;
        } else {
            deleteButton.disabled = true;
        }
    };

    // Initial check in case the browser autofills
    checkConfirmation();

    // Add event listener to the input field
    if (confirmationInput) {
        confirmationInput.addEventListener('input', checkConfirmation);
        confirmationInput.addEventListener('paste', (e) => {
            // Re-check immediately after pasting
            setTimeout(checkConfirmation, 0);
        });
    }

    // Optional: Add a final confirmation dialog on submit
    if (deleteForm && deleteButton) {
        deleteForm.addEventListener('submit', (event) => {
            // Double-check the button isn't disabled (shouldn't happen if JS is correct, but good practice)
            if (deleteButton.disabled) {
                event.preventDefault(); // Stop submission if button is somehow disabled
                console.error("Delete button is disabled, preventing submission.");
                return;
            }

            // Ask for final confirmation
            const isConfirmed = confirm('Are you absolutely sure you want to delete your account? This action is permanent.');

            if (!isConfirmed) {
                event.preventDefault(); // Stop the form submission if the user cancels
            }
            // If confirmed, the form will submit naturally to the controller action URL
        });
    }
});
