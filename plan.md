# AI Implementation Request: Full Customize Landing Page Feature for Modu Public Project

## Core Project Constraints (Mandatory for All Code)
These rules apply to every component you build:
1. **Existing Table Priority**: If there is a conflict between the table names in this plan and tables already present in the project module, use the existing module tables
2. **No Over-Engineering**: Build simple, maintainable code that is easy to debug and update long-term
3. **UI Standard**: Use shadcn/ui exclusively, with design reference from https://www.launchuicomponents.com/
4. **Up-to-Date Data**: Use context7 to pull the latest project data to ensure alignment with current codebase
5. **Legacy Compatibility**: All existing static templates must remain fully functional and unchanged

---

## 1. Core Concept
Add a new dynamic template option to the existing list of static landing page templates:
Existing static templates to preserve:
* Corporate Template
* Editorial Template
* Institutional Template
* Launch Template
* Modern Template

New dynamic template to build:
* Full Customize Template (for admins to build custom landing pages from scratch)

---

## 2. Section Area Structure (Fixed Rules for All Use Cases)
Split all custom landing pages into 3 non-interchangeable content areas with strict behavior rules:

### TOP Area (Fixed position, cannot be moved to Middle/Bottom)
Fixed order: Navbar → Hero
Allowed components:
* Navbar
* Hero
Core properties:
- Fixed sequence that cannot be reordered
- Each component supports multiple design variants
- Cannot be dragged to other areas

### MIDDLE Area (Only area that supports reordering)
Allowed components:
* Bento Grid
* Carousel
* FAQ
* Feature
* Gallery
* Items
* Logos / Client / Partner
* Pricing
* Social Proof
* Stats
* Tabs
* Testimonials
* Product
* Pengumuman (Announcements)
Core properties:
- Full drag-and-drop reordering
- Toggle active/inactive status
- Select design variant
- Edit section title and subtitle
- Set limit for number of items to display

### BOTTOM Area (Fixed position, cannot be moved to Top/Middle)
Fixed order: CTA → Footer
Allowed components:
* CTA
* Footer
Core properties:
- Fixed sequence that cannot be reordered
- Toggle active/inactive status
- Select design variant
- Cannot be dragged to other areas

---

## 3. Database Implementation Plan
### New Table: `landing_sections` (only add this table if it does not conflict with existing tables)
Table columns:
| Column Name | Type | Required | Description |
|-------------|------|----------|-------------|
| id | Primary Key | ✅ | Auto-incrementing ID |
| section_key | String | ✅ | Unique machine-readable identifier (e.g. `navbar`, `hero`) |
| section_name | String | ✅ | Human-readable name for admin panel |
| area | Enum: `top`, `middle`, `bottom` | ✅ | Which area the section belongs to |
| component_name | String | ✅ | Base React component name to load |
| variant | String | ✅ | Selected design variant (e.g. `navbar_1`) |
| title | String | ❌ | Custom section title set by admin |
| subtitle | String | ❌ | Custom section subtitle set by admin |
| description | Text | ❌ | Optional section description |
| sort_order | Integer | ✅ | Order of section within its area |
| limit_data | Integer | ❌ | Max number of items to display in the section |
| is_active | Boolean | ✅ | Toggle section visibility |
| settings | JSON | ❌ | Flexible additional configuration for the section |
| created_at | Timestamp | ✅ | Auto-set creation time |
| updated_at | Timestamp | ✅ | Auto-set update time |

### Sample Default Seed Data for `landing_sections`
| section_key  | area   | component_name     | variant       | sort_order | is_active |
| ------------ | ------ | ------------------ | ------------- | ---------- | --------- |
| navbar       | top    | NavbarSection      | navbar_1      | 1          | 1         |
| hero         | top    | HeroSection        | hero_2        | 2          | 1         |
| stats        | middle | StatsSection       | stats_1       | 1          | 1         |
| products     | middle | ProductSection     | product_2     | 2          | 1         |
| features     | middle | FeatureSection     | feature_1     | 3          | 1         |
| testimonials | middle | TestimonialSection | testimonial_2 | 4          | 1         |
| clients      | middle | ClientSection      | logo_1        | 5          | 1         |
| cta          | bottom | CtaSection         | cta_1         | 1          | 1         |
| footer       | bottom | FooterSection      | footer_1      | 2          | 1         |

---

## 4. Design Variant Library
All components must support these initial variants. Add more variants only after the core system is stable:
### Navbar
* `navbar_1`: Simple base navbar
* `navbar_2`: Navbar with integrated CTA button
* `navbar_3`: Centered menu layout

### Hero
* `hero_1`: Text left, image right layout
* `hero_2`: Centered hero content
* `hero_3`: Hero with full-width background image

### Feature
* `feature_1`: 3-column grid layout
* `feature_2`: Icon card layout
* `feature_3`: Alternating image + text layout

### Product
* `product_1`: Card grid layout
* `product_2`: Horizontal showcase layout
* `product_3`: Tabbed product selector

### Stats
* `stats_1`: Simple number counter
* `stats_2`: Statistic card layout
* `stats_3`: Statistics with custom background

### Testimonials
* `testimonial_1`: Card grid layout
* `testimonial_2`: Carousel layout
* `testimonial_3`: Single highlighted testimonial

### Client / Logos
* `logos_1`: Logo grid layout
* `logos_2`: Logo carousel
* `logos_3`: Grayscale logo layout

### CTA
* `cta_1`: Simple centered CTA
* `cta_2`: CTA with background image
* `cta_3`: Split screen CTA

### Footer
* `footer_1`: Simple base footer
* `footer_2`: Full footer with navigation menus
* `footer_3`: Footer with contact info and social media links

---

## 5. Admin Panel Additions
Add this new menu structure under the existing "Landing Page" main menu:
