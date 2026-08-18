# Pemutu — Template Development Rules

> Copy this entire file into your AI context when creating a new template.
> Every rule here reflects a real constraint in this codebase.

---

## KONTRAK MODE SECTION — SISTEM AKTIF (BACA DULU)

> Bagian bawah file ini (STRUKTUR FILE, TEMPLATE SKELETON, dst.) adalah sistem
> **template utuh** yang sudah legacy. Saat ini tema di `/preview` adalah
> **preset** (kombinasi section + warna + font + radius) yang dikustomisasi
> per-section dari Theme Settings. Titik ekstensi utama = **menambah mode baru
> pada sebuah section** — cukup satu file komponen, tanpa menyentuh registry.

---

### A. Kontrak penamaan file `Mode{n}.jsx`

Setiap mode adalah satu file JSX default-export dengan pola:

```
resources/assets/js/components/sections/<dir-section>/Mode{n}.jsx
```

- `Mode` = kata kunci **`Mode` + angka** — inilah penanda mode
- Nama section **tidak** ikut di nama file (folder section sudah mewakili)
- Satu folder = satu section; mode diurutkan otomatis dari angka
- Konvensi lama `X{n}V{m}.jsx` (mis. `FaqV4.jsx`) sudah diganti `Mode{n}.jsx`
  (mis. `Mode4.jsx`) — nama lama tidak dikenali lagi oleh auto-discovery

Contoh nyata di repo:

```
sections/faq/Mode1.jsx        → "Mode 1"
sections/faq/Mode4.jsx        → "Mode 4"
sections/hero/Mode1.jsx       → "Mode 1"
sections/hero/Mode2.jsx       → "Mode 2"
sections/hero/Mode3.jsx       → "Mode 3"
sections/topbar/Mode3.jsx     → "Mode 3"
```

**Inventaris mode Hero saat ini** (per repo ini):

| File | Mode | Desain |
|------|------|--------|
| `hero/Mode1.jsx` | Mode 1 | Split — teks di kiri, visual/browser mockup di kanan |
| `hero/Mode2.jsx` | Mode 2 | **Aurora** — latar gradien bergerak, judul muncul kata per kata (blob + word-stagger) |
| `hero/Mode3.jsx` | Mode 3 | Galeri dua kolom UMKM — teks kiri, foto produk kanan |

Mode aurora lama (dulu `hero/Mode4.jsx`) sudah **digabung menjadi Mode 2** —
jangan buat file `Mode4.jsx` untuk desain yang sama. Saat menambah mode baru,
ikuti nomor berikutnya yang tersedia (hero berikutnya = Mode 4).

**Yang terjadi otomatis** (frontend `registry.js` memakai `import.meta.glob`,
backend `config/landing_sections.php` memakai `glob()` — keduanya auto-scan
folder yang sama):

1. Muncul di dropdown **Theme Settings** (`/preview`) sebagai "Mode N"
2. Muncul di daftar variant CMS/admin
3. Ter-render oleh `SectionVariantRenderer`

**TIDAK perlu** mengedit `registry.js`, `renderer.jsx`, atau config — cukup
membuat file. File helper di folder yang sama (mis. `FaqReveal.jsx`,
`HeroActions.jsx`, `hours.js`, `CountUp.jsx`) aman **selama namanya tidak
memuat pola `Mode{digit}`** — otomatis diabaikan.

Mapping `section_key` → folder komponen:

| Section key (normalized) | Folder komponen        | Nama di UI           |
|--------------------------|------------------------|----------------------|
| `navbar`                 | `sections/navbar/`     | Navbar               |
| `topbar`                 | `sections/topbar/`     | Top Bar              |
| `hero`                   | `sections/hero/`       | Hero                 |
| `product`                | `sections/product/`    | Produk / Modul       |
| `statistic`              | `sections/statistic/`  | Statistik            |
| `feature`                | `sections/feature/`    | Fitur                |
| `testimonial`            | `sections/testimonial/`| Testimoni            |
| `client`                 | `sections/client/`     | Klien / Logo         |
| `faq`                    | `sections/faq/`        | FAQ                  |
| `pengumuman`             | `sections/announcement/`| Pengumuman          |
| `cta`                    | `sections/cta/`        | Call to Action       |
| `price`                  | `sections/price/`      | Harga / Paket        |
| `footer`                 | `sections/footer/`     | Footer               |

