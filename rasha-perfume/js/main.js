// Simple JavaScript for UI interactions

// Auto-hide alerts after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    });
});

// Confirm delete actions
const deleteButtons = document.querySelectorAll('.btn-delete');
deleteButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });
});

// Quantity validation
const quantityInputs = document.querySelectorAll('.quantity-form input');
quantityInputs.forEach(input => {
    input.addEventListener('change', function() {
        const max = parseInt(this.getAttribute('max'));
        let value = parseInt(this.value);
        
        if (isNaN(value) || value < 1) {
            this.value = 1;
        }
        if (max && value > max) {
            this.value = max;
            alert('Only ' + max + ' items available in stock');
        }
    });
});

// Mobile menu toggle
const navToggle = document.createElement('button');
navToggle.className = 'nav-toggle';
navToggle.innerHTML = '<i class="fas fa-bars"></i>';
navToggle.style.cssText = `
    background: transparent;
    border: 1px solid white;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    display: none;
`;

const navLinks = document.querySelector('.nav-links');

function checkMobileMenu() {
    if (window.innerWidth <= 768) {
        const navbar = document.querySelector('.navbar .container');
        if (navbar && !document.querySelector('.nav-toggle')) {
            navbar.insertBefore(navToggle, navLinks);
            navLinks.style.display = 'none';
            
            navToggle.addEventListener('click', () => {
                if (navLinks.style.display === 'none') {
                    navLinks.style.display = 'flex';
                    navLinks.style.flexDirection = 'column';
                    navLinks.style.position = 'absolute';
                    navLinks.style.top = '70px';
                    navLinks.style.left = '0';
                    navLinks.style.right = '0';
                    navLinks.style.backgroundColor = '#2c1a4d';
                    navLinks.style.padding = '20px';
                    navLinks.style.zIndex = '999';
                    navLinks.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                } else {
                    navLinks.style.display = 'none';
                }
            });
            navToggle.style.display = 'block';
        }
    } else {
        if (navToggle) navToggle.style.display = 'none';
        if (navLinks) {
            navLinks.style.display = 'flex';
            navLinks.style.position = 'relative';
            navLinks.style.flexDirection = 'row';
            navLinks.style.backgroundColor = 'transparent';
            navLinks.style.padding = '0';
            navLinks.style.boxShadow = 'none';
        }
    }
}

// Run on load and resize
checkMobileMenu();
window.addEventListener('resize', checkMobileMenu);

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});