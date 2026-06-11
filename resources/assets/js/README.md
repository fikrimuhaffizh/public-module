# Public React Frontend

Frontend publik memakai React, Inertia, Tailwind CSS, dan komponen shadcn/ui. CMS tetap memakai Blade dan Tabler.

## Struktur

```text
js/
├── app.jsx                 # Entry Inertia dan registrasi halaman
├── pages/                  # Halaman yang dirender controller Inertia
├── layouts/                # Header, navigasi, footer, dan layout bersama
├── templates/              # Variasi landing page yang dapat dipilih dari CMS
├── components/
│   ├── ui/                 # Primitive hasil shadcn CLI
│   ├── sections/           # Section reusable berbasis data CMS
│   └── motion/             # Efek dan animasi reusable
└── lib/                    # Utility frontend
```

## Menambah Komponen shadcn

Jalankan CLI dari root aplikasi melalui container:

```bash
docker exec pemutu-app npx shadcn@latest add dialog
```

Komponen akan dibuat di `components/ui`. Kustomisasi primitive dilakukan di file tersebut. Import komponen secara langsung, misalnya:

```jsx
import { Button } from '@public/components/ui/button';
```

## Menambah Halaman

1. Buat komponen di `pages`.
2. Daftarkan komponen pada map `pages` di `app.jsx`.
3. Tambahkan route/controller Public yang memanggil `Inertia::render()`.
4. Tambahkan menu dari CMS bila halaman perlu masuk navigasi.

## Menambah Template Landing

1. Buat template baru di `templates`.
2. Daftarkan template pada map di `pages/Home.jsx`.
3. Tambahkan nilai template pada validasi dan pilihan pengaturan CMS.

Data CMS tetap disiapkan oleh backend. Template hanya menentukan presentasi dan tidak melakukan query data sendiri.