---

### A.2 Key variant — WAJIB canonical (tidak ada toleransi legacy)

Key variant dibangkitkan dari **canonical section key** (kolom pertama tabel
di atas) + nomor mode:

```
<canonical_section_key>_<nomor>   →   statistic_2, client_3, pengumuman_4, hero_2, faq_1
```

- **Frontend** `registry.js` membangkitkan key dari canonical
  (`variantsFromFolder(dir, sectionKey)`).
- **Backend** `config/landing_sections.php` memakai `autoVariants(dir, canonical)`
  dengan prefix yang sama — keduanya harus selalu konsisten.
- **TIDAK BOLEH** memakai prefix lama:

  | ❌ Legacy (sudah dimigrasi & dihapus) | ✅ Canonical (wajib) |
  |---|---|
  | `stats_N` | `statistic_N` |
  | `logos_N` | `client_N` |
  | `announcement_N` | `pengumuman_N` |

**`normalizeVariantKey` sudah DIHAPUS** dari `registry.js`, 
`ThemeCustomizerContext.jsx`, dan `ThemeSettingsDrawer.jsx`. Artinya:

1. Nilai legacy (`stats_1`, `logos_3`, `announcement_4`) di preset tema,
   baris DB, atau draft **tidak lagi di-normalisasi** → `resolveVariant`
   tidak menemukannya → **diam-diam jatuh ke Mode 1**.
2. Saat menambah preset tema baru, mengisi `default_variant`, atau menulis
   nilai variant apa pun — gunakan **selalu key canonical**.
3. Data yang sudah ada sudah dimigrasi ke canonical: `config/themes.php`,
   `cms_landing_sections` (migration `normalize_landing_section_keys`), dan
   `RefSeeder`. Jangan mengembalikan prefix lama.

Cek cepat bahwa nilai yang Anda tulis valid:

```bash
php artisan tinker --execute="print_r(array_keys(config('landing_sections.statistic.variants')));"
# → ["statistic_1", "statistic_2", "statistic_3"]  (bukan stats_N)
```

---

### B. Nama mode — cukup "Mode N"

Dropdown Theme Settings menampilkan **"Mode N"** apa adanya (mis. "Mode 4"),
tanpa teks deskripsi — seragam untuk semua section. Tidak ada parsing JSDoc
untuk nama mode.

JSDoc di atas komponen utama **tetap disarankan** sebagai dokumentasi internal
(pola `Mode {n} — deskripsi`), tapi tidak memengaruhi UI. Contoh:

```jsx
/** FAQ Mode 4 — accordion animasi: kartu membuka-tutup dengan transisi halus. Prop: { section, data } */
export default function FaqMode4({ section, data }) { ... }
```

---

### C. Kontrak elemen root (WAJIB)

Root elemen komponen variant **HARUS** berupa:

- `<section>` — untuk semua section konten
- `<header>` — untuk navbar & top bar
- `<footer>` — untuk footer

Alasannya konkret:

- Override warna per-section (`.sec-colored`) dan styling tema menembus lewat
  selektor CSS `section/header/footer`
- Root `<div>` → override warna & styling tema **tidak bekerja**
- Jangan bungkus root dengan `<div>` lain

Di mode dev, `SectionRootGuard` di `renderer.jsx` menulis warning ke konsol
saat root tidak sah — **selalu cek konsol setelah menambah mode baru**.
Wrapper bersama `<Section>` (dari `components/sections/LandingSections.jsx`)
diizinkan karena ia sendiri me-render `<section>` di DOM.

---

### D. Prop yang diterima

Konten section: `{ section, data }`

- `section` — objek section: `title`, `pre_title`, `subtitle`, `post_title`,
  `is_active`, `limit_data`, `variant`, `settings.text_align`
  (`'left'|'center'|'right'`), dst.
- `data` — props Inertia lengkap: `site`, `landing`, `announcements`, `faqs`,
  `testimonials`, `partners`, `pages`, `products`, `slides`, dst.

Layout section (navbar/footer/topbar) menerima prop yang berbeda — lihat file
yang sudah ada (`navbar/Mode1.jsx`, `topbar/Mode1.jsx`, `footer/Mode1.jsx`) sebagai acuan.

