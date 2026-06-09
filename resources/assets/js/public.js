// Vendor JS Files
import '../vendor/bootstrap/js/bootstrap.bundle.min.js';
import '../vendor/php-email-form/validate.js';
import AOS from 'aos'; // Use AOS from npm
import 'aos/dist/aos.css'; // Import AOS CSS as well
import '../vendor/purecounter/purecounter_vanilla.js';
import '../vendor/glightbox/js/glightbox.min.js';
import '../vendor/swiper/swiper-bundle.min.js';
import '../vendor/drift-zoom/Drift.min.js';

/* Note: jQuery and Select2 were CDNs. They should be kept in Blade or installed via npm.
   However, SweetAlert2 was cross-referenced from assets-auth/libs.
*/



// Make AOS globally available
if (typeof window !== 'undefined') {
    window.AOS = AOS;
}

// Main JS File
import './main.js';

// Custom SweetAlert Utils
import './custom/sweetalert-utils.js';

// --- jQuery (dibutuhkan oleh DataTables dan plugin lama)
import $ from 'jquery';
window.$ = window.jQuery = $;

// --- Bootstrap 5 JS (untuk Modal, Dropdown, Tooltip, dll)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// --- SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;
