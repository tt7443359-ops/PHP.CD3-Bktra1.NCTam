// Checkout initialization
(function() {
    if (typeof initMapPicker === 'function') {
        initMapPicker('ship_address');
        initMapPicker('modal_address');
    }
})();

function openAddressModal() {
    document.getElementById('addressModal').classList.add('show');
}
function closeAddressModal() {
    document.getElementById('addressModal').classList.remove('show');
}
function saveAddressModal() {
    var name = document.getElementById('modal_name').value;
    var phone = document.getElementById('modal_phone').value;
    var addr = document.getElementById('modal_address').value;
    
    document.getElementById('ship_name_input').value = name;
    document.getElementById('ship_phone_input').value = phone;
    document.getElementById('ship_address_input').value = addr;
    
    document.getElementById('display_name').innerText = name || 'Chưa cập nhật Tên';
    document.getElementById('display_phone').innerText = phone ? phone.replace(/^0+/, '') : 'Chưa có SĐT';
    document.getElementById('display_address').innerText = addr || 'Chưa cập nhật địa chỉ';
    
    closeAddressModal();
}

function selectPayment(btn, method, name) {
    // Remove active class from all buttons
    document.querySelectorAll('.btn-payment').forEach(el => el.classList.remove('active'));
    // Add active class to clicked button
    btn.classList.add('active');
    // Update hidden input
    document.getElementById('payment_method_input').value = method;
    // Update summary text
    document.getElementById('currentPaymentName').innerText = name;
    // Hide options and show summary
    document.getElementById('paymentOptionsWrapper').style.display = 'none';
    document.getElementById('paymentSummary').style.display = 'flex';
}

function togglePaymentOptions() {
    var wrapper = document.getElementById('paymentOptionsWrapper');
    var summary = document.getElementById('paymentSummary');
    
    if (wrapper.style.display === 'none') {
        wrapper.style.display = 'block';
        summary.style.display = 'none';
    } else {
        wrapper.style.display = 'none';
        summary.style.display = 'flex';
    }
}

function openShippingModal() {
    document.getElementById('shippingModal').classList.add('show');
}
function closeShippingModal() {
    document.getElementById('shippingModal').classList.remove('show');
}
function saveShippingModal() {
    // In a real app, you'd update the UI based on selection.
    // For now, we just close it.
    closeShippingModal();
}