Gunakan helper yang sudah ada: `sectionHeading()` / `<Section>` untuk judul +
alignment, `heroCopy()` untuk hero, `combinedText()` untuk teks. Hormati
`section.is_active`, `section.limit_data`, dan `settings.text_align`.

---

### E. CSS — satu file per section

- Tambahkan style baru ke `resources/assets/css/sections/<section>.css`
  (mis. style FAQ Mode 5 → `sections/faq.css`)
- Daftarkan import-nya di agregator `resources/assets/css/landing.css` bila
  file belum ada di sana (urutan import = urutan cascade)
- Pakai class bersama agar knob tema tetap bekerja: `gen-card` (Radius &
  Elevasi), `.shell`, `.section`, variabel `--primary`, `--radius`, dst.

---

### E.2 Selang-seling latar section (--section-alt)

Section ganjil-transparan / genap-berlatar diselang-seling **otomatis di
semua tema** (bukan per-template):

- Rule ada di `resources/assets/css/base.css` (2 baris): section **genap**
  (urutan visual di halaman) mendapat `background: var(--section-alt, transparent)`
- `--section-alt` didefinisikan **per tema** di `css/themes/<tema>.css` pada
  blok `.theme-<key>` — nilainya varian latar yang senada tapi sedikit lebih
  dalam dari `--background` tema (contoh: corporate `#f0f2f6`, warung
  `#fff6ec`, aurora `#10131d`)
- Dua selektor karena struktur render beda: **prod** = section child langsung
  (`:where([class^="theme-"]) > section:nth-of-type(even)`); **dev** = section
  dibungkus `<span style="display:contents">` oleh `SectionRootGuard`
  (`> span:nth-of-type(even) > section`)
- Specificity `:where()` = (0,1,0) — sengaja rendah supaya kalah dari kelas
  ber-latar sendiri:

| Keadaan section | Perilaku |
|---|---|
| `.section--dark` / `.section--tint` / `.hero` / `.stats-band` | punya latar sendiri → **tidak** di-alternate (di-exclude via `:not()`) |
| `.sec-colored` (warna kustom dari Theme Settings) | **tidak** tersentuh — wrapper `<div>` di luar rule |
| `section` polos | kena alternation — genap → `--section-alt` |

**Aturan untuk developer:**

- Saat membuat mode baru, jangan beri latar sendiri lewat root `<section>`
  **kecuali memang perlu** — pakai class ber-latar yang sudah ada
  (`.section--tint`, `.hero`, `.stats-band`) atau biarkan polos agar ikut
  selang-seling.
- Saat menambah tema baru, **wajib** definisikan `--section-alt` di
  `css/themes/<key>.css`. Tanpa itu section genap tetap transparan (fallback
  `transparent`) — tidak rusak, hanya tidak selang-seling.

### F. Contoh end-to-end: tambah "Mode 5" untuk FAQ

**Langkah 1 — buat file** `resources/assets/js/components/sections/faq/Mode5.jsx`:

```jsx
import React from 'react';
import { Reveal } from '@public/components/motion/effects';
import { sectionHeading } from '../LandingSections';

/** FAQ Mode 5 — daftar dua kolom dengan jawaban ringkas. Prop: { section, data } */
export default function FaqMode5({ section, data }) {
    const items = (data.faqs || []).slice(0, section.limit_data || 8);
    const heading = sectionHeading(section, {
        eyebrow: section.pre_title || 'Pertanyaan umum',
        title: section.title || 'FAQ',
    });

    return (
        <section className="faq-split">          {/* root WAJIB <section> */}
            <div className="shell">
                <Reveal className={`section-heading section-heading--${heading.align}`}>
                    {heading.eyebrow && <span className="eyebrow">{heading.eyebrow}</span>}
                    {heading.title && <h2>{heading.title}</h2>}
                </Reveal>
                <div className="faq-split-grid">
                    {items.map((faq) => (
                        <article key={faq.id} className="gen-card faq-split-item">
                            <h3>{faq.question}</h3>
                            <p dangerouslySetInnerHTML={{ __html: faq.answer }} />
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
```

**Langkah 2 — CSS** (bila butuh style baru): tambah `.faq-split` / `.faq-split-grid`
ke `resources/assets/css/sections/faq.css`. Tidak ada registrasi lain.

