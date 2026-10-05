document.addEventListener('DOMContentLoaded', () => {
    // 1. ระบบค้นหาและกรองหมวดหมู่สินค้า
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

// 2. SweetAlert2 ยืนยันการลบสินค้า
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${name}" from the system?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D97745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Delete Data',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete.php?id=${id}`;
        }
    });
}