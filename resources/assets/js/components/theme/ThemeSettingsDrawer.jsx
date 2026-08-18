import React, { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    AlignCenter,
    AlignLeft,
    AlignRight,
    Check,
    Palette,
    Pencil,
    RotateCcw,
    Settings2,
    Trash2,
    Upload,
    X,
} from 'lucide-react';
import { useThemeCustomizer } from './ThemeCustomizerContext';
import {
    DENSITY_OPTIONS,
    ELEVATION_OPTIONS,
    RADIUS_OPTIONS,
    SECTION_COLOR_PRESETS,
    SECTION_PATTERNS,
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
function ColorField({ label, value, onChange }) {
    return (
        <label className="theme-sec-color-field">
            <span>{label}</span>
            <input
                type="color"
                value={value || '#ffffff'}
                onChange={(e) => onChange(e.target.value)}
                aria-label={`Warna ${label}`}
            />
            <input
                type="text"
                className="theme-sec-color-hex"
                value={value || ''}
                placeholder="auto"
                onChange={(e) => onChange(e.target.value)}
                aria-label={`Hex ${label}`}
            />
        </label>
    );
}

/** Style CSS untuk pola latar abstrak (dots/grid/diagonal/waves/beams/noise). */
function patternStyle(key) {
    const c = 'rgba(21, 94, 239, .4)';
    switch (key) {
        case 'dots':
            return {
                backgroundImage: `radial-gradient(${c} 1.6px, transparent 1.6px)`,
                backgroundSize: '22px 22px',
            };
        case 'grid':
            return {
                backgroundImage: `linear-gradient(${c} 1px, transparent 1px), linear-gradient(90deg, ${c} 1px, transparent 1px)`,
                backgroundSize: '28px 28px',
            };
        case 'diagonal':
            return {
                backgroundImage: `repeating-linear-gradient(45deg, ${c} 0 10px, transparent 10px 20px)`,
            };
        case 'waves':
            return {
                backgroundImage: `radial-gradient(120% 90% at 50% 0%, transparent 60%, ${c} 60.5% 100%)`,
                backgroundSize: '100% 50%',
                backgroundPosition: 'top',
                backgroundRepeat: 'no-repeat',
            };
        case 'beams':
            return {
                backgroundImage: `linear-gradient(120deg, transparent 0 38%, ${c} 38% 46%, transparent 46% 62%, ${c} 62% 72%, transparent 72%)`,
                backgroundSize: '150px 150px',
            };
        case 'noise':
            return {
                backgroundImage: `url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='80'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='2'/></filter><rect width='80' height='80' filter='url(%23n)' opacity='0.55'/></svg>")`,
                backgroundSize: '90px 90px',
            };
        default:
            return {};
    }
}

/** ChipGroup — deretan tombol pilihan (Radius / Kepadatan / Elevasi). */
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
                        {value === opt.key && <Check size={13} />}
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
            <span className="theme-select-hint">
                Tema = preset lengkap (section, warna, font, radius) — ubah di bawah untuk menyesuaikan.
            </span>
        </div>
    );
}

/**
 * ColorPopover — latar section: tab Warna / Pola / Gambar.
 * Tab Warna: mode PRESET (Default + swatch) ATAU mode kustom (Latar/Teks/
 * Aksen) — toggle "Warna kustom sendiri" MENGGANTIKAN daftar preset, tidak
 * tampil bersamaan. Perubahan diterapkan live.
 */
