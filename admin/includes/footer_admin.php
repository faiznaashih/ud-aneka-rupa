    </div><!-- end admin-content -->
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert helpers
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
    }).then(r => { if (r.isConfirmed) document.getElementById(formId).submit(); });
}

// Auto-show SweetAlert from session flash messages
<?php if (isset($_SESSION['flash'])): ?>
const flash = <?= json_encode($_SESSION['flash']) ?>;
Swal.fire({
    icon: flash.type,
    title: flash.title,
    text: flash.text || '',
    confirmButtonColor: '#F97316',
    timer: flash.type === 'success' ? 2500 : undefined,
    showConfirmButton: flash.type !== 'success'
});
<?php unset($_SESSION['flash']); endif; ?>

// Image preview
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const el = document.getElementById(previewId);
            if (el) { el.src = e.target.result; el.style.display = 'block'; }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
