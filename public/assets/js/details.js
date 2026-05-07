 window.onload = function() {
        imageZoom("myimage", "myresult", "mylens");
        initThumbs();
        initDescToggle();
    };

    // ── Lightbox modal ──────────────────────────────
    function openImgModal(src) {
        document.getElementById('modalMainImg').src = src;
        // sync active thumb in modal to match current main image
        var mainSrc = document.getElementById('myimage').src;
        document.querySelectorAll('.img-modal-thumbs .thumb-item').forEach(function(t) {
            t.classList.toggle('active', t.dataset.src === mainSrc);
        });
       // Độ trễ mở form
        setTimeout(function() {
            document.getElementById('imgModalOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }, 0,8); // 100ms = 0.1 giây.
    }
    function closeImgModal(e) {
        if (e.target === document.getElementById('imgModalOverlay')) closeImgModalDirect();
    }
    function closeImgModalDirect() {
        document.getElementById('imgModalOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    function switchModalImg(el, idx) {
        document.querySelectorAll('.img-modal-thumbs .thumb-item').forEach(function(t){ t.classList.remove('active'); });
        el.classList.add('active');
        document.getElementById('modalMainImg').src = el.dataset.src;
    }

    // ── Read Sample modal ───────────────────────────
    function openReadSample() {
        document.getElementById('readSampleOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeReadSample(e) {
        if (e.target === document.getElementById('readSampleOverlay')) closeReadSampleDirect();
    }
    function closeReadSampleDirect() {
        document.getElementById('readSampleOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }

    // ── Keyboard ESC closes any open modal ──────────
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImgModalDirect();
            closeReadSampleDirect();
        }
    });

    function initDescToggle() {
        var wrapper = document.getElementById('descWrapper');
        var toggle  = document.getElementById('descToggle');
        var fade    = document.getElementById('descFade');
        if (!wrapper || !toggle) return;

        // Chỉ hiện nút nếu nội dung thực sự dài hơn max-height
        if (wrapper.scrollHeight > wrapper.offsetHeight + 10) {
            toggle.style.display = 'inline-block';
            if (fade) fade.style.display = 'block';
        } else {
            toggle.style.display = 'none';
            if (fade) fade.style.display = 'none';
            wrapper.style.maxHeight = 'none'; // Đảm bảo nội dung ngắn không bị kẹp
        }
    }

    function toggleDesc() {
        var wrapper = document.getElementById('descWrapper');
        var toggle  = document.getElementById('descToggle');
        var fade    = document.getElementById('descFade');
        var expanded = wrapper.classList.toggle('expanded');
        
        // Lấy chữ từ data attributes để đa ngôn ngữ
        var textMore = toggle.getAttribute('data-text-more') || 'Xem thêm ▾';
        var textLess = toggle.getAttribute('data-text-less') || 'Thu gọn ▴';
        
        toggle.textContent = expanded ? textLess : textMore;
    }

    function initThumbs() {
        var mainImg = document.getElementById('myimage');
        var thumbs  = document.querySelectorAll('.thumb-item');
        if (!thumbs.length) return;

        thumbs.forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                // Cập nhật active
                thumbs.forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');

                // Đổi ảnh chính
                var newSrc = this.dataset.src;
                mainImg.src = newSrc;

                // Reinit zoom sau khi ảnh load xong
                mainImg.onload = function() {
                    imageZoom("myimage", "myresult", "mylens");
                };
                // Nếu ảnh đã cache (complete ngay)
                if (mainImg.complete) {
                    imageZoom("myimage", "myresult", "mylens");
                }
            });
        });
    }