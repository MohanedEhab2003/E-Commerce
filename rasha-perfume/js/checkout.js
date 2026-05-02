// Checkout Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Card number formatting
    const cardNumberInput = document.getElementById('card_number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\s/g, '');
            if (value.length > 0) {
                value = value.match(/.{1,4}/g).join(' ');
            }
            this.value = value.substring(0, 19);
        });
    }
    
    // Expiry date formatting
    const expiryInput = document.getElementById('expiry_date');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\//g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.value = value.substring(0, 5);
        });
    }
    
    // CVV should only accept numbers
    const cvvInput = document.getElementById('cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 4);
        });
    }
    
    // Card number should only accept numbers
    if (cardNumberInput) {
        cardNumberInput.addEventListener('keypress', function(e) {
            if (!/[0-9\s]/.test(e.key)) {
                e.preventDefault();
            }
        });
    }
    
    // Form validation
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            
            if (paymentMethod && paymentMethod.value !== 'cash_on_delivery') {
                // Validate card details
                const cardNumber = document.getElementById('card_number');
                const cardHolder = document.getElementById('card_holder_name');
                const expiry = document.getElementById('expiry_date');
                const cvv = document.getElementById('cvv');
                
                let isValid = true;
                
                // Clear previous errors
                document.querySelectorAll('.error-message').forEach(el => el.remove());
                document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                
                // Validate card number
                const cardNumberClean = cardNumber.value.replace(/\s/g, '');
                if (!cardNumberClean || cardNumberClean.length < 13) {
                    showError(cardNumber, 'Please enter a valid card number');
                    isValid = false;
                }
                
                // Validate card holder
                if (!cardHolder.value.trim()) {
                    showError(cardHolder, 'Please enter card holder name');
                    isValid = false;
                }
                
                // Validate expiry
                if (!expiry.value || !/^\d{2}\/\d{2}$/.test(expiry.value)) {
                    showError(expiry, 'Please enter valid expiry date (MM/YY)');
                    isValid = false;
                } else {
                    // Check if card is expired
                    const [month, year] = expiry.value.split('/');
                    const expiryDate = new Date(2000 + parseInt(year), parseInt(month) - 1);
                    const today = new Date();
                    if (expiryDate < today) {
                        showError(expiry, 'Card has expired');
                        isValid = false;
                    }
                }
                
                // Validate CVV
                if (!cvv.value || cvv.value.length < 3) {
                    showError(cvv, 'Please enter valid CVV');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            }
            
            // Validate shipping address
            const address = document.querySelector('textarea[name="shipping_address"]');
            if (!address.value.trim()) {
                showError(address, 'Please enter shipping address');
                e.preventDefault();
            }
            
            // Validate phone
            const phone = document.querySelector('input[name="phone"]');
            if (!phone.value.trim()) {
                showError(phone, 'Please enter phone number');
                e.preventDefault();
            }
        });
    }
    
    // Payment method toggle
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const cardDetails = document.getElementById('cardDetails');
    
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'credit_card' || this.value === 'debit_card') {
                if (cardDetails) cardDetails.style.display = 'block';
            } else {
                if (cardDetails) cardDetails.style.display = 'none';
            }
        });
    });
    
    // Show error function
    function showError(input, message) {
        input.classList.add('error');
        const errorDiv = document.createElement('span');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }
});

// Detect card type based on first digits
function getCardType(cardNumber) {
    const cleanNumber = cardNumber.replace(/\s/g, '');
    const firstDigit = cleanNumber.charAt(0);
    const firstTwoDigits = cleanNumber.substring(0, 2);
    
    if (firstDigit === '4') return 'visa';
    if (firstTwoDigits >= '51' && firstTwoDigits <= '55') return 'mastercard';
    if (firstTwoDigits === '34' || firstTwoDigits === '37') return 'amex';
    return 'unknown';
}

// Update card icon based on type
function updateCardIcon(cardNumber) {
    const cardType = getCardType(cardNumber);
    const cardIcon = document.querySelector('.card-icon');
    
    if (cardIcon) {
        if (cardType === 'visa') {
            cardIcon.className = 'fab fa-cc-visa card-icon';
        } else if (cardType === 'mastercard') {
            cardIcon.className = 'fab fa-cc-mastercard card-icon';
        } else if (cardType === 'amex') {
            cardIcon.className = 'fab fa-cc-amex card-icon';
        } else {
            cardIcon.className = 'fas fa-credit-card card-icon';
        }
    }
}