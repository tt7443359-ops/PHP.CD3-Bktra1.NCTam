/* -- SIDEBAR  -- */
function toggleSidebar() {
    var sidebar = document.getElementById("sidebarMenu");
    var overlay = document.getElementById("sidebarOverlay");
    if (sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        overlay.style.display = "none";
    } else {
        sidebar.classList.add('active');
        overlay.style.display = "block";
    }
}

/* -- CATEGORY DROPDOWN  -- */
function toggleCatDropdown() {
    var dd = document.getElementById("catDropdown");
    var arrow = document.getElementById("catArrow");
    var isOpen = dd.classList.contains('open');
    if (isOpen) {
        dd.classList.remove('open');
        arrow.style.transform = "rotate(0deg)";
    } else {
        dd.classList.add('open');
        arrow.style.transform = "rotate(180deg)";
        // Scroll to selected item
        var sel = dd.querySelector('.cat-item.selected');
        if (sel) {
            var list = document.getElementById("catList");
            list.scrollTop = sel.offsetTop - 30;
        }
    }
}

function selectCat(el, value, label) {
    document.querySelectorAll('.cat-item').forEach(function (i) { i.classList.remove('selected'); });
    el.classList.add('selected');
    var fullLabel = label.length > 14 ? label.substring(0, 13) + '…' : label;
    document.getElementById("catBtnLabel").textContent = fullLabel;
    document.getElementById("catValueInput").value = value;
    var dd = document.getElementById("catDropdown");
    dd.classList.remove('open');
    document.getElementById("catArrow").style.transform = "rotate(0deg)";
    document.querySelector('.hi-search-input').value = '';
    document.getElementById("headerSearchForm").submit();
}

// Đóng dropdown click bên ngoài
document.addEventListener('click', function (e) {
    var wrapper = document.getElementById("catWrapper");
    if (wrapper && !wrapper.contains(e.target)) {
        var dd = document.getElementById("catDropdown");
        if (dd) {
            dd.classList.remove('open');
            document.getElementById("catArrow").style.transform = "rotate(0deg)";
        }
    }
});

// Set label ban đầu
(function () {
    var sel = document.querySelector('.cat-item.selected');
    if (sel) {
        var label = sel.textContent.trim();
        var fullLabel = label.length > 14 ? label.substring(0, 13) + '…' : label;
        var btnLabel = document.getElementById("catBtnLabel");
        if (btnLabel) btnLabel.textContent = fullLabel;
    }
})();
