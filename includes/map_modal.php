<?php
/**
 * includes/map_modal.php
 * Include snippet này vào bất kỳ trang nào cần chọn địa chỉ bằng bản đồ.
 * Đảm bảo đã load Leaflet CSS/JS và map_picker.js trước khi include.
 */
?>
<!-- ===== MAP MODAL ===== -->
<div class="modal-overlay" id="map-modal">
    <div class="modal-box" style="padding:0;">
        <div class="modal-header">
            <h3>
                <i class="fa-solid fa-map-location-dot" style="color:#288ad6;"></i>
                Chọn vị trí giao hàng trên bản đồ
            </h3>
            <button type="button" class="modal-close" onclick="closeMapModal(event)">&times;</button>
        </div>
        <div class="map-loading" id="map-loading">
            <i class="fa-solid fa-circle-notch fa-spin" style="font-size:28px; margin-bottom:12px; color:#288ad6;"></i><br>
            Đang khởi tạo bản đồ...
        </div>
        <div id="leaflet-map" style="display:none;"></div>
        <div class="map-footer">
            <i class="fa-solid fa-location-dot" style="color:#d70018; font-size:20px; flex-shrink:0;"></i>
            <input type="text" id="map-address-preview"
                   onkeydown="handleMapSearchKey(event)"
                   placeholder="Search.">
            <button type="button" onclick="confirmMapAddress()">Xác nhận</button>
        </div>
    </div>
</div>
