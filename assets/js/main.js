// ============================================================
// main.js - UD Aneka Rupa Website
// ============================================================

// ---- Loading Overlay ----
window.addEventListener('load', function() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        setTimeout(() => overlay.classList.add('hidden'), 400);
    }
});

// ---- Navbar Scroll Effect ----
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

// ---- Scroll Reveal Animation ----
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -40px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', function() {
    // Observe elements with fade-in-up class
    document.querySelectorAll('.fade-in-up').forEach(el => {
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // Observe product cards
    document.querySelectorAll('.product-card').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `opacity 0.5s ease ${i * 0.08}s, transform 0.5s ease ${i * 0.08}s`;
        observer.observe(el);
    });

    // Observe feature cards
    document.querySelectorAll('.feature-card').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = `opacity 0.5s ease ${i * 0.1}s, transform 0.5s ease ${i * 0.1}s`;
        observer.observe(el);
    });
});

// ---- Quantity Control ----
function changeQty(action) {
    const input = document.getElementById('jumlah');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    const max = parseInt(input.max) || 999;
    if (action === 'plus') val = Math.min(val + 1, max);
    if (action === 'minus') val = Math.max(val - 1, 1);
    input.value = val;
    updateTotal();
}

function updateTotal() {
    const input = document.getElementById('jumlah');
    const priceEl = document.getElementById('harga_satuan');
    const totalEl = document.getElementById('total_display');
    if (!input || !priceEl || !totalEl) return;

    const qty = parseInt(input.value) || 1;
    const price = parseInt(priceEl.value) || 0;
    const total = qty * price;
    totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// ---- Form Validation ----
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let isValid = true;
    const fields = form.querySelectorAll('[required]');

    fields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validate phone number
    const phone = form.querySelector('[name="no_hp"]');
    if (phone && phone.value) {
        const phoneRegex = /^[0-9]{10,13}$/;
        if (!phoneRegex.test(phone.value.replace(/[-\s]/g, ''))) {
            phone.classList.add('is-invalid');
            isValid = false;
            showAlert('error', 'Nomor HP tidak valid', 'Masukkan nomor HP yang benar (10-13 digit angka)');
        }
    }

    if (!isValid) {
        // Only show general alert if no specific one was shown
        const hasPhoneError = phone && phone.classList.contains('is-invalid') && phone.value;
        if (!hasPhoneError) {
            showAlert('warning', 'Form Belum Lengkap', 'Harap isi semua kolom yang wajib diisi');
        }
    }

    return isValid;
}

// Remove invalid class on input
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('is-invalid')) {
        if (e.target.value.trim()) {
            e.target.classList.remove('is-invalid');
        }
    }
});

// ---- SweetAlert Helpers ----
function showAlert(icon, title, text = '', callback = null) {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonColor: '#F97316',
        confirmButtonText: 'OK'
    }).then(result => {
        if (callback && result.isConfirmed) callback();
    });
}

function showSuccess(title, text = '', callback = null) {
    Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        confirmButtonColor: '#F97316',
        timer: callback ? undefined : 2000,
        showConfirmButton: !!callback
    }).then(result => {
        if (callback) callback();
    });
}

function confirmDelete(formId) {
    Swal.fire({
        title: 'Hapus Data?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#78716C',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

// ---- Product Search Filter (client-side) ----
function filterProducts(keyword, kategori) {
    const cards = document.querySelectorAll('.product-card-wrap');
    let count = 0;

    cards.forEach(card => {
        const name = (card.dataset.name || '').toLowerCase();
        const cat = (card.dataset.kategori || '').toLowerCase();
        const kw = keyword.toLowerCase().trim();

        const matchKeyword = !kw || name.includes(kw);
        const matchKategori = !kategori || kategori === 'semua' || cat === kategori;

        if (matchKeyword && matchKategori) {
            card.style.display = '';
            count++;
        } else {
            card.style.display = 'none';
        }
    });

    // Show empty state
    const emptyState = document.getElementById('empty-state');
    if (emptyState) {
        emptyState.style.display = count === 0 ? 'block' : 'none';
    }
}

// ---- Auto dismiss alerts ----
setTimeout(function() {
    document.querySelectorAll('.alert-auto-dismiss').forEach(el => {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    });
}, 3500);

// ---- Tooltips ----
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));
});

// ---- Number input prevent non-numeric ----
document.addEventListener('keypress', function(e) {
    if (e.target.type === 'tel' || e.target.name === 'no_hp') {
        if (!/[\d\s+\-]/.test(e.key)) e.preventDefault();
    }
    if (e.target.type === 'number') {
        if (!/[\d]/.test(e.key)) e.preventDefault();
    }
});
