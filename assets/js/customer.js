// assets/js/customer.js

document.addEventListener('DOMContentLoaded', () => {
    // 1. ระบบค้นหาและกรองหมวดหมู่สินค้าหน้าร้าน
    const searchInput = document.getElementById('searchInput');
    const categoryPills = document.querySelectorAll('.category-pill');
    const productItems = document.querySelectorAll('.product-item');
    let activeCategory = '';

    function runFilter() {
        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase().trim();

        productItems.forEach(item => {
            const name = (item.getAttribute('data-name') || '').toLowerCase().trim();
            const category = (item.getAttribute('data-category') || '').toLowerCase().trim();

            const matchName = name.includes(searchTerm);
            const matchCategory = (activeCategory === '') || (category === activeCategory);

            if (matchName && matchCategory) {
                item.style.setProperty('display', 'block', 'important');
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', runFilter);
    }

    if (categoryPills.length > 0) {
        categoryPills.forEach(pill => {
            pill.addEventListener('click', function (e) {
                e.preventDefault();
                categoryPills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                activeCategory = (this.getAttribute('data-category') || '').toLowerCase().trim();
                runFilter();
            });
        });
    }
});

// 2. ระบบจำลองสั่งซื้อหยิบใส่ตะกร้า (Cart)
let totalItems = 0;
function addToCart(name, price) {
    totalItems++;
    const cartCountElement = document.getElementById('cartCount');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
    }

    Swal.fire({
        icon: 'success',
        title: 'เพิ่มลงในตะกร้าแล้ว!',
        text: `${name} (ราคา ฿${Number(price).toFixed(2)})`,
        timer: 1200,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}