/**
 * Registri komponen tema (frontend).
 *
 * Menambah tema baru:
 *   1. Buat file di folder ini (default export, terima prop `data`).
 *   2. Daftarkan di bawah — key HARUS sama dengan key di
 *      Modules/Public/config/themes.php (backend).
 *   3. Tambahkan file `resources/assets/css/themes/<key>.css` (blok `.theme-<key>`)
 *      lalu daftarkan @import-nya di resources/assets/css/landing.css.
 *
 * Metadata tema (nama, kategori, deskripsi, ikon) datang dari backend via
 * prop Inertia `themeOptions` — jangan duplikasi label di sini.
 */
import ModernTemplate from './ModernTemplate';
import EditorialTemplate from './EditorialTemplate';
import CorporateTemplate from './CorporateTemplate';
import LaunchTemplate from './LaunchTemplate';
import AuroraTemplate from './AuroraTemplate';
import EnterpriseTemplate from './EnterpriseTemplate';
import RegistrationTemplate from './RegistrationTemplate';
import ProfileTemplate from './ProfileTemplate';
import CampusTemplate from './CampusTemplate';
import AdmissionsTemplate from './AdmissionsTemplate';
import TracerTemplate from './TracerTemplate';

// ── UMKM ──────────────────────────────────────────────────────────────
// Tema kurasi tangan (komponen khusus):
// Tema hasil CLI public:generate-themes — SATU komponen generik (data-driven).
// Layout & font dibaca dari metadata tema (prop Inertia themeOptions), jadi
// menambah tema TIDAK perlu menulis komponen baru.
import UmkmGenericTemplate from './UmkmGenericTemplate';

export const templateComponents = {
    // Institusi / platform
    modern: ModernTemplate,
    editorial: EditorialTemplate,
    corporate: CorporateTemplate,
    launch: LaunchTemplate,
    aurora: AuroraTemplate,
    enterprise: EnterpriseTemplate,
    registration: RegistrationTemplate,
    profile: ProfileTemplate,
    campus: CampusTemplate,
    admissions: AdmissionsTemplate,
    tracer: TracerTemplate,

};

export function resolveTemplate(key) {
    if (templateComponents[key]) {
        return templateComponents[key];
    }
    // Tema UMKM hasil generator: data-driven, tanpa komponen per tema.
    if (key && key.startsWith('umkm_')) {
        return UmkmGenericTemplate;
    }
    return ModernTemplate;
}
