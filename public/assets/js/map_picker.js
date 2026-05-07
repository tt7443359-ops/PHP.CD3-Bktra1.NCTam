/**
 * map_picker.js - Dùng chung cho Profile, Admin Update User, Checkout
 * Yêu cầu: Leaflet CSS/JS phải được load trước file này
 *
 * Tính năng:
 *   - Chọn địa chỉ bằng cách click hoặc kéo marker
 *   - Chuyển đổi giữa bản đồ đường phố và vệ tinh
 *   - Tìm kiếm địa chỉ toàn thế giới (Nominatim)
 *   - Tự động detect vị trí hiện tại
 */

let _map = null, _marker = null, _layerControl = null;
let _streetLayer = null, _satelliteLayer = null;
let _addressInputId = 'address';

function initMapPicker(inputId) {
    _addressInputId = inputId || 'address';
}

function openMapModal(e) {
    if (e) e.preventDefault();
    document.getElementById('map-modal').classList.add('show');

    const currentAddress = document.getElementById(_addressInputId)
        ? document.getElementById(_addressInputId).value.trim()
        : '';

    if (!_map) {
        setTimeout(() => {
            document.getElementById('leaflet-map').style.display = 'block';
            document.getElementById('map-loading').style.display = 'none';

            _map = L.map('leaflet-map', { zoomControl: true }).setView([10.762622, 106.660172], 14);

            // Layer 1: (OpenStreetMap)
            _streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19
            });

            // Layer 2: Vệ tinh (Esri World Imagery )
            _satelliteLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, USGS, NOAA',
                maxZoom: 19
            });

            // Mặc định hiển thị đường phố
            _streetLayer.addTo(_map);

            // Bộ chuyển đổi layer
            const baseLayers = {
                '<span style="font-weight:600;">Bản đồ</span>': _streetLayer,
                '<span style="font-weight:600;">Vệ tinh</span>': _satelliteLayer
            };
            L.control.layers(baseLayers, null, { position: 'topright', collapsed: false }).addTo(_map);

            // Marker kéo 
            _marker = L.marker([10.762622, 106.660172], { draggable: true }).addTo(_map);

            _marker.on('dragend', function () {
                const pos = _marker.getLatLng();
                reverseGeocode(pos.lat, pos.lng);
            });

            _map.on('click', function (e) {
                _marker.setLatLng(e.latlng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });

            // Nếu có địa chỉ hiện tại thì tìm trước
            if (currentAddress) {
                searchMapAddress(currentAddress);
            } else if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    _map.setView([lat, lng], 16);
                    _marker.setLatLng([lat, lng]);
                    reverseGeocode(lat, lng);
                }, () => { /* Từ chối quyền - giữ nguyên vị trí mặc định */ });
            }
        }, 300);
    } else {
        if (currentAddress) {
            document.getElementById('map-address-preview').value = currentAddress;
        }
        setTimeout(() => _map.invalidateSize(), 150);
    }
}

function closeMapModal(e) {
    if (e) e.preventDefault();
    document.getElementById('map-modal').classList.remove('show');
}

function reverseGeocode(lat, lng) {
    const preview = document.getElementById('map-address-preview');
    preview.value = 'Đang quét địa chỉ...';
    // Toàn cầu - không giới hạn countrycodes
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=vi`)
        .then(r => r.json())
        .then(data => {
            preview.value = (data && data.display_name) ? data.display_name : 'Không tìm thấy địa chỉ tại vị trí này';
        })
        .catch(() => { preview.value = 'Lỗi kết nối máy chủ bản đồ'; });
}

function searchMapAddress(query) {
    if (!query || query.startsWith('Đang')) return;
    const preview = document.getElementById('map-address-preview');
    preview.value = 'Đang tìm kiếm...';
    //ưu tiên hiển thị tiếng Việt
    fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1&accept-language=vi`)
        .then(r => r.json())
        .then(data => {
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lon = parseFloat(data[0].lon);
                _map.setView([lat, lon], 16);
                _marker.setLatLng([lat, lon]);
                preview.value = data[0].display_name;
            } else {
                preview.value = query;
            }
        })
        .catch(() => { preview.value = query; });
}

// Xử lý Enter trong ô tìm kiếm
function handleMapSearchKey(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchMapAddress(e.target.value);
    }
}

function confirmMapAddress() {
    const addr = document.getElementById('map-address-preview').value;
    const skipVals = ['Đang quét địa chỉ...', 'Đang tìm kiếm...', 'Đang tìm kiếm trên bản đồ...'];
    if (addr && !skipVals.includes(addr)) {
        const el = document.getElementById(_addressInputId);
        if (el) el.value = addr;
        closeMapModal();
    }
}

// Đóng modal khi click bên ngoài
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('map-modal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('show');
        });
    }
});