**Langkah 3 — verifikasi (tanpa edit registry/config):**

1. Buka `/preview` → Theme Settings → section **FAQ** → dropdown berisi
   **"Mode 5"**
2. Pilih Mode 5 → section ter-render
3. Konsol bersih (tidak ada warning `[sections] ... seharusnya <section>`)
4. Backend ikut terdaftar — cek:
   ```bash
   php artisan tinker --execute="print_r(config('landing_sections.faq.variants'));"
   ```
   → memuat `faq_5 => "Mode 5"`

**Langkah 4 (opsional)** — jadikan default di tema tertentu: set `faq_5`
sebagai variant untuk section `faq` di preset tema (`config/themes.php`) atau
lewat Theme Settings → "Terapkan ke landing".

---

### G. Checklist mode baru

- [ ] Nama file `Mode{n}.jsx` di folder section-nya
- [ ] (Opsional) JSDoc `Mode {n} — deskripsi` sebagai dokumentasi internal
- [ ] Root elemen `<section>` / `<header>` / `<footer>` — bukan `<div>`
- [ ] Hormati `section.is_active`, `section.limit_data`, `settings.text_align`
- [ ] CSS di `css/sections/<section>.css` (+ import di `landing.css` bila perlu)
- [ ] Tidak menyentuh `registry.js` / `renderer.jsx` / `config/landing_sections.php`
- [ ] Konsol dev bersih dari warning root

---

## 1. STRUKTUR FILE — LEGACY (template utuh, pertahankan, tapi jangan untuk ekstensi baru)

Setiap template adalah **satu file JSX default export** di folder ini:
```
Modules/Public/resources/assets/js/templates/YourTemplate.jsx
```

Setelah membuat file, daftarkan di:
```js
// Modules/Public/resources/assets/js/pages/Home.jsx
const templates = {
  institutional: InstitutionalTemplate,
  modern: ModernTemplate,
  // ... tambahkan di sini:
  your_key: YourTemplate,
};
```

Key harus huruf kecil, bisa underscore. Key ini yang disimpan di DB (`landing_template`).

---

## 2. PROP YANG DITERIMA TEMPLATE

Template hanya menerima **satu prop: `data`**. Tidak ada prop lain.

```jsx
export default function YourTemplate({ data }) { ... }
```

### Shape `data` secara lengkap:

