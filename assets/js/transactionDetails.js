// Handles viewing transaction details in a popup modal
function viewTransactionDetails(saleId) {
    fetch(`/sari-sari-store/controllers/salesTransactionController.php?action=view&sale_id=${saleId}`)
        .then(response => response.json())
        .then(data => {
            if (!data || !data.sale_id) {
                Swal.fire({
                    icon: 'error',
                    title: 'Not Found',
                    text: 'Transaction details not found.'
                });
                return;
            }
            // Build HTML for details
            let html = `<div class='text-start'>
                <strong>Transaction ID:</strong> ${data.sale_id}<br>
                <strong>Customer:</strong> ${data.customer_id || 'Walk-in'}<br>
                <strong>Admin:</strong> ${data.admin_id}<br>
                <strong>Date:</strong> ${data.sale_date}<br>
                <strong>Payment Method:</strong> ${data.payment_method}<br>
                <strong>Total Amount:</strong> ₱${parseFloat(data.total_amount).toFixed(2)}<br>
            </div>`;
            Swal.fire({
                title: 'Transaction Details',
                html: html,
                icon: 'info',
                confirmButtonText: 'Close',
                customClass: {popup: 'swal-wide'}
            });
        })
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch transaction details.'
            });
        });
}
