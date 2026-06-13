# Pemutu — Template Development Rules

> Copy this entire file into your AI context when creating a new template.
> Every rule here reflects a real constraint in this codebase.

---

## 1. STRUKTUR FILE

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

Setiap section punya `limit_data` yang diatur dari CMS. **Selalu slice data dengan nilai ini.**

```jsx
// ✅ Benar
const limit = section.limit_data || 6; // fallback default yang masuk akal
items.slice(0, limit).map(...)

// ❌ Salah
items.map(...)            // tidak ada limit
items.slice(0, 6).map(...) // hardcode, mengabaikan CMS setting
```

Default yang direkomendasikan per section type:
- `hero`: tidak berlaku
- `feature`: 6
- `product`: 6  
- `statistic`: 4
- `testimonial`: 6 (atau 3 untuk preset)
- `client`/`partner`: semua (null fallback)
- `pengumuman`: 6
- `faq`: 8

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
```

CSS class tema yang sudah ada:
- `.theme-modern` — ungu/indigo
- `.theme-editorial` — coklat/oranye, font serif
- `.theme-corporate` — navy/gold
- `.theme-launch` — indigo gelap

Untuk template baru, tambahkan blok CSS di `landing.css`:
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
  PartnerCloud,         // logo cloud partner — perlu prop: partners, section
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
<PartnerCloud partners={data.partners} section={section} />
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
  PartnerCloud,
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
        return <PartnerCloud key={key} partners={data.partners} section={section} />;

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
- [ ] CSS tema baru ditambahkan di `landing.css` dengan prefix `.theme-yourkey`
- [ ] `default: return null` ada di switch
- [ ] `variant` dari section dapat digunakan untuk kondisional rendering