```js
data = {
  // ─── Metadata template ─────────────────────────────────────
  template: 'your_key',     // string — key template aktif
  preview: false,           // boolean — true jika sedang preview dari CMS

  // ─── Info site/tenant ──────────────────────────────────────
  site: {
    name: string,           // nama institusi
    tagline: string,        // slogan/deskripsi singkat
    address: string|null,
    email: string|null,
    phone: string|null,
    whatsapp: string|null,
    logo: string|null,      // URL logo (sudah melalui sys_media_url)
    favicon: string|null,
    homeUrl: string,        // route('public.index')
    contactUrl: string,     // route('public.contact')
    loginUrl: string,       // route('login')
    social: {
      facebook: string|null,
      instagram: string|null,
      linkedin: string|null,
      youtube: string|null,
    },
  },

  // ─── SEO ───────────────────────────────────────────────────
  seo: {
    title: string|null,
    description: string|null,
    keywords: string|null,
  },

  // ─── Menu navigasi ─────────────────────────────────────────
  menus: Array<{
    id: number,
    title: string,
    url: string,
    target: '_self'|'_blank',
  }>,

  // ─── Sections (WAJIB — urutan dari CMS) ───────────────────
  sections: Array<{
    landing_section_id: number,   // unique ID
    section_key: string,          // lihat daftar section keys di bawah
    section_name: string,
    area: string,
    title: string|null,           // judul section dari CMS
    pre_title: string|null,       // eyebrow/kicker text
    post_title: string|null,      // subtitle kedua
    subtitle: string|null,        // deskripsi
    is_active: boolean,           // WAJIB dihormati — skip jika false
    limit_data: number|null,      // max item yang ditampilkan
    variant: string|null,         // variant desain (dari CMS)
    settings: object|null,        // settings tambahan, termasuk text_align
    // settings.text_align: 'left'|'center'|'right'
  }>,

  // ─── Konten halaman ────────────────────────────────────────
  pages: Array<{
    id: number,
    title: string,
    excerpt: string,
    url: string,
  }>,

  announcements: Array<{
    id: number,
    title: string,
    excerpt: string,
    type: string,           // e.g. 'Pengumuman', 'Berita'
    date: string,           // sudah diformat (formatTanggalIndo)
    image: string,          // URL gambar cover
    url: string,
  }>,

  testimonials: Array<{
    id: number,
    name: string,
    position: string|null,
    organization: string|null,
    quote: string,
    rating: number,         // 1–5
    photo: string|null,     // URL foto
  }>,

  partners: Array<{
    id: number,
    name: string,
    category: string|null,
    url: string|null,       // website partner
    logo: string|null,      // URL logo
  }>,

  faqs: Array<{
    id: number,
    question: string,
    answer: string,         // HTML
    category: string|null,
  }>,

  slides: Array<{
    id: number,
    title: string|null,
    caption: string|null,
    image: string,
    link: string|null,
  }>,

  // ─── Data landing teragregasi ────────────────────────────────
  // landing tersedia di semua template
  landing: {
    hero: {
      title: string,
      subtitle: string|null,
      description: string,
      image: string|null,   // URL hero image
      buttonPrimary: { text: string, link: string },
      buttonSecondary: { text: string, link: string },
    },
    features: Array<{
      id: number,
      title: string,
      description: string|null,
      icon: string|null,    // CSS class icon (e.g. 'ti ti-star')
      image: string|null,
    }>,
    products: Array<{
      id: number,
      name: string,
      slug: string,
      shortDescription: string|null,
      description: string|null,
      image: string|null,
      demoUrl: string|null,
    }>,
    statistics: Array<{
      id: number,
      label: string,
      value: string,
      icon: string|null,
    }>,
    clients: Array<{
      id: number,
      name: string,
      logo: string|null,
      website: string|null,
    }>,
    cta: {
      title: string,
      description: string|null,
      buttonText: string|null,
      buttonLink: string|null,
      backgroundImage: string|null,
    } | null,
  },
}
```

---

## 3. SECTION KEYS — DAFTAR LENGKAP

`section_key` yang valid dan mapping alias-nya (dari `sectionKey()` helper):

| `section_key` di DB   | Resolved key (pakai di switch) | Keterangan                      |
|-----------------------|--------------------------------|---------------------------------|
| `hero`                | `hero`                         | Hero / banner utama             |
| `feature`             | `feature`                      | Fitur / keunggulan              |
| `product`             | `product`                      | Produk / modul                  |
| `statistic`           | `statistic`                    | Angka/statistik                 |
| `stats`               | `statistic`                    | Alias → `statistic`             |
| `testimonial`         | `testimonial`                  | Testimoni                       |
| `testimonials`        | `testimonial`                  | Alias → `testimonial`           |
| `client`              | `client`                       | Klien / logo klien              |
| `clients`             | `client`                       | Alias → `client`                |
| `pengumuman`          | `pengumuman`                   | Berita / pengumuman             |
| `announcement`        | `pengumuman`                   | Alias → `pengumuman`            |
| `faq`                 | `faq`                          | FAQ                             |
| `cta`                 | `cta`                          | Call To Action                  |
| `navbar`              | `navbar`                       | Navigasi                        |
| `footer`              | `footer`                       | Footer                          |

**Gunakan selalu `sectionKey(section)` untuk normalisasi**, bukan `section.section_key` langsung.

---

## 4. ATURAN WAJIB SAAT RENDER SECTION

```jsx
const renderSection = (section) => {
  // WAJIB: Skip section yang tidak aktif
  if (!section.is_active) return null;

  const key = section.landing_section_id; // WAJIB: gunakan sebagai React key
  
  switch (sectionKey(section)) {
    case 'hero': ...
    default: return null; // WAJIB: selalu ada default null
  }
};

return <>{sections.map(renderSection)}</>;
```

---

## 5. LIMIT DATA — WAJIB DIHORMATI

Setiap section bertipe data punya **`limit_data`** (kolom `cms_landing_sections.limit_data`)
— batas jumlah item yang boleh dirender. Mode **WAJIB** membaca dan menghormatinya;
jangan pernah render semua data atau hardcode jumlah.

### 5.1 Dari mana `limit_data` diatur

