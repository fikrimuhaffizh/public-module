import { DESIGN_FAMILIES } from './design-system';
import SectionEditPopover from './SectionEditPopover';
import React, { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    Check,
    Dice5,
    Palette,
    Pencil,
    RotateCcw,
    Settings2,
    X,
} from 'lucide-react';
import { useThemeCustomizer } from './ThemeCustomizerContext';
import {
    DENSITY_OPTIONS,
    ELEVATION_OPTIONS,
    RADIUS_OPTIONS,
    randomizeTheme,
} from './presets';
import { SECTION_VARIANTS, sectionVariants } from '../sections/registry';

/**
 * Canonical section key — server memakai alias/plural (products, stats,
 * testimonials, announcement, …), registry memakai key tunggal.
 */
function canonicalOf(section) {
    const key = section?.section_key || section?.key || '';
    const map = {
        products: 'product',
        stats: 'statistic',
        features: 'feature',
        testimonials: 'testimonial',
        clients: 'client',
        announcement: 'pengumuman',
    };
    return map[key] || key;
}

/** Field warna (color picker + hex) untuk popover latar section. */
function ChipGroup({ title, options, value, onSelect, icon, hint }) {
    return (
        <div className="theme-opt">
            <h4>{icon}{title}</h4>
            <div className="theme-chip-row">
                {options.map((opt) => (
                    <button
                        key={opt.key}
                        type="button"
                        className={`theme-chip${value === opt.key ? ' active' : ''}`}
                        onClick={() => onSelect(opt.key)}
                    >
                        {opt.label}
                    </button>
                ))}
            </div>
            {hint && <span className="theme-select-hint">{hint}</span>}
        </div>
    );
}

/** LabelSelect — dropdown dengan judul + hint (mis. Tipografi). */
function LabelSelect({ title, icon, options, value, onSelect, hint }) {
    return (
        <div className="theme-opt">
            <h4>{icon}{title}</h4>
            <select
                className="theme-select"
                value={value || ''}
                onChange={(e) => onSelect(e.target.value)}
                aria-label={title}
            >
                {options.map((opt) => (
                    <option key={opt.key} value={opt.key}>{opt.name || opt.label}</option>
                ))}
            </select>
            {hint && <span className="theme-select-hint">{hint}</span>}
        </div>
    );
}

/** ThemeSelect — dropdown tema (daftar flat, tanpa grouping). */
function ThemeSelect() {
    const { template, themeOptions = {} } = usePage().props;
    const changeTheme = (e) => {
        const url = new URL(window.location.href);
        url.searchParams.set('template', e.target.value);
        router.visit(url.pathname + url.search, { preserveState: false, preserveScroll: true });
    };

    return (
        <div className="theme-select-wrap">
            <select
                className="theme-select"
                value={template}
                onChange={changeTheme}
                aria-label="Pilih tema"
            >
                {Object.entries(themeOptions || {}).map(([key, meta]) => (
                    <option key={key} value={key}>{meta.name || key}</option>
                ))}
            </select>
        </div>
    );
}



/**
 * LogoUploader — upload logo Navbar/Footer langsung dari Theme Settings.
 * Endpoint: POST /cms/section/upload-logo + DELETE /cms/section/delete-logo/{collection}
 * (perlu permission public.cms.update — hanya tampil untuk user login CMS).
 * Setelah sukses, partial reload prop `site` agar navbar/footer ter-render
 * dengan logo baru tanpa me-refresh seluruh halaman.
 */
/**
 * ThemeSettingsDrawer — offcanvas Theme Settings dua mode:
 * Landing Page (halaman depan) & Detail Page (halaman dalam).
 * Di /preview selalu tampil; di halaman asli hanya untuk user login CMS.
 * Daftar section difilter sesuai konteks halaman — hanya yang relevan.
 */
