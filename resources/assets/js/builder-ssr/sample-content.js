/**
 * Data sampel untuk SSR builder blocks.
 *
 * Meniru bentuk props `LandingPageService::home()` (data Inertia di halaman
 * depan React) supaya komponen variant section (Mode{n}.jsx) bisa di-render
 * menjadi HTML statis via ReactDOMServer. Satu-satunya sumber kebenaran
 * konten section tetap React; file ini hanya "bahan isi" untuk preview blok.
 */

const img = (w = 640, h = 480, label = '') =>
    `data:image/svg+xml;utf8,` +
    encodeURIComponent(
        `<svg xmlns="http://www.w3.org/2000/svg" width="${w}" height="${h}"><rect width="100%" height="100%" fill="#e2e8f0"/><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="22" fill="#94a3b8" text-anchor="middle" dominant-baseline="middle">${label || 'Gambar'}</text></svg>`
    );

const LOGO = img(160, 48, 'Logo');

/** Ringkasan site/menu — mirror shared() */
const site = {
    name: 'Nama Bisnis Anda',
    title: 'Nama Bisnis Anda',
    description: 'Deskripsi singkat tentang bisnis Anda dalam satu kalimat.',
    tagline: 'Informasi, layanan, dan inovasi dalam satu ekosistem digital.',
    address: 'Jl. Contoh No. 1, Kota Anda',
    email: 'halo@example.com',
    phone: '+62 812 3456 7890',
    whatsapp: '6281234567890',
    logo: LOGO,
    logoNavbar: LOGO,
    logoFooter: LOGO,
    favicon: LOGO,
    homeUrl: '/',
    contactUrl: '#kontak',
    loginUrl: '#login',
    social: { facebook: '#', instagram: '#', linkedin: '#', youtube: '#' },
};

const menus = [
    { id: 'home', title: 'Beranda', url: '/', target: '_self' },
    { id: 'produk', title: 'Produk', url: '#produk', target: '_self' },
    { id: 'fitur', title: 'Fitur', url: '#fitur', target: '_self' },
    { id: 'contact', title: 'Hubungi Kami', url: '#kontak', target: '_self' },
];

const footerMenus = [...menus];

const sections = [
    'pageheader', 'navbar', 'topbar', 'hero', 'product', 'statistic',
    'feature', 'testimonial', 'client', 'faq', 'pengumuman', 'cta', 'price', 'footer',
].map((key, i) => ({
    landing_section_id: i + 1,
    section_key: key,
    section_name: key,
    area: i < 2 ? 'top' : i < 12 ? 'middle' : 'bottom',
    title: '',
    pre_title: '',
    post_title: '',
    subtitle: '',
    is_active: true,
    limit_data: 3,
    variant: null,
    settings: { text_align: 'center' },
}));

const landing = {
    hero: {
        title: 'Judul Hero Utama',
        subtitle: 'Subjudul singkat yang menjelaskan nilai utama',
        description: 'Deskripsi singkat tentang layanan atau produk Anda.',
        image: img(720, 540, 'Hero'),
        buttonPrimary: { text: 'Mulai Sekarang', link: '#kontak' },
        buttonSecondary: { text: 'Pelajari', link: '#fitur' },
        whatsapp: '6281234567890',
        microcopy: ['Respon cepat', 'Gratis konsultasi', 'Tanpa komitmen'],
    },
    features: [
        { id: 1, title: 'Cepat', description: 'Performa tinggi sejak awal.', icon: 'zap', image: img() },
        { id: 2, title: 'Aman', description: 'Data terlindungi dengan baik.', icon: 'shield', image: img() },
        { id: 3, title: 'Fleksibel', description: 'Mudah disesuaikan kebutuhan.', icon: 'sliders', image: img() },
    ],
    products: [
        { id: 1, name: 'Produk A', slug: 'produk-a', shortDescription: 'Deskripsi singkat produk A.', description: 'Deskripsi lengkap produk A.', image: img(640, 480, 'Produk A'), demoUrl: '#' },
        { id: 2, name: 'Produk B', slug: 'produk-b', shortDescription: 'Deskripsi singkat produk B.', description: 'Deskripsi lengkap produk B.', image: img(640, 480, 'Produk B'), demoUrl: '#' },
        { id: 3, name: 'Produk C', slug: 'produk-c', shortDescription: 'Deskripsi singkat produk C.', description: 'Deskripsi lengkap produk C.', image: img(640, 480, 'Produk C'), demoUrl: '#' },
    ],
    statistics: [
        { id: 1, label: 'Kepuasan', value: 99, icon: 'heart' },
        { id: 2, label: 'Klien Aktif', value: 120, icon: 'users' },
        { id: 3, label: 'Tahun Pengalaman', value: 10, icon: 'award' },
    ],
    clients: [
        { id: 1, name: 'Klien Satu', logo: img(240, 96, 'K1'), website: '#' },
        { id: 2, name: 'Klien Dua', logo: img(240, 96, 'K2'), website: '#' },
        { id: 3, name: 'Klien Tiga', logo: img(240, 96, 'K3'), website: '#' },
    ],
    cta: {
        title: 'Siap untuk memulai?',
        description: 'Bergabunglah bersama pengguna lain yang sudah terbantu.',
        buttonText: 'Hubungi Kami',
        buttonLink: '#kontak',
        backgroundImage: null,
    },
};