1. **Admin CMS** — form edit section: field "Style / Variant" + limit (validasi
   `nullable|integer|min:1|max:50` di `SectionController::updateSection`).
2. **Theme Settings offcanvas** (`/preview`, atau halaman asli saat login) — popover
   edit teks section menampilkan input **"Jumlah data tampil (dari X)"**:
   - `X` = jumlah total data nyata section itu (dari `dataCounts` di drawer).
   - `min 1`, `max X`; kosong = pakai default.
   - Perubahan **live** (`onApplyLive`) → saat "Terapkan ke landing" disimpan ke
     kolom `limit_data` via `LandingPageService::saveSectionSettings` (frontend
     camelCase dinormalisasi ke snake_case).

### 5.2 Section mana yang punya limit

Hanya section dengan flag **`limit: true`** di `SECTION_META` (`registry.js`):

| Section key | Folder | Default `limit_data` |
|-------------|--------|---------------------|
| `product` | `sections/product/` | 6 |
| `statistic` | `sections/statistic/` | 4 |
| `feature` | `sections/feature/` | 6 |
| `testimonial` | `sections/testimonial/` | 3 |
| `client` | `sections/client/` | 8 |
| `faq` | `sections/faq/` | 8 |
| `pengumuman` | `sections/announcement/` | 6 |

Default di atas = `default_limit` di `config/landing_sections.php`. Section
tanpa flag (navbar, topbar, hero, pageheader, cta, price, footer) **tidak**
mendapat input limit di drawer — jangan menambahkan field ini sendiri.

### 5.3 Cara membaca di komponen

Renderer menggabungkan override drawer ke **level atas** object `section`
(`renderer.jsx`): `mergedSection.limit_data`. Komponen menerima `{ section, data }`
dan harus membaca `section?.limit_data` dengan fallback default section-nya:

```jsx
// ✅ Benar
const limit = section?.limit_data || 6; // fallback = default_limit section
const items = data.slice(0, limit);

// ❌ Salah
data.map(...)              // render semua, mengabaikan limit CMS
data.slice(0, 6).map(...)  // hardcode, mengabaikan pengaturan user
```

Gunakan `useMemo` bila daftar besar dan limit sering berubah (live preview):

```jsx
const visible = useMemo(() => data.slice(0, limit), [data, limit]);
```

### 5.4 Checklist mode baru

- [ ] Baca `section?.limit_data || <default section>` (default mengikuti
      `default_limit` config).
- [ ] Slice data **sebelum** render (bukan hanya menyembunyikan via CSS).
- [ ] Jangan tambahkan input limit manual — flag `limit: true` di `registry.js`
      otomatis memunculkannya di drawer untuk section data.
- [ ] Mode yang menampilkan semua data (mis. marquee klien yang berulang) tetap
      pakai `limit` untuk daftar **unik** yang dirender, lalu duplikasi hanya
      untuk efek visual.

---

## 6. TEXT ALIGN — WAJIB DIHORMATI

Section heading harus mengikuti `section.settings?.text_align` (`'left'`|`'center'`|`'right'`).

**Gunakan helper dari `LandingSections.jsx`:**
```jsx
import { sectionHeading, headingAlignClass } from '@public/components/sections/LandingSections';

// Untuk Section component (otomatis):
<Section section={section} eyebrow="..." title="...">...</Section>

// Untuk heading manual:
const heading = sectionHeading(section, { eyebrow, title, text });
// heading.align → 'left'|'center'|'right'
// headingAlignClass(section) → 'section-heading--left' | '--center' | '--right'

<Reveal className={`section-heading ${headingAlignClass(section)}`}>
  <span className="eyebrow">{heading.eyebrow}</span>
  <h2>{heading.title}</h2>
</Reveal>
```

---

## 7. CSS THEME CLASS

Setiap template **harus** menambahkan class tema ke `<body>` agar CSS variables dan overrides bekerja.
Ini dilakukan **oleh SiteLayout** secara otomatis dari `data.template`.
Template **tidak perlu** menambahkan class tema sendiri.

CSS variables tema tersedia:
```css
--primary        /* warna utama */
--primary-dark
--foreground     /* warna teks */
--muted          /* teks sekunder */
--border
--card
--background
--radius
--section-alt   /* latar section genap (selang-seling otomatis, lihat E.2) */
```

