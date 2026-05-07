function toggleCustom(sel) {
    var wrap = document.getElementById('customWrap');
    if (wrap) {
        wrap.style.display = (sel.value === 'Lý do khác') ? 'block' : 'none';
    }
}

function validateCancelForm(orderId) {
    var sel = document.getElementById('reasonSel').value;
    if (!sel) {
        alert('Vui lòng chọn lý do hủy!');
        return false;
    }
    if (sel === 'Lý do khác') {
        var txt = document.querySelector('textarea[name="custom_reason"]').value.trim();
        if (!txt) {
            alert('Vui lòng nhập lý do cụ thể vào khung trống bên dưới!');
            return false;
        }
    }
    return confirm('Bạn chắc chắn muốn gửi yêu cầu hủy cho mã đơn #' + orderId + '?');
}

function openNoteModal(note) {
    const content = document.getElementById('noteModalContent');
    const modalTitle = document.querySelector('#noteModal .modal-header span');
    
    if (content) {
        content.innerText = note;
        content.style.whiteSpace = 'pre-wrap';
    }
    if (modalTitle) modalTitle.innerText = 'Lời Nhắn Của Khách';
    
    const modal = document.getElementById('noteModal');
    if (modal) modal.classList.add('show');
}

function closeNoteModal() {
    const modal = document.getElementById('noteModal');
    if (modal) modal.classList.remove('show');
}

function openOrderDetailsModal(html) {
    const content = document.getElementById('noteModalContent');
    const modalTitle = document.querySelector('#noteModal .modal-header span');
    
    if (content) {
        content.innerHTML = html;
        content.style.whiteSpace = 'normal';
    }
    if (modalTitle) modalTitle.innerText = 'Chi Tiết Sản Phẩm';
    
    const modal = document.getElementById('noteModal');
    if (modal) modal.classList.add('show');
}