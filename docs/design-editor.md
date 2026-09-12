# Landing design and editor

Publishing requires `public.cms.settings.update` and a resolved tenant.
`SaveDesignRequest` validates nested section settings and available variants.
The canonical template is `cms_landing_page_settings.design.template`; the old
tenant configuration remains a read fallback until a design/template is saved.
Design and section updates use one database transaction. Publishing never
implicitly deletes uploaded backgrounds used by another template or draft.

Drafts are scoped by tenant, account, and template. `ThemeEditor` downloads the
drawer only when an authorized editor opens it. Inertia pages and section modes
use dynamic imports. Templates share `SectionTemplate`; presets determine their
appearance. `SectionEditPopover` owns section editing; the drawer owns selection
and publishing. Recipe modes use named section keys rather than array positions.

CSS still uses one combined stylesheet: tokens/base, theme and section styling,
customization, then the shared `refined.css` composition layer. Lazy JavaScript
does not imply per-mode CSS loading. Add new static styles to the relevant asset,
not to Blade; keep runtime palette values as CSS variables.

Run `node --test Modules/Public/tests/Frontend/design-system.test.mjs` and
`php vendor/phpunit/phpunit/phpunit Modules/Public/tests/Feature/DesignPersistenceTest.php`
from the project root. The integration checks use isolated SQLite connections.
