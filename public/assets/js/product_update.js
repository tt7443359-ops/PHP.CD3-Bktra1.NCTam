// Toggle đánh dấu xóa ảnh phụ
function toggleDeleteExtra(imgId) {
    var item = document.getElementById('exItem' + imgId);
    var chk  = document.getElementById('delChk' + imgId);
    var btn  = item.querySelector('.del-btn');
    if (chk.checked) {
        chk.checked = false;
        item.classList.remove('marked-delete');
        btn.textContent = '✕';
    } else {
        chk.checked = true;
        item.classList.add('marked-delete');
        btn.textContent = '↩';
    }
}

// Preview ảnh phụ mới chọn
document.getElementById('extraImgsInput').addEventListener('change', function() {
    var list = document.getElementById('extraPreviewList');
    var placeholder = document.getElementById('extraPlaceholder');
    list.innerHTML = '';
    if (this.files.length > 0) {
        placeholder.style.display = 'none';
    } else {
        placeholder.style.display = 'block';
    }
    Array.from(this.files).forEach(function(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var wrap = document.createElement('div');
            wrap.style.cssText = 'width:56px;height:72px;border:1px solid #d5d9d9;border-radius:3px;overflow:hidden;background:#fff;';
            var img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
            wrap.appendChild(img);
            list.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
});
