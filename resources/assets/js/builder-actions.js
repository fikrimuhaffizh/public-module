/**
 * Website Builder — action buttons (publish / unpublish / duplicate).
 *
 * Dipakai di halaman index halaman builder (DataTable). Button dengan
 * class `builder-post` akan: konfirmasi (SweetAlert2) → POST ke data-url
 * → reload DataTable di scope terdekat / redirect sesuai response.
 *
 * HTML:
 * <button class="builder-post"
 *         data-url="/cms/builder/pages/xxx/publish"
 *         data-confirm="Publikasikan halaman ini?"
 *         data-method="POST">
 */
import axios from 'axios';
import Swal from 'sweetalert2';

class BuilderPostHandler {
    init() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.builder-post');
            if (!btn) return;
            e.preventDefault();

            const url = btn.dataset.url;
            const method = btn.dataset.method || 'post';
            const title = btn.dataset.confirm || 'Apakah Anda yakin?';
            const text = btn.dataset.text || 'Tindakan ini akan dijalankan sekarang.';

            Swal.fire({
                title,
                text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (!result.isConfirmed) return;

                axios({
                    method,
                    url,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                    .then((response) => {
                        const msg = response.data?.message || 'Berhasil';
                        Swal.fire({
                            title: 'Berhasil',
                            text: msg,
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true,
                        });

                        if (response.data?.redirect) {
                            window.location.href = response.data.redirect;
                            return;
                        }

                        const scope = btn.closest('.card, .modal, .tab-pane');
                        if (window.reloadDataTablesIn) {
                            window.reloadDataTablesIn(scope || document);
                        } else if (window.reloadAllDataTables) {
                            window.reloadAllDataTables();
                        }
                    })
                    .catch((error) => {
                        const msg = error.response?.data?.message || 'Terjadi kesalahan.';
                        Swal.fire({ title: 'Kesalahan!', text: msg, icon: 'error' });
                    });
            });
        });
    }
}

window.BuilderPostHandler = new BuilderPostHandler();
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.BuilderPostHandler.init());
} else {
    window.BuilderPostHandler.init();
}