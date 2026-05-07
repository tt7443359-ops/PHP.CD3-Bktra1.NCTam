document.addEventListener("DOMContentLoaded", function () {
    //input file ẩn
    const uploadZones = document.querySelectorAll('.upload-zone-trigger');
    uploadZones.forEach(function (zone) {
        zone.addEventListener('click', function () {
            const inputId = this.getAttribute('data-input');
            if (inputId) {
                document.getElementById(inputId).click();
            }
        });
    });

    // Xử lý preview
    const fileInputs = document.querySelectorAll('.preview-input');
    fileInputs.forEach(function (input) {
        input.addEventListener('change', function (e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                // Lấy bộ chọn 
                const targetSelector = this.getAttribute('data-target');
                const previewImgs = document.querySelectorAll(targetSelector);
                
                // Lấy ID 
                const placeholderSelector = this.getAttribute('data-placeholder');
                const placeholders = placeholderSelector ? document.querySelectorAll(placeholderSelector) : [];

                reader.onload = function (event) {
                    // Cập nhật tất cả 
                    previewImgs.forEach(img => {
                        img.src = event.target.result;
                        img.style.display = 'block';
                    });
                    
                    // Ẩn placeholder 
                    placeholders.forEach(ph => {
                        ph.style.display = 'none';
                    });
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});
