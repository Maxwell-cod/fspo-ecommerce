// FSPO Ltd – Main JS

// Auto-hide flash messages
setTimeout(() => {
    const flash = document.getElementById('flashMsg');
    if (flash) flash.style.animation = 'slideIn .3s ease reverse';
    setTimeout(() => flash && flash.remove(), 300);
}, 4000);

// Cart quantity controls
function changeQty(btn, delta) {
    const input = btn.parentElement.querySelector('input[type="number"]');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 999) val = 999;
    input.value = val;
}

// Payment method toggle
document.addEventListener('DOMContentLoaded', () => {

    // Payment radio toggle
    const paymentOptions = document.querySelectorAll('.payment-option');
    const bankOptions = document.querySelector('.bank-options');

    paymentOptions.forEach(opt => {
        const radio = opt.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', () => {
                paymentOptions.forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
                if (bankOptions) {
                    bankOptions.classList.toggle('show', radio.value.startsWith('bank_'));
                }
            });
        }
    });

    // Pre-select if checked on load
    const checkedRadio = document.querySelector('.payment-option input[type="radio"]:checked');
    if (checkedRadio) {
        checkedRadio.closest('.payment-option').classList.add('selected');
        if (bankOptions && checkedRadio.value.startsWith('bank_')) bankOptions.classList.add('show');
    }

    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    // Image preview
    const imgInput = document.getElementById('imageInput');
    const imgPreview = document.getElementById('imagePreview');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', () => {
            const file = imgInput.files[0];
            if (file) {
                imgPreview.src = URL.createObjectURL(file);
                imgPreview.style.display = 'block';
            }
        });
    }

    // Animate stat numbers
    document.querySelectorAll('.stat-number').forEach(el => {
        const target = parseInt(el.dataset.target || el.textContent.replace(/\D/g, ''));
        if (isNaN(target)) return;
        let start = 0;
        const step = Math.ceil(target / 40);
        const timer = setInterval(() => {
            start += step;
            if (start >= target) { el.textContent = el.dataset.prefix + target.toLocaleString() + (el.dataset.suffix || ''); clearInterval(timer); }
            else el.textContent = el.dataset.prefix + start.toLocaleString() + (el.dataset.suffix || '');
        }, 30);
    });
});

// Add to cart AJAX helper
function addToCart(productId, qty = 1) {
    fetch('ajax/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=add&product_id=${productId}&qty=${qty}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.querySelector('.cart-badge');
            if (badge) badge.textContent = data.cart_count;
            else {
                const link = document.querySelector('.cart-link');
                if (link) link.insertAdjacentHTML('beforeend', `<span class="cart-badge">${data.cart_count}</span>`);
            }
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Error adding to cart', 'error');
        }
    });
}

function showToast(msg, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `flash flash-${type}`;
    toast.id = 'toastMsg';
    toast.innerHTML = msg + '<button onclick="this.parentElement.remove()">×</button>';
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.animation = 'slideIn .3s ease reverse'; setTimeout(() => toast.remove(), 300); }, 3000);
}
