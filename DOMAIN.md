# Public Module — Domain Knowledge

## Purpose
CMS (Content Management System) and public-facing landing page — manages website content including announcements, pages, navigation menus, slideshows, and landing page sections.

## Key Entities

| Entity | Description |
|--------|-------------|
| `Pengumuman` | Announcement/news article. Has title, content, image, publish date, type (pengumuman/berita). |
| `Page` | Static CMS page with title, slug, content (rich text). |
| `Menu` | Public navigation menu item — ordered tree structure with URL links. |
| `Slideshow` | Hero/carousel slideshow with image, title, caption, link. Ordered. |
| `FAQ` | Frequently asked questions — question/answer pairs, ordered. |
| `Feature` | Landing page feature highlight — icon, title, description. Ordered. |
| `Product` | Product/service showcase item. Ordered. |
| `Statistic` | Counter/statistic display (number, label, icon). Ordered. |
| `Client` | Client/partner logo display. Ordered. |
| `Testimonial` | Client testimonial with name, photo, quote. Ordered. |
| `Partner` | Partner logo/link. Ordered. |
| `Cta` | Call-to-action section. |
| `HeroSection` | Hero/banner section for landing page. |
| `LandingSection` | Configurable landing page section — toggleable, reorderable. |
| `LandingPageSetting` | Global landing page settings (colors, fonts, SEO). |

## Two Areas

### 1. CMS Admin (`/cms/*`)
- CRUD for all content entities
- Drag-and-drop reordering for ordered items
- Landing page section management (toggle on/off, reorder)
- Template-based landing page editor

### 2. Public Website (`/public/*`)
- Landing page with all published content
- News/announcement listing and detail pages
- Static pages by slug (`/public/page/{slug}`)
- Contact form (rate-limited)
- Preview mode for admins

## Business Rules
- **Ordered content**: Most entities support drag-and-drop `reorder` endpoints for custom ordering.
- **Berita vs Pengumuman**: Both use `Pengumuman` model but with different `type` values. Berita has its own route group.
- **Landing page sections**: Sections are configurable via `LandingSection` — admin can toggle visibility and reorder.
- **Public routes use Inertia**: Public pages use `HandleInertiaRequests` middleware (React/Vue SPA).
- **Contact form**: Rate-limited to 5 submissions per minute.
- **Preview mode**: Admin can preview the landing page before publishing.

## Services
- `LandingSettingsService` — landing page section management
- `PengumumanService` — news/announcement CRUD
- `PageService` — static page management
- `MenuService` — public navigation management
- `SlideshowService` — hero carousel management
- Various CRUD services for Feature, Product, FAQ, Client, etc.

## Route Prefix
- Admin: `/cms/*` — requires `auth` + `check.expired` + `module:public`
- Public: `/public/*` — no auth (uses Inertia middleware)

## Permission Pattern
`public.cms.{resource}.{action}` (e.g., `public.cms.faq.create`, `public.cms.menu.reorder`)