CSS class tema yang sudah ada:
- `.theme-modern` — ungu/indigo
- `.theme-editorial` — coklat/oranye, font serif
- `.theme-corporate` — navy/gold
- `.theme-launch` — indigo gelap

Untuk template baru, buat file `resources/assets/css/themes/<key>.css` berisi blok tema
(dan daftarkan import-nya di `resources/assets/css/landing.css`):
```css
.theme-yourkey { --primary: #xxx; ... }
```

---

## 8. KOMPONEN YANG TERSEDIA

### Dari `@public/components/sections/LandingSections`
Komponen preset siap pakai (DIREKOMENDASIKAN untuk template baru):

```jsx
import {
  Section,              // wrapper section dengan heading otomatis
  NewsGrid,             // grid berita/pengumuman — perlu prop: announcements, section
  PagesGrid,            // grid halaman — perlu prop: pages, section
  TestimonialSection,   // grid testimoni — perlu prop: testimonials, section
  FaqSection,           // accordion FAQ — perlu prop: faqs
  CtaSection,           // CTA card — perlu prop: site, section
  PlatformOverview,     // overview fitur SaaS — perlu prop: site, image, pageCount, section
  ValueStrip,           // strip nilai/USP
  sectionKey,           // normalisasi section_key
  sectionHeading,       // baca heading dari section
  headingAlignClass,    // class CSS alignment
  heroCopy,             // bantu baca copy untuk hero
} from '@public/components/sections/LandingSections';
```

**Signature penting:**
```jsx
<Section
  section={section}       // WAJIB — untuk heading & align
  id="anchor-id"          // opsional
  eyebrow="fallback"      // fallback jika section.pre_title kosong
  title="fallback"        // fallback jika section.title kosong
  text="fallback"         // fallback jika section.subtitle/post_title kosong
  dark={false}            // background gelap
  tint={false}            // background abu terang
  narrow={false}          // max-width lebih sempit
>
  {/* konten section */}
</Section>

<NewsGrid announcements={data.announcements} section={section} />
<NewsGrid announcements={data.announcements} section={section} editorial />  {/* layout editorial */}
<PagesGrid pages={data.pages} section={section} />
<TestimonialSection testimonials={data.testimonials} section={section} />
<FaqSection faqs={data.faqs} />
<CtaSection site={data.site} section={section} />
```

### Dari `@public/components/motion/effects`
```jsx
import { Reveal, Stagger, SpotlightCard, BackgroundBeams, Marquee } from '@public/components/motion/effects';

<Reveal delay={0.1}>...</Reveal>        // fade+slide in saat scroll
<Stagger className="grid">...</Stagger> // reveal anak-anak berurutan
<SpotlightCard>...</SpotlightCard>      // card dengan efek spotlight hover
<BackgroundBeams />                     // beam cahaya background (light beam)
<Marquee items={['item1', 'item2']} />  // ticker/marquee horizontal
```

### Dari `@public/components/ui/`
```jsx
import { Button } from '@public/components/ui/button';
import { Badge } from '@public/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@public/components/ui/card';
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@public/components/ui/accordion';
```

### Navigasi
```jsx
import { Link } from '@inertiajs/react';
// Gunakan Link untuk navigasi internal, <a> untuk eksternal
```

---

## 9. CSS CLASSES YANG SUDAH ADA

Gunakan class global ini — **jangan redefinisi**:

```css
.shell              /* container max-width 1180px, centered */
.shell--narrow      /* container max-width 840px */
.section            /* padding: 90px 0 */
.section--dark      /* background gelap (#091b31) */
.section--tint      /* background abu terang (#f5f8ff) */
.section-heading    /* wrapper heading, max-width 720px */
.section-heading--left    /* text-align: left */
.section-heading--center  /* text-align: center, centered */
.section-heading--right   /* text-align: right */
.eyebrow            /* label kecil uppercase di atas judul */
.feature-grid       /* CSS grid 3 kolom untuk feature cards */
.feature-card       /* card fitur dengan hover effect */
.news-grid          /* CSS grid 3 kolom untuk berita */
.news-card          /* card berita */
.partner-cloud      /* grid logo partner */
.testimonial-grid   /* grid testimoni */
.testimonial-card   /* card testimoni */
.hero-actions       /* flex container untuk tombol hero */
.text-link          /* link dengan panah, warna primary */
.site-header        /* header sticky dengan backdrop blur */
.site-footer        /* footer gelap */
.brand              /* container logo+nama site */
.value-strip        /* strip fitur/nilai horizontal */
```