const slides = [
    { id: 1, title: 'Slide Satu', caption: 'Caption slide pertama.', image: img(1600, 700, 'Slide 1'), link: '#' },
    { id: 2, title: 'Slide Dua', caption: 'Caption slide kedua.', image: img(1600, 700, 'Slide 2'), link: '#' },
];

const announcements = [
    { id: 1, title: 'Pengumuman Satu', excerpt: 'Ringkasan pengumuman pertama.', type: 'Info', date: '10 Jan 2026', image: img(640, 400, 'Info 1'), url: '#' },
    { id: 2, title: 'Pengumuman Dua', excerpt: 'Ringkasan pengumuman kedua.', type: 'Kegiatan', date: '12 Jan 2026', image: img(640, 400, 'Info 2'), url: '#' },
    { id: 3, title: 'Pengumuman Tiga', excerpt: 'Ringkasan pengumuman ketiga.', type: 'Info', date: '14 Jan 2026', image: img(640, 400, 'Info 3'), url: '#' },
];

const faqs = [
    { id: 1, question: 'Bagaimana cara memulai?', answer: 'Daftar akun dan ikuti panduan yang tersedia.', category: 'Umum' },
    { id: 2, question: 'Apakah ada biaya tersembunyi?', answer: 'Tidak ada, harga sesuai paket pilihan.', category: 'Umum' },
];

const testimonials = [
    { id: 1, name: 'Andi Wijaya', position: 'Direktur', organization: 'PT Contoh', quote: 'Layanan yang luar biasa dan tepat waktu.', rating: 5, photo: img(96, 96, 'AW') },
    { id: 2, name: 'Siti Rahma', position: 'CEO', organization: 'CV Maju', quote: 'Sangat membantu pertumbuhan bisnis kami.', rating: 5, photo: img(96, 96, 'SR') },
];

const partners = [
    { id: 1, name: 'Partner Satu', category: 'Tech', url: '#', logo: img(240, 96, 'P1') },
    { id: 2, name: 'Partner Dua', category: 'Edukasi', url: '#', logo: img(240, 96, 'P2') },
];

const pages = [
    { id: 1, title: 'Tentang Kami', excerpt: 'Ringkasan halaman tentang.', url: '#' },
    { id: 2, title: 'Layanan', excerpt: 'Ringkasan halaman layanan.', url: '#' },
];

/**
 * Mirip `LandingPageService::home()` — sesuaikan bila shape berubah di sana.
 */
export const sampleProps = {
    template: 'builder',
    preview: true,
    design: {},
    themeOptions: [],
    site,
    seo: { title: site.name, description: site.tagline, keywords: '' },
    menus,
    footerMenus,
    sections,
    slides,
    announcements,
    faqs,
    testimonials,
    partners,
    pages,
    landing,
};