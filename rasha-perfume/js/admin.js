// Admin Panel JavaScript

// Auto-hide alerts after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    });
    
    // Initialize status select auto-submit
    const statusSelects = document.querySelectorAll('.status-select');
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
    
    // Initialize add product form validation
    const addProductForm = document.getElementById('addProductForm');
    if (addProductForm) {
        addProductForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name');
            const description = document.getElementById('description');
            const price = document.getElementById('price');
            const category = document.getElementById('category');
            const stock = document.getElementById('stock');
            
            if (!name.value.trim()) {
                e.preventDefault();
                alert('Please enter product name');
                name.focus();
                return false;
            }
            
            if (!description.value.trim()) {
                e.preventDefault();
                alert('Please enter product description');
                description.focus();
                return false;
            }
            
            if (!price.value || parseFloat(price.value) <= 0) {
                e.preventDefault();
                alert('Please enter a valid price greater than 0');
                price.focus();
                return false;
            }
            
            if (!category.value) {
                e.preventDefault();
                alert('Please select a category');
                category.focus();
                return false;
            }
            
            if (stock.value === '' || parseInt(stock.value) < 0) {
                e.preventDefault();
                alert('Please enter a valid stock quantity');
                stock.focus();
                return false;
            }
            
            return true;
        });
    }
});

// Confirm delete
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// Attach delete confirmation to all delete buttons
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-delete')) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
        }
    }
});