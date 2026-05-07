function toggleAll(source) {
    let checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    syncCheckboxes();
}

function syncCheckboxes() {
    let checkboxes = document.querySelectorAll('.item-checkbox');
    let allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
    document.getElementById('check-all-header').checked = allChecked;
    document.getElementById('check-all-footer').checked = allChecked;
    
    let totalQty = 0;
    let totalPrice = 0;
    checkboxes.forEach(cb => {
        if(cb.checked) {
            totalQty += parseInt(cb.getAttribute('data-qty'));
            totalPrice += parseFloat(cb.getAttribute('data-price')) * parseInt(cb.getAttribute('data-qty'));
        }
    });
    
    document.getElementById('selected-count').innerText = totalQty;
    document.getElementById('selected-price').innerText = new Intl.NumberFormat('vi-VN').format(totalPrice);
}

function checkoutSelected() {
    let selected = [];
    document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
        selected.push(cb.value);
    });
    if (selected.length === 0) {
        document.getElementById('emptySelectModal').classList.add('show');
        return;
    }
    window.location.href = BASE_URL + 'checkout?items=' + selected.join(',');
}