export function ThemeSettingsDrawer() {
    const customizer = useThemeCustomizer();
    if (!customizer) return null;

    const {
        isOpen, setOpen,
        custom, update, reset,
        paletteOptions, fontOptions,
    } = customizer;

    const [openEditKey, setOpenEditKey] = useState(null);
    const [dragIndex, setDragIndex] = useState(null);
    const [overIndex, setOverIndex] = useState(null);
    const [overAfter, setOverAfter] = useState(false);
    const [savingReorder, setSavingReorder] = useState(false);
    const [saving, setSaving] = useState(false);
    const [saveMsg, setSaveMsg] = useState(null);

    const saveReorder = async (order) => {
        setSavingReorder(true);
        try {
            const response = await fetch('/cms/section/reorder-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ order: order.map((s) => ({ id: String(s.id), area: s.area })) }),
            });
            if (!response.ok) throw new Error('Urutan gagal disimpan.');
            router.reload({ only: ['sections'] });
        } catch (e) {
            console.error('reorder section gagal', e);
            setSaveMsg('Urutan gagal disimpan. Periksa izin dan koneksi Anda.');
        } finally {
            setSavingReorder(false);
            setDragIndex(null);
            setOverIndex(null);
            setOverAfter(false);
        }
    };

    const commitDrop = () => {
        if (dragIndex === null || overIndex === null) {
            setDragIndex(null);
            setOverIndex(null);
            setOverAfter(false);
            return;
        }
        let insertAt = overIndex + (overAfter ? 1 : 0);
        if (dragIndex === insertAt || dragIndex === insertAt - 1) {
            setDragIndex(null);
            setOverIndex(null);
            setOverAfter(false);
            return;
        }
        const next = [...visibleSections];
        const [moved] = next.splice(dragIndex, 1);
        if (dragIndex < insertAt) insertAt -= 1;
        next.splice(insertAt, 0, moved);
        // Pertahankan posisi section yang tidak tampil (mis. Page Header saat
        // di landing) agar urutan global tidak rusak oleh reorder terfilter.
        const visibleIds = new Set(visibleSections.map((i) => i.id));
        let vi = 0;
        const fullOrder = sectionsList.map((item) =>
            visibleIds.has(item.id) ? next[vi++] : item);
        saveReorder(fullOrder);
    };

    // Tutup popover saat klik di luar area popover.
    useEffect(() => {
        if (!openEditKey) return;
        const handler = (e) => {
            if (!e.target.closest('[data-sec-color-pop]')) setOpenEditKey(null);
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, [openEditKey]);

    const { template, sections: rawSections = [], landing = {}, testimonials = [], announcements = [], faqs = [], page = null, announcement = null, header = null } = usePage().props;
    const dataCounts = {
        product: landing.products?.length || 0,
        statistic: landing.statistics?.length || 0,
        feature: landing.features?.length || 0,
        client: landing.clients?.length || 0,
        testimonial: testimonials?.length || 0,
        pengumuman: announcements?.length || 0,
        faq: faqs?.length || 0,
    };

    const sectionsList = rawSections
        .map((s) => ({ ...s, canonical: canonicalOf(s) }))
        .filter((s) => SECTION_VARIANTS[s.canonical])
        .map((s) => ({
            key: s.canonical,
            id: s.landing_section_id,
            area: s.area,
            active: s.is_active,
            name: s.section_name || SECTION_VARIANTS[s.canonical].name,
            current: s.variant,
            options: sectionVariants(s.canonical),
            preTitle: s.pre_title || '',
            title: s.title || '',
            subtitle: s.subtitle || '',
            textAlign: s.settings?.text_align || 'left',
            topbarHours: s.settings?.topbar_hours || '',
            hasHeading: !!SECTION_VARIANTS[s.canonical]?.heading,
            align: SECTION_VARIANTS[s.canonical]?.align !== false,
            limitData: s.limit_data,
            hasLimit: !!SECTION_VARIANTS[s.canonical]?.limit,
            dataTotal: dataCounts[s.canonical] || 0,
        }))
        // Footer dirender PublicLayout PALING AKHIR (layout section), jadi di
        // drawer posisinya dipin terakhir supaya urutan = urutan halaman.
        .sort((a, b) => (a.key === 'footer' ? 1 : b.key === 'footer' ? -1 : 0));

    // Konteks halaman: landing (home) vs detail (berita/halaman statis/kontak).
    // Detail hanya menampilkan section layout yang relevan; landing menyembunyikan
    // Page Header (tidak dirender di halaman depan).
    const isDetail = !!(page || announcement || header);
    const DETAIL_KEYS = ['topbar', 'navbar', 'pageheader', 'footer'];
    const visibleSections = isDetail
        ? sectionsList.filter((i) => DETAIL_KEYS.includes(i.key))
        : sectionsList.filter((i) => i.key !== 'pageheader');

    const applyToLanding = async () => {
        setSaving(true);
        setSaveMsg(null);
        try {
            const res = await fetch('/preview/design', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    template,
                    paletteKey: custom.paletteKey,
                    customPalette: custom.customPalette,
                    designFamily: custom.designFamily,
                    font: custom.font,
                    radius: custom.radius,
                    density: custom.density,
                    elevation: custom.elevation,
                    dark: custom.dark,
                    heroFill: custom.heroFill,
                    sectionVariants: customizer.sectionVariants,
                    sectionColors: customizer.sectionColors,
                    sectionSettings: customizer.sectionSettings,
                }),
            });
            if (!res.ok) throw new Error('Gagal menyimpan desain.');
            setSaveMsg('Tersimpan — landing asli / kini memakai desain ini.');
        } catch (e) {
            setSaveMsg('Gagal menyimpan. Pastikan login CMS aktif lalu coba lagi.');
        } finally {
            setSaving(false);
        }
    };

    return (
        <>
            <button
                type="button"
                className="theme-settings-btn"
                onClick={() => setOpen(true)}
                aria-label="Pengaturan tema"
            >
                <Settings2 size={18} />
                <span>Theme Settings</span>
            </button>

            {isOpen && (
                <>
                    <div className="theme-offcanvas-backdrop" onClick={() => setOpen(false)} />
                    <aside className="theme-offcanvas theme-offcanvas--open" aria-label="Pengaturan tema">
                        <header className="theme-offcanvas-header">
                            <div>
                                <h3>Theme Settings — {isDetail ? 'Detail Page' : 'Landing Page'}</h3>
                            </div>
                            <button type="button" className="theme-offcanvas-close" onClick={() => setOpen(false)} aria-label="Tutup">
                                <X size={20} />
                            </button>
                        </header>

                        <div className="theme-offcanvas-body">
                            <div className="theme-opt theme-opt--template">
                                <h4><span className="theme-opt-icon">◧</span>Tema</h4>
                                <ThemeSelect />
                            </div>

                            <div className="theme-apply">
                                <div className="theme-apply-row">
                                    <button
                                        type="button"
                                        className="theme-apply-btn"
                                        onClick={applyToLanding}
                                        disabled={saving}
                                    >
                                        {saving ? 'Menyimpan…' : (<><Check size={15} /> Terapkan ke landing</>)}
                                    </button>
                                    <button
                                        type="button"
                                        className="theme-dice-btn"
                                        title="Acak layout, warna, dan tipografi"
                                        onClick={() => {
                                            const sectionMeta = Object.entries(SECTION_VARIANTS).map(([key, value]) => ({ key, variants: value.variants }));
                                            customizer.applyGeneratedDesign(randomizeTheme(paletteOptions, fontOptions, sectionMeta));
                                        }}
                                        aria-label="Acak desain landing page"
                                    >
                                        <Dice5 size={16} />
                                    </button>
                                </div>
                                {saveMsg && (
                                    <span className={`theme-apply-msg ${saveMsg.startsWith('Tersimpan') ? 'ok' : 'err'}`}>
                                        {saveMsg}
                                    </span>
                                )}
                            </div>

                            <div className="theme-opt">
                                <h4>Gaya desain</h4>
                                <div className="theme-design-families">
                                    {DESIGN_FAMILIES.map(family => (
                                        <button type="button" key={family.key} aria-pressed={custom.designFamily === family.key}
                                            onClick={() => customizer.applyGeneratedDesign(randomizeTheme(paletteOptions, fontOptions,
                                                Object.entries(SECTION_VARIANTS).map(([key, value]) => ({ key, variants: value.variants })), family.key))}>
                                            {family.name}
                                        </button>
                                    ))}
                                </div>
                                {custom.customPalette && <div className="theme-generated-palette" aria-label="Palet hasil desain">
                                    {['primary', 'accent', 'background', 'foreground'].map(key => <span key={key} title={custom.customPalette[key]} style={{ background: custom.customPalette[key] }} />)}
                                    <small>Palet kustom</small>
                                </div>}
                            </div>
                            <div className="theme-opt">
                                <h4><span className="theme-opt-icon">▤</span>Sections</h4>
                                <div className="theme-sections-list">
                                    {visibleSections.map((item, index) => {
                                        const hasColor = !!(customizer.sectionColors?.[item.key] && Object.values(customizer.sectionColors[item.key]).some(Boolean));
                                        const isOver = overIndex === index && dragIndex !== null && dragIndex !== index;
                                        const active = customizer.sectionSettings?.[item.key]?.active ?? item.active;
                                        return (
                                            <div
                                                key={item.key}
                                                className={`theme-section-item${dragIndex === index ? ' dragging' : ''}${isOver ? ` drag-over drag-over--${overAfter ? 'after' : 'before'}` : ''}`}
                                                data-sec-color-pop
                                                onDragOver={(e) => {
                                                    e.preventDefault();
                                                    const rect = e.currentTarget.getBoundingClientRect();
                                                    const after = e.clientY > rect.top + rect.height / 2;
                                                    if (overIndex !== index || overAfter !== after) {
                                                        setOverIndex(index);
                                                        setOverAfter(after);
                                                    }
                                                }}
                                                onDrop={(e) => { e.preventDefault(); commitDrop(); }}
                                                onDragEnd={() => { setDragIndex(null); setOverIndex(null); setOverAfter(false); }}
                                            >
                                                <div className="theme-section-row">
                                                    <span className="theme-section-name">
                                                        {!isDetail && (
                                                            <span
                                                                className="theme-section-grip"
                                                                aria-hidden="true"
                                                                title="Seret untuk mengatur urutan"
                                                                draggable
                                                                onDragStart={(e) => { e.dataTransfer.effectAllowed = "move"; setDragIndex(index); setOverIndex(index); setOverAfter(false); }}
                                                                onDragEnd={() => { setDragIndex(null); setOverIndex(null); setOverAfter(false); }}
                                                            >⠿</span>
                                                        )}
                                                        {item.name}
                                                    </span>
                                                    <div className="theme-section-controls">
                                                        <select
                                                            className="theme-section-select"
                                                            value={customizer.sectionVariants?.[item.key] || item.current}
                                                            onChange={(e) => customizer.setSectionVariant(item.key, e.target.value)}
                                                            aria-label={`Variant ${item.name}`}
                                                        >
                                                            {item.options.map((opt) => (
                                                                <option key={opt.key} value={opt.key}>{opt.name}</option>
                                                            ))}
                                                        </select>
                                                        <button
                                                            type="button"
                                                            className={`theme-section-color-btn${openEditKey === item.key ? ' active' : ''}`}
                                                            onClick={() => setOpenEditKey((prev) => (prev === item.key ? null : item.key))}
                                                            title={`Edit ${item.name}`}
                                                            aria-label={`Edit ${item.name}`}
                                                        >
                                                            <Pencil size={13} />
                                                            {hasColor && (
                                                                <i
                                                                    className="theme-section-color-dot"
                                                                    style={{ background: customizer.sectionColors[item.key].bg || customizer.sectionColors[item.key].accent || '#155eef' }}
                                                                />
                                                            )}
                                                        </button>
                                                    </div>
                                                </div>

                                                {openEditKey === item.key && (
                                                    <SectionEditPopover
                                                        item={item}
                                                        sectionKey={item.key}
                                                        currentText={customizer.sectionSettings?.[item.key]}
                                                        currentColors={customizer.sectionColors?.[item.key]}
                                                        palette={customizer.palette}
                                                        active={active}
                                                        onApplyText={(patch) => { customizer.setSectionSetting(item.key, patch); setOpenEditKey(null); }}
                                                        onApplyTextLive={(patch) => customizer.setSectionSetting(item.key, patch)}
                                                        onToggleActive={(v) => customizer.setSectionSetting(item.key, { active: v })}
                                                        onApplyColor={(patch) => customizer.setSectionColor(item.key, patch)}
                                                        onClearColor={() => { customizer.resetSectionColor(item.key); }}
                                                        heroFill={custom.heroFill}
                                                        onToggleHeroFill={(v) => update({ heroFill: v })}
                                                        dark={custom.dark}
                                                        onToggleDark={(v) => update({ dark: v })}
                                                        showLogin={customizer.sectionSettings?.[item.key]?.show_login ?? true}
                                                        onToggleLogin={(v) => customizer.setSectionSetting(item.key, { show_login: v })}
                                                    />
                                                )}
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>

                            <div className="theme-opt">
                                <h4><span className="theme-opt-swatch" style={{ background: customizer.palette?.primary }} />Warna</h4>
                                <div className="theme-swatch-grid">
                                    {paletteOptions.map((p) => (
                                        <button
                                            key={p.key}
                                            type="button"
                                            className={`theme-swatch${custom.paletteKey === p.key ? ' active' : ''}`}
                                            style={{ background: p.primary }}
                                            title={p.name}
                                            onClick={() => update({ paletteKey: p.key, customPalette: null })}
                                            aria-label={`Palet ${p.name}`}
                                        >
                                            {custom.paletteKey === p.key && <Check size={14} />}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            <LabelSelect
                                title="Tipografi"
                                icon={<span className="theme-opt-icon">Aa</span>}
                                options={fontOptions}
                                value={custom.font}
                                onSelect={(key) => update({ font: key })}
                            />

                            <ChipGroup
                                title="Sudut (Radius)"
                                icon={<span className="theme-opt-icon">◜</span>}
                                options={RADIUS_OPTIONS}
                                value={custom.radius}
                                onSelect={(key) => update({ radius: key })}
                            />

                            <ChipGroup
                                title="Kepadatan"
                                icon={<span className="theme-opt-icon">⇕</span>}
                                options={DENSITY_OPTIONS}
                                value={custom.density}
                                onSelect={(key) => update({ density: key })}
                            />

                            <ChipGroup
                                title="Elevasi Kartu"
                                icon={<span className="theme-opt-icon">◫</span>}
                                options={ELEVATION_OPTIONS}
                                value={custom.elevation}
                                onSelect={(key) => update({ elevation: key })}
                            />

                            <button type="button" className="theme-reset-btn" onClick={reset}>
                                <RotateCcw size={15} /> Reset ke default tema
                            </button>
                        </div>
                    </aside>
                </>
            )}
        </>
    );
}
