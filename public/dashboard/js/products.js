document.addEventListener('DOMContentLoaded', function() {
    // Tính toán giá giảm khi thay đổi % giảm giá
    const discountPercentInput = document.querySelector('input[name="discount_percent"]');
    const priceInput = document.querySelector('input[name="price"]');
    
    if (discountPercentInput && priceInput) {
        discountPercentInput.addEventListener('input', function() {
            const percent = parseFloat(this.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            
            if (percent > 0 && price > 0) {
                const discountPrice = price * (1 - percent / 100);
                // Có thể hiển thị giá giảm cho người dùng thấy
                console.log('Giá sau giảm:', discountPrice.toFixed(2));
            }
        });
    }
    
    // Xử lý hiển thị preview ảnh khi chọn file
    const fileInputs = document.querySelectorAll('input[type="file"][name="images[]"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewContainer = document.createElement('div');
            previewContainer.className = 'row mt-2';
            
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-2 mb-2';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.width = 100;
                    
                    col.appendChild(img);
                    previewContainer.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
            
            this.parentNode.insertBefore(previewContainer, this.nextSibling);
        });
    });
});