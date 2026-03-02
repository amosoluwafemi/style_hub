// Confirm before deleting from cart
function confirmDelete(event) {
    if (!confirm("Are you sure you want to remove this item?")) {
        event.preventDefault();
    }
}

// Add event listeners to delete buttons
document.querySelectorAll('.btn-outline-danger').forEach(button => {
    button.addEventListener('click', confirmDelete);
});

// Example: If shop.php detects a success session, trigger a nice toast
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('ordered')) {
        // Use a pretty modal or toast here
        console.log("Order was successful!");
    }
});