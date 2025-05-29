document.addEventListener('DOMContentLoaded', function () {
    window.processPayment = function (method) {
        const paymentAmount = parseFloat(document.getElementById('paymentAmount').value) || 0;
        const total = parseFloat((document.getElementById('total').innerText || '0').replace('₱', ''));

        if (typeof cart === 'undefined' || cart.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Empty Cart',
                text: 'Please add items to the cart before proceeding.'
            });
            return;
        }

        if (paymentAmount < total) {
            Swal.fire({
                icon: 'error',
                title: 'Insufficient Payment',
                text: 'The payment amount is less than the total.'
            });
            return;
        }

        if (method === 'GCash') {
            const qrModal = new bootstrap.Modal(document.getElementById('gcashQRModal'));
            qrModal.show();

            // Attach handler to the "Payment Completed" button
            document.getElementById('completeGcashPayment').onclick = function () {
                submitTransaction(method, paymentAmount, total);
                qrModal.hide();
            };
        } else {
            // Cash: Submit immediately
            submitTransaction(method, paymentAmount, total);
        }
    };

    function submitTransaction(method, paymentAmount, total) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/sari-sari-store/controllers/salesTransactionController.php?action=create';

        const fields = {
            total_amount: total,
            payment_method: method,
            payment_amount: paymentAmount,
            cart: JSON.stringify(cart || [])
        };

        for (let key in fields) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }
});