function ColorPopover({ sectionKey, current = {}, palette, onApply, onClear }) {
    const [tab, setTab] = useState('color');
    const [customOpen, setCustomOpen] = useState(Boolean(current?.bg || current?.text || current?.accent));
    const [uploading, setUploading] = useState(false);
    const [msg, setMsg] = useState(null);
    const [form, setForm] = useState({
        bg: current?.bg || '',
        text: current?.text || '',
        accent: current?.accent || '',
        pattern: current?.pattern || '',
        image: current?.image || '',
    });

    useEffect(() => {
        setForm({
            bg: current?.bg || '',
            text: current?.text || '',
            accent: current?.accent || '',
            pattern: current?.pattern || '',
            image: current?.image || '',
        });
    }, [sectionKey, current?.bg, current?.text, current?.accent, current?.pattern, current?.image]);

    const applyPreset = (x) => {
        const patch = {};
        if (x.key === 'accent') {
            patch.bg = palette?.primary || '#155eef';
            patch.accent = palette?.accent || patch.bg;
        } else {
            if (x.bg) patch.bg = x.bg;
            if (x.accent) patch.accent = x.accent;
        }
        if (x.text) patch.text = x.text;
        setForm((prev) => ({ ...prev, bg: patch.bg || '', text: patch.text || '', accent: patch.accent || '' }));
        onApply(patch);
    };

    const changeField = (field, value) => {
        const next = { ...form, [field]: value };
        setForm(next);
        const patch = {};
        Object.entries(next).forEach(([k, v]) => { if (v) patch[k] = v; });
        Object.keys(patch).length ? onApply(patch) : onClear();
    };

    const setPattern = (key) => {
        setForm((prev) => ({ ...prev, pattern: key }));
        onApply({ pattern: key || null });
    };

    const setImage = (url) => {
        setForm((prev) => ({ ...prev, image: url }));
        onApply({ image: url || null });
    };

    const uploadImage = async (file) => {
        if (!file) return;
        setUploading(true);
        setMsg(null);
        try {
            const fd = new FormData();
            fd.append('image', file);
            const res = await fetch('/cms/section/upload-background', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: fd,
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.url) throw new Error(data.message || 'Gagal mengunggah gambar.');
            setImage(data.url);
            setMsg('Tersimpan.');
        } catch (e) {
            setMsg(e.message || 'Gagal mengunggah gambar.');
        } finally {
            setUploading(false);
        }
    };

    const tabClass = (x) => `theme-sec-bg-tab${tab === x ? ' active' : ''}`;

    return (
        <div className="theme-sec-color-pop">
            <div className="theme-sec-color-title">Latar section</div>
            <div className="theme-sec-bg-tabs" role="tablist" aria-label="Jenis latar section">
                <button type="button" className={tabClass('color')} onClick={() => setTab('color')}>Warna</button>
                <button type="button" className={tabClass('pattern')} onClick={() => setTab('pattern')}>Pola</button>
                <button type="button" className={tabClass('image')} onClick={() => setTab('image')}>Gambar</button>
            </div>

            {tab === 'color' && (
                <>
                    {!customOpen ? (
                        <div className="theme-sec-color-presets">
                            <button
                                type="button"
                                className="theme-sec-color-preset theme-sec-color-preset--clear"
                                onClick={onClear}
                                title="Kembalikan ke default tema"
                            >
                                <RotateCcw size={12} /> Default
                            </button>
                            {SECTION_COLOR_PRESETS.map((x) => {
                                const j = x.key === 'accent' ? (palette?.primary || '#155eef') : (x.bg || '#ffffff');
                                const active = form.bg === j && form.text === (x.text || '') && form.accent === (x.accent || (x.key === 'accent' ? (palette?.accent || j) : ''));
                                return (
                                    <button
                                        key={x.key}
                                        type="button"
                                        className={`theme-sec-color-preset${active ? ' active' : ''}`}
                                        style={{ background: j }}
                                        title={x.name}
                                        onClick={() => applyPreset(x)}
                                        aria-label={`Preset ${x.name}`}
                                    >
                                        {active && <Check size={12} />}
                                    </button>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="theme-sec-color-fields">
                            <ColorField label="Latar" value={form.bg} onChange={(v) => changeField('bg', v)} />
                            <ColorField label="Teks" value={form.text} onChange={(v) => changeField('text', v)} />
                            <ColorField label="Aksen" value={form.accent} onChange={(v) => changeField('accent', v)} />
                        </div>
                    )}
                    <button
                        type="button"
                        className={`theme-sec-custom-toggle${customOpen ? ' active' : ''}`}
                        onClick={() => setCustomOpen((v) => !v)}
                        aria-pressed={customOpen}
                    >
                        <Palette size={13} />
                        {customOpen ? 'Kembali ke preset' : 'Warna kustom sendiri'}
                    </button>
                </>
            )}

            {tab === 'pattern' && (
                <div className="theme-sec-pattern-grid">
                    <button
                        type="button"
                        className={`theme-sec-pattern-swatch theme-sec-pattern-swatch--none${form.pattern ? '' : ' active'}`}
                        onClick={() => setPattern('')}
                        title="Tanpa pola"
                        aria-label="Tanpa pola"
                    >
                        <span />Tanpa
                    </button>
                    {SECTION_PATTERNS.map((x) => (
                        <button
                            key={x.key}
                            type="button"
                            className={`theme-sec-pattern-swatch${form.pattern === x.key ? ' active' : ''}`}
                            style={patternStyle(x.key)}
                            onClick={() => setPattern(x.key)}
                            title={x.name}
                            aria-label={`Pola ${x.name}`}
                        >
                            {form.pattern === x.key && <Check size={12} />}
                        </button>
                    ))}
                </div>
            )}

            {tab === 'image' && (
                <div className="theme-sec-img-row">
                    <label className="theme-sec-img-upload">
                        <Upload size={14} />
                        {uploading ? 'Mengunggah…' : 'Upload gambar'}
                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml"
                            disabled={uploading}
                            onChange={(e) => uploadImage(e.target.files?.[0])}
                        />
                    </label>
                    <input
                        type="text"
                        className="theme-sec-img-url"
                        value={form.image}
                        placeholder="…atau tempel URL gambar"
                        onChange={(e) => setImage(e.target.value)}
                        aria-label="URL gambar latar"
                    />
                    {form.image && (
                        <button type="button" className="theme-sec-img-remove" onClick={() => setImage('')} title="Hapus gambar latar">
                            <X size={13} /> Hapus gambar
                        </button>
                    )}
                    {msg && <span className={`theme-sec-img-msg${msg === 'Tersimpan.' ? ' ok' : ''}`}>{msg}</span>}
                </div>
            )}
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
function LogoUploader({ collection, label }) {
    const [busy, setBusy] = useState(false);
    const [msg, setMsg] = useState(null);
    const site = usePage().props?.site || {};
    const currentUrl = collection === 'logo_footer' ? site.logoFooter : site.logo;

    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    const done = () => router.reload({ only: ['site'] });

    const upload = async (file) => {
        if (!file) return;
        setBusy(true);
        setMsg(null);
        try {
            const fd = new FormData();
            fd.append('logo', file);
            fd.append('collection', collection);
            const res = await fetch('/cms/section/upload-logo', {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
                body: fd,
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Gagal mengunggah logo.');
            setMsg('Logo tersimpan.');
            done();
        } catch (e) {
            setMsg(e.message || 'Gagal mengunggah logo.');
        } finally {
            setBusy(false);
        }
    };

    const remove = async () => {
        if (!window.confirm('Hapus logo ini?')) return;
        setBusy(true);
        setMsg(null);
        try {
            const res = await fetch(`/cms/section/delete-logo/${collection}`, {
                method: 'DELETE',
                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) throw new Error(data.message || 'Gagal menghapus logo.');
            setMsg('Logo dihapus.');
            done();
        } catch (e) {
            setMsg(e.message || 'Gagal menghapus logo.');
        } finally {
            setBusy(false);
        }
    };

    return (
        <div className="theme-sec-logo">
            <span className="theme-sec-logo-label">{label}</span>
            <div className="theme-sec-logo-row">
                {currentUrl && <img src={currentUrl} alt={label} className="theme-sec-logo-preview" />}
                <label className="theme-sec-logo-upload">
                    <Upload size={13} />
                    {busy ? 'Memproses…' : currentUrl ? 'Ganti logo' : 'Upload logo'}
                    <input
                        type="file"
                        accept="image/png,image/svg+xml,image/webp,image/jpeg"
                        disabled={busy}
                        onChange={(e) => upload(e.target.files?.[0])}
                    />
                </label>
                {currentUrl && (
                    <button type="button" className="theme-sec-logo-remove" onClick={remove} disabled={busy} title="Hapus logo" aria-label="Hapus logo">
                        <Trash2 size={13} />
                    </button>
                )}
            </div>
            {msg && <span className={`theme-sec-img-msg${msg === 'Logo tersimpan.' || msg === 'Logo dihapus.' ? ' ok' : ''}`}>{msg}</span>}
        </div>
    );
}
/**
 * TextEditPopover — teks section: toggle aktif, pretitle/title/subtitle,
 * perataan teks (3 tombol), jam operasional khusus Top Bar.
 */
function TextEditPopover({ item, current = {}, active, onApply, onApplyLive, onToggleActive }) {
    const [form, setForm] = useState({
        pre_title: current.pre_title ?? item.preTitle ?? '',
        title: current.title ?? item.title ?? '',
        subtitle: current.subtitle ?? item.subtitle ?? '',
        text_align: current.text_align ?? item.textAlign ?? 'left',
        topbar_hours: current.topbar_hours ?? item.topbarHours ?? '',
        limit_data: current.limit_data ?? item.limitData ?? '',
    });

    const alignOptions = [
        { key: 'left', icon: AlignLeft, title: 'Rata kiri' },
        { key: 'center', icon: AlignCenter, title: 'Rata tengah' },
        { key: 'right', icon: AlignRight, title: 'Rata kanan' },
    ];
    const setField = (field) => (e) => setForm((prev) => ({ ...prev, [field]: e.target.value }));

    const dirty =
        form.pre_title !== (current.pre_title ?? item.preTitle ?? '') ||
        form.title !== (current.title ?? item.title ?? '') ||
        form.subtitle !== (current.subtitle ?? item.subtitle ?? '') ||
        form.text_align !== (current.text_align ?? item.textAlign ?? 'left') ||
        form.topbar_hours !== (current.topbar_hours ?? item.topbarHours ?? '') ||
        String(form.limit_data) !== String(current.limit_data ?? item.limitData ?? '');
    const isTopbar = item.key === 'topbar';
    // Upload logo hanya untuk user login CMS (endpoint butuh public.cms.update).
    const canUploadLogo = (item.key === 'navbar' || item.key === 'footer') && !!usePage().props?.auth?.user;

    return (
        <div className="theme-sec-text-pop">
            <div className="theme-sec-text-title">Teks section</div>
            <label className="theme-topbar-toggle" title={active ? 'Nonaktifkan section ini' : 'Aktifkan section ini'}>
                <span>{active ? 'Section aktif' : 'Section nonaktif'}</span>
                <input
                    type="checkbox"
                    checked={!!active}
                    onChange={(e) => onToggleActive(e.target.checked)}
                />
                <i />
            </label>

            {item.hasHeading && (
                <>
                    {item.align && (
                        <div className="theme-sec-align-row" role="group" aria-label="Perataan teks">
                            {alignOptions.map((opt) => (
                                <button
                                    key={opt.key}
                                    type="button"
                                    className={`theme-sec-align-btn${form.text_align === opt.key ? ' active' : ''}`}
                                    title={opt.title}
                                    aria-label={`${opt.title} section`}
                                    aria-pressed={form.text_align === opt.key}
                                    onClick={() => {
                                        setForm((prev) => ({ ...prev, text_align: opt.key }));
                                        onApplyLive({ text_align: opt.key });
                                    }}
                                >
                                    <opt.icon size={14} />
                                </button>
                            ))}
                        </div>
                    )}
                    <label className="theme-sec-text-field">
                        <span>Pretitle</span>
                        <input value={form.pre_title} onChange={setField('pre_title')} placeholder="mis. Dipercaya Oleh" />
                    </label>
                    <label className="theme-sec-text-field">
                        <span>Judul</span>
                        <input value={form.title} onChange={setField('title')} placeholder="mis. Institusi Mitra" />
                    </label>
                    <label className="theme-sec-text-field">
                        <span>Subjudul</span>
                        <textarea rows={2} value={form.subtitle} onChange={setField('subtitle')} placeholder="Deskripsi singkat section" />
                    </label>
                </>
            )}

            {isTopbar && (
                <label className="theme-sec-text-field">
                    <span>Jam operasional</span>
                    <input value={form.topbar_hours} onChange={setField('topbar_hours')} placeholder="mis. Senin–Jumat 08.00–17.00, Sabtu 09.00–14.00" />
                </label>
            )}

            {canUploadLogo && (
                <LogoUploader collection={item.key === 'navbar' ? 'logo_navbar' : 'logo_footer'} label={item.key === 'navbar' ? 'Logo Navbar' : 'Logo Footer'} />
            )}

            {item.hasLimit && (
                <label className="theme-sec-text-field">
                    <span>Jumlah data tampil{item.dataTotal > 0 ? ` (dari ${item.dataTotal})` : ''}</span>
                    <input
                        type="number"
                        min={1}
                        max={item.dataTotal > 0 ? item.dataTotal : undefined}
                        value={form.limit_data}
                        onChange={(e) => {
                            const v = e.target.value;
                            setForm((prev) => ({ ...prev, limit_data: v }));
                            onApplyLive({ limit_data: v === '' ? null : Number(v) });
                        }}
                        placeholder={item.limitData ? String(item.limitData) : 'Semua'}
                    />
                </label>
            )}

            <button
                type="button"
                className="theme-sec-text-apply"
                disabled={!dirty}
                onClick={() => onApply({
                    pre_title: form.pre_title,
                    title: form.title,
                    subtitle: form.subtitle,
                    text_align: form.text_align,
                    topbar_hours: form.topbar_hours,
                    limit_data: form.limit_data === '' ? null : Number(form.limit_data),
                })}
            >
                Simpan teks & tutup
            </button>
        </div>
    );
}

/**
 * ThemeSettingsDrawer — offcanvas Theme Settings dua mode:
 * Landing Page (halaman depan) & Detail Page (halaman dalam).
 * Di /preview selalu tampil; di halaman asli hanya untuk user login CMS.
 * Daftar section difilter sesuai konteks halaman — hanya yang relevan.
 */
export function ThemeSettingsDrawer() {
    const customizer = useThemeCustomizer();
    if (!customizer) return null;
    const canEdit = customizer.preview || !!usePage().props?.auth?.user;
    if (!canEdit) return null;

    const {
        isOpen, setOpen,
        custom, update, reset,
        paletteOptions, fontOptions,
    } = customizer;

    const [openColorKey, setOpenColorKey] = useState(null);
    const [openTextKey, setOpenTextKey] = useState(null);
    const [dragIndex, setDragIndex] = useState(null);
    const [overIndex, setOverIndex] = useState(null);
    const [overAfter, setOverAfter] = useState(false);
    const [savingReorder, setSavingReorder] = useState(false);
    const [saving, setSaving] = useState(false);
    const [saveMsg, setSaveMsg] = useState(null);

    const saveReorder = async (order) => {
        setSavingReorder(true);
        try {
            await fetch('/cms/section/reorder-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ order: order.map((s) => ({ id: String(s.id), area: s.area })) }),
            });
            router.reload({ only: ['sections'] });
        } catch (e) {
            console.error('reorder section gagal', e);
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

    // Tutup popover teks saat klik di luar area popover.
    useEffect(() => {
        if (!openTextKey) return;
        const handler = (e) => {
            if (!e.target.closest('[data-sec-color-pop]')) setOpenTextKey(null);
        };
        document.addEventListener('mousedown', handler);
        return () => document.removeEventListener('mousedown', handler);
    }, [openTextKey]);

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
                                <p>{isDetail ? 'Section yang relevan untuk halaman dalam (topbar, navbar, breadcrumb/page header, footer).' : 'Customize tampilan halaman depan — hanya untuk pratinjau.'}</p>
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
                                <button
                                    type="button"
                                    className="theme-apply-btn"
                                    onClick={applyToLanding}
                                    disabled={saving}
                                >
                                    {saving ? 'Menyimpan…' : (<><Check size={15} /> Terapkan ke landing</>)}
                                </button>
                                {saveMsg && (
                                    <span className={`theme-apply-msg ${saveMsg.startsWith('Tersimpan') ? 'ok' : 'err'}`}>
                                        {saveMsg}
                                    </span>
                                )}
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
                                                            className={`theme-section-color-btn${openTextKey === item.key ? ' active' : ''}`}
                                                            onClick={() => setOpenTextKey((prev) => (prev === item.key ? null : item.key))}
                                                            title={`Edit teks ${item.name}`}
                                                            aria-label={`Edit teks ${item.name}`}
                                                        >
                                                            <Pencil size={13} />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            className={`theme-section-color-btn${hasColor ? ' active' : ''}`}
                                                            onClick={() => setOpenColorKey((prev) => (prev === item.key ? null : item.key))}
                                                            title={`Warna ${item.name}`}
                                                            aria-label={`Warna ${item.name}`}
                                                        >
                                                            <Palette size={14} />
                                                            {hasColor && (
                                                                <i
                                                                    className="theme-section-color-dot"
                                                                    style={{ background: customizer.sectionColors[item.key].bg || customizer.sectionColors[item.key].accent || '#155eef' }}
                                                                />
                                                            )}
                                                        </button>
                                                    </div>
                                                </div>

                                                {openColorKey === item.key && (
                                                    <ColorPopover
                                                        sectionKey={item.key}
                                                        current={customizer.sectionColors?.[item.key]}
                                                        palette={customizer.palette}
                                                        onApply={(patch) => customizer.setSectionColor(item.key, patch)}
                                                        onClear={() => { customizer.resetSectionColor(item.key); setOpenColorKey(null); }}
                                                    />
                                                )}
                                                {openTextKey === item.key && (
                                                    <TextEditPopover
                                                        item={item}
                                                        current={customizer.sectionSettings?.[item.key]}
                                                        active={active}
                                                        onApply={(patch) => { customizer.setSectionSetting(item.key, patch); setOpenTextKey(null); }}
                                                        onApplyLive={(patch) => customizer.setSectionSetting(item.key, patch)}
                                                        onToggleActive={(v) => customizer.setSectionSetting(item.key, { active: v })}
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
                                            onClick={() => update({ paletteKey: p.key })}
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
                                hint="Pasangan font heading & body — ikut berubah di seluruh halaman."
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
                                hint="Jarak antar section — Padat hemat ruang, Lega lebih bernapas."
                            />

                            <ChipGroup
                                title="Elevasi Kartu"
                                icon={<span className="theme-opt-icon">◫</span>}
                                options={ELEVATION_OPTIONS}
                                value={custom.elevation}
                                onSelect={(key) => update({ elevation: key })}
                                hint="Bayangan kartu — Flat datar, Tajam sangat terangkat."
                            />

                            <div className="theme-opt theme-dark-row">
                                <div className="theme-dark-info">
                                    <h4><span className="theme-opt-icon">◐</span>Mode Gelap</h4>
                                    <span className="theme-select-hint">Ubah permukaan halaman menjadi gelap.</span>
                                </div>
                                <label className="theme-switch">
                                    <input
                                        type="checkbox"
                                        checked={!!custom.dark}
                                        onChange={(e) => update({ dark: e.target.checked })}
                                        aria-label="Mode gelap"
                                    />
                                    <span className="theme-switch-track" />
                                </label>
                            </div>

                            <div className="theme-opt theme-dark-row">
                                <div className="theme-dark-info">
                                    <h4><span className="theme-opt-icon">▭</span>Isi Satu Layar</h4>
                                    <span className="theme-select-hint">
                                        Hero mengisi satu layar penuh. Matikan agar tinggi hero mengikuti konten.
                                    </span>
                                </div>
                                <label className="theme-switch">
                                    <input
                                        type="checkbox"
                                        checked={!!custom.heroFill}
                                        onChange={(e) => update({ heroFill: e.target.checked })}
                                        aria-label="Isi satu layar"
                                    />
                                    <span className="theme-switch-track" />
                                </label>
                            </div>

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
