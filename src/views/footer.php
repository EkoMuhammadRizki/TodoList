<!-- Content ends here -->
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Cek Pesan Flash dari Session (di-set via PHP)
    <?php 
    $flash = get_flash(); 
    if ($flash): 
    ?>
    Swal.fire({
        icon: '<?= $flash['type'] ?>', // 'success', 'error', 'warning', 'info'
        title: '<?= ucfirst($flash['type']) ?>',
        text: '<?= $flash['message'] ?>'
    });
    <?php endif; ?>

    function confirmLogout(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Apakah anda yakin ingin log out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= BASE_URL ?>logout.php';
            }
        });
    }
</script>

</body>
</html>