---

## 10. HERO COPY HELPER

Untuk section hero, gunakan `heroCopy()` yang sudah menghandle override dari CMS:

```jsx
const copy = heroCopy(section, hero, data.site);
// copy.title       — judul (dari section atau hero atau site.name)
// copy.eyebrow     — pre_title
// copy.subtitle    — subtitle
// copy.description — post_title atau hero.description
// copy.imageAlt    — alt text untuk gambar
// copy.align       — 'left'|'center'|'right'
// copy.alignClass  — 'section-heading--left' dll.
```

---

## 11. TEMPLATE SKELETON

```jsx
// Modules/Public/resources/assets/js/templates/YourTemplate.jsx

import React from 'react';
import { ArrowRight } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { Button } from '@public/components/ui/button';
import { Reveal } from '@public/components/motion/effects';
import {
  CtaSection,
  FaqSection,
  NewsGrid,
  PagesGrid,
  Section,
  TestimonialSection,
  heroCopy,
  sectionKey,
} from '@public/components/sections/LandingSections';

export default function YourTemplate({ data }) {
  const sections = data.sections || [];
  const hero = data.landing?.hero;

  const renderSection = (section) => {
    if (!section.is_active) return null;
    const key = section.landing_section_id;
    const copy = heroCopy(section, hero, data.site);

    switch (sectionKey(section)) {
      case 'hero':
        return (
          <section key={key} className="your-hero">
            <div className="shell">
              <Reveal>
                <h1>{copy.title}</h1>
                <p>{copy.description}</p>
              </Reveal>
            </div>
          </section>
        );

      case 'feature':
        return (
          <Section key={key} section={section}
            eyebrow={section.pre_title || 'Fitur'}
            title={section.title || 'Keunggulan kami'}
          >
            {/* render landing.features.slice(0, section.limit_data || 6) */}
          </Section>
        );

      case 'product':
        return (
          <Section key={key} section={section}
            eyebrow={section.pre_title || 'Produk'}
            title={section.title || 'Layanan kami'}
          >
            <PagesGrid pages={data.pages} section={section} />
          </Section>
        );

      case 'client':
        // Klien / Logo — pakai komponen variant section (sections/client/Mode{n}.jsx)
        // lewat SectionVariantRenderer; helper legacy PartnerCloud sudah dihapus.
        return null;

      case 'testimonial':
        return <TestimonialSection key={key} testimonials={data.testimonials} section={section} />;

      case 'pengumuman':
        return (
          <Section key={key} section={section}
            eyebrow={section.pre_title || 'Berita'}
            title={section.title || 'Kabar terbaru'}
          >
            <NewsGrid announcements={data.announcements} section={section} />
          </Section>
        );

      case 'faq':
        return (
          <Section key={key} section={section} narrow
            eyebrow={section.pre_title || 'FAQ'}
            title={section.title || 'Pertanyaan umum'}
          >
            <FaqSection faqs={data.faqs} />
          </Section>
        );

      case 'cta':
        return <CtaSection key={key} site={data.site} section={section} />;

      default:
        return null;
    }
  };

  return <>{sections.map(renderSection)}</>;
}
```

---

## 12. CHECKLIST SEBELUM SELESAI

- [ ] File ada di folder `templates/`
- [ ] Key template didaftarkan di `Home.jsx`
- [ ] Setiap `renderSection` dimulai dengan `if (!section.is_active) return null`
- [ ] `key` React menggunakan `section.landing_section_id`
- [ ] Semua list data menggunakan `section.limit_data` (dengan fallback)
- [ ] Heading section menggunakan `sectionHeading()` atau `Section` component (bukan hardcode)
- [ ] Text alignment mengikuti `section.settings?.text_align` (via `headingAlignClass()` atau `Section`)
- [ ] CSS tema baru ditambahkan di `resources/assets/css/themes/<key>.css` (import di `landing.css`) dengan prefix `.theme-yourkey`
- [ ] `default: return null` ada di switch
- [ ] `variant` dari section dapat digunakan untuk kondisional rendering
