import React, { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    AlignCenter,
    AlignLeft,
    AlignRight,
    Check,
    Dice5,
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
    randomizeTheme,
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
function ColorField({ label, value, onChange, hint }) {
    return (
        <label className="theme-sec-color-field">
            <span>{label}{hint && <small className="theme-sec-color-hint">{hint}</small>}</span>
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
 * SectionEditPopover — gabungan Teks + Warna + Latar dalam satu dropdown bertab.
 * Menggantikan TextEditPopover dan ColorPopover yang terpisah.
 */
function SectionEditPopover({ item, sectionKey, currentText = {}, currentColors = {}, palette, active, onApplyText, onApplyTextLive, onToggleActive, onApplyColor, onClearColor, heroFill, onToggleHeroFill, dark, onToggleDark, showLogin, onToggleLogin }) {
    const [tab, setTab] = useState('text');
    const [customColors, setCustomColors] = useState(Boolean(currentColors?.bg || currentColors?.pretext_color || currentColors?.text_color || currentColors?.posttext_color || currentColors?.accent));
    const [uploading, setUploading] = useState(false);
    const [msg, setMsg] = useState(null);

    /* ── Text form state ── */
    const [textForm, setTextForm] = useState({
        pre_title: currentText.pre_title ?? item.preTitle ?? '',
        title: currentText.title ?? item.title ?? '',
        subtitle: currentText.subtitle ?? item.subtitle ?? '',
        text_align: currentText.text_align ?? item.textAlign ?? 'left',
        topbar_hours: currentText.topbar_hours ?? item.topbarHours ?? '',
        limit_data: currentText.limit_data ?? item.limitData ?? '',
    });

    /* ── Color form state ── */
    const [colorForm, setColorForm] = useState({
        bg: currentColors?.bg || '',
        pretext_color: currentColors?.pretext_color || '',
        text_color: currentColors?.text_color || '',
        posttext_color: currentColors?.posttext_color || '',
        accent: currentColors?.accent || '',
        pattern: currentColors?.pattern || '',
        image: currentColors?.image || '',
    });

    useEffect(() => {
        setTextForm({
            pre_title: currentText.pre_title ?? item.preTitle ?? '',
            title: currentText.title ?? item.title ?? '',
            subtitle: currentText.subtitle ?? item.subtitle ?? '',
            text_align: currentText.text_align ?? item.textAlign ?? 'left',
            topbar_hours: currentText.topbar_hours ?? item.topbarHours ?? '',
            limit_data: currentText.limit_data ?? item.limitData ?? '',
        });
    }, [sectionKey]);

    useEffect(() => {
        setColorForm({
            bg: currentColors?.bg || '',
            pretext_color: currentColors?.pretext_color || '',
            text_color: currentColors?.text_color || '',
            posttext_color: currentColors?.posttext_color || '',
            accent: currentColors?.accent || '',
            pattern: currentColors?.pattern || '',
            image: currentColors?.image || '',
        });
    }, [sectionKey]);

    const alignOptions = [
        { key: 'left', icon: AlignLeft, title: 'Rata kiri' },
        { key: 'center', icon: AlignCenter, title: 'Rata tengah' },
        { key: 'right', icon: AlignRight, title: 'Rata kanan' },
    ];

    const setTextField = (field) => (e) => setTextForm((prev) => ({ ...prev, [field]: e.target.value }));

    const isTopbar = item.key === 'topbar';
    const canUploadLogo = (item.key === 'navbar' || item.key === 'footer') && !!usePage().props?.auth?.user;

    const textDirty =
        textForm.pre_title !== (currentText.pre_title ?? item.preTitle ?? '') ||
        textForm.title !== (currentText.title ?? item.title ?? '') ||
        textForm.subtitle !== (currentText.subtitle ?? item.subtitle ?? '') ||
        textForm.text_align !== (currentText.text_align ?? item.textAlign ?? 'left') ||
        textForm.topbar_hours !== (currentText.topbar_hours ?? item.topbarHours ?? '') ||
        String(textForm.limit_data) !== String(currentText.limit_data ?? item.limitData ?? '');

    /* ── Color handlers ── */
    const applyColorPreset = (x) => {
        const patch = {};
        if (x.key === 'accent') {
            patch.bg = palette?.primary || '#155eef';
            patch.accent = palette?.accent || patch.bg;
        } else {
            if (x.bg) patch.bg = x.bg;
            if (x.accent) patch.accent = x.accent;
        }
        setColorForm((prev) => ({ ...prev, bg: patch.bg || '', accent: patch.accent || '' }));
        onApplyColor(patch);
    };

    const changeColorField = (field, value) => {
        const next = { ...colorForm, [field]: value };
        setColorForm(next);
        const patch = {};
        Object.entries(next).forEach(([k, v]) => { if (v) patch[k] = v; });
        Object.keys(patch).length ? onApplyColor(patch) : onClearColor();
    };

    const setPattern = (key) => {
        setColorForm((prev) => ({ ...prev, pattern: key }));
        onApplyColor({ pattern: key || null });
    };

    const setImage = (url) => {
        setColorForm((prev) => ({ ...prev, image: url }));
        onApplyColor({ image: url || null });
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

    const saveText = () => {
        onApplyText({
            pre_title: textForm.pre_title,
            title: textForm.title,
            subtitle: textForm.subtitle,
            text_align: textForm.text_align,
            topbar_hours: textForm.topbar_hours,
            limit_data: textForm.limit_data === '' ? null : Number(textForm.limit_data),
        });
    };

    const tabClass = (x) => `theme-sec-bg-tab${tab === x ? ' active' : ''}`;

    return (
        <div className="theme-sec-edit-pop">
            {/* ── Tab bar ── */}
            <div className="theme-sec-bg-tabs" role="tablist" aria-label="Edit section">
                <button type="button" className={tabClass('text')} onClick={() => setTab('text')}>Teks</button>
                <button type="button" className={tabClass('color')} onClick={() => setTab('color')}>Warna</button>
                <button type="button" className={tabClass('image')} onClick={() => setTab('image')}>Latar</button>
            </div>

            {/* ── Active toggle (selalu tampil) ── */}
            <label className="theme-topbar-toggle" title={active ? 'Nonaktifkan section ini' : 'Aktifkan section ini'}>
                <span>{active ? 'Section aktif' : 'Section nonaktif'}</span>
                <input
                    type="checkbox"
                    checked={!!active}
                    onChange={(e) => onToggleActive(e.target.checked)}
                />
                <i />
            </label>

            {/* ── Navbar-specific toggles ── */}
            {item.key === 'navbar' && (
                <>
                    <label className="theme-topbar-toggle" title="Tampilkan tombol Masuk di navbar">
                        <span>Tombol Masuk</span>
                        <input
                            type="checkbox"
                            checked={showLogin !== false}
                            onChange={(e) => onToggleLogin(e.target.checked)}
                        />
                        <i />
                    </label>
                    <label className="theme-topbar-toggle" title="Mode gelap untuk seluruh halaman">
                        <span>Mode Gelap</span>
                        <input
                            type="checkbox"
                            checked={!!dark}
                            onChange={(e) => onToggleDark(e.target.checked)}
                        />
                        <i />
                    </label>
                </>
            )}

            {/* ── Tab: Teks ── */}
            {tab === 'text' && (
                <div className="theme-sec-edit-fields">
                    {item.hasHeading && (
                        <>
                            {item.align && (
                                <div className="theme-sec-align-row" role="group" aria-label="Perataan teks">
                                    {alignOptions.map((opt) => (
                                        <button
                                            key={opt.key}
                                            type="button"
                                            className={`theme-sec-align-btn${textForm.text_align === opt.key ? ' active' : ''}`}
                                            title={opt.title}
                                            aria-label={`${opt.title} section`}
                                            aria-pressed={textForm.text_align === opt.key}
                                            onClick={() => {
                                                setTextForm((prev) => ({ ...prev, text_align: opt.key }));
                                                onApplyTextLive({ text_align: opt.key });
                                            }}
                                        >
                                            <opt.icon size={14} />
                                        </button>
                                    ))}
                                </div>
                            )}
                            <label className="theme-sec-text-field">
                                <span>Pretitle</span>
                                <input value={textForm.pre_title} onChange={setTextField('pre_title')} placeholder="mis. Dipercaya Oleh" />
                            </label>
                            <label className="theme-sec-text-field">
                                <span>Judul</span>
                                <input value={textForm.title} onChange={setTextField('title')} placeholder="mis. Institusi Mitra" />
                            </label>
                            <label className="theme-sec-text-field">
                                <span>Subjudul</span>
                                <textarea rows={2} value={textForm.subtitle} onChange={setTextField('subtitle')} placeholder="Deskripsi singkat section" />
                            </label>
                        </>
                    )}

                    {isTopbar && (
                        <label className="theme-sec-text-field">
                            <span>Jam operasional</span>
                            <input value={textForm.topbar_hours} onChange={setTextField('topbar_hours')} placeholder="mis. Senin–Jumat 08.00–17.00, Sabtu 09.00–14.00" />
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
                                value={textForm.limit_data}
                                onChange={(e) => {
                                    const v = e.target.value;
                                    setTextForm((prev) => ({ ...prev, limit_data: v }));
                                    onApplyTextLive({ limit_data: v === '' ? null : Number(v) });
                                }}
                                placeholder={item.limitData ? String(item.limitData) : 'Semua'}
                            />
                        </label>
                    )}

                    <button
                        type="button"
                        className="theme-sec-text-apply"
                        disabled={!textDirty}
                        onClick={saveText}
                    >
                        Simpan teks & tutup
                    </button>
                </div>
            )}

            {/* ── Tab: Warna ── */}
            {tab === 'color' && (
                <div className="theme-sec-edit-fields">
                    {!customColors ? (
                        <div className="theme-sec-color-presets">
                            <button
                                type="button"
                                className="theme-sec-color-preset theme-sec-color-preset--clear"
                                onClick={onClearColor}
                                title="Kembalikan ke default tema"
                            >
                                <RotateCcw size={12} /> Default
                            </button>
                            {SECTION_COLOR_PRESETS.map((x) => {
                                const j = x.key === 'accent' ? (palette?.primary || '#155eef') : (x.bg || '#ffffff');
                                const active = colorForm.bg === j && colorForm.accent === (x.accent || (x.key === 'accent' ? (palette?.accent || j) : ''));
                                return (
                                    <button
                                        key={x.key}
                                        type="button"
                                        className={`theme-sec-color-preset${active ? ' active' : ''}`}
                                        style={{ background: j }}
                                        title={x.name}
                                        onClick={() => applyColorPreset(x)}
                                        aria-label={`Preset ${x.name}`}
                                    >
                                        {active && <Check size={12} />}
                                    </button>
                                );
                            })}
                        </div>
                    ) : (
                        <div className="theme-sec-color-fields">
                            <ColorField label="Latar" value={colorForm.bg} onChange={(v) => changeColorField('bg', v)} />
                            <div className="theme-sec-color-divider"><span>Warna Teks</span></div>
                            <ColorField label="Pretitle" value={colorForm.pretext_color} onChange={(v) => changeColorField('pretext_color', v)} />
                            <ColorField label="Judul" value={colorForm.text_color} onChange={(v) => changeColorField('text_color', v)} />
                            <ColorField label="Subjudul" value={colorForm.posttext_color} onChange={(v) => changeColorField('posttext_color', v)} />
                            <div className="theme-sec-color-divider"><span>Aksen</span></div>
                            <ColorField label="Aksen" value={colorForm.accent} onChange={(v) => changeColorField('accent', v)} />
                        </div>
                    )}
                    <button
                        type="button"
                        className={`theme-sec-custom-toggle${customColors ? ' active' : ''}`}
                        onClick={() => setCustomColors((v) => !v)}
                        aria-pressed={customColors}
                    >
                        <Palette size={13} />
                        {customColors ? 'Kembali ke preset' : 'Warna kustom sendiri'}
                    </button>
                </div>
            )}

            {/* ── Tab: Latar (Isi Satu Layar + Gambar + Pola) ── */}
            {tab === 'image' && (
                <div className="theme-sec-edit-fields">
                    {item.key === 'hero' && (
                        <label className="theme-topbar-toggle" title="Hero mengisi satu layar penuh">
                            <span>Isi Satu Layar</span>
                            <input
                                type="checkbox"
                                checked={!!heroFill}
                                onChange={(e) => onToggleHeroFill(e.target.checked)}
                            />
                            <i />
                        </label>
                    )}

                    <div className="theme-sec-img-row">
                        <label className="theme-sec-img-upload">
                            <Upload size={14} />
                            {uploading ? 'Mengunggah…' : 'Upload gambar latar'}
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
                            value={colorForm.image}
                            placeholder="…atau tempel URL gambar"
                            onChange={(e) => setImage(e.target.value)}
                            aria-label="URL gambar latar"
                        />
                        {colorForm.image && (
                            <button type="button" className="theme-sec-img-remove" onClick={() => setImage('')} title="Hapus gambar latar">
                                <X size={13} /> Hapus gambar
                            </button>
                        )}
                        {msg && <span className={`theme-sec-img-msg${msg === 'Tersimpan.' ? ' ok' : ''}`}>{msg}</span>}
                    </div>

                    <div className="theme-sec-color-divider"><span>Pola abstrak</span></div>
                    <div className="theme-sec-pattern-grid">
                        <button
                            type="button"
                            className={`theme-sec-pattern-swatch theme-sec-pattern-swatch--none${colorForm.pattern ? '' : ' active'}`}
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
                                className={`theme-sec-pattern-swatch${colorForm.pattern === x.key ? ' active' : ''}`}
                                style={patternStyle(x.key)}
                                onClick={() => setPattern(x.key)}
                                title={x.name}
                                aria-label={`Pola ${x.name}`}
                            >
                                {colorForm.pattern === x.key && <Check size={12} />}
                            </button>
                        ))}
                    </div>
                </div>
            )}
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
                                        title="Randomize semua pengaturan"
                                        onClick={() => {
                                            const sectionMeta = Object.entries(SECTION_VARIANTS).map(([k, v]) => ({
                                                key: k,
                                                numModes: v.variants?.length || 3,
                                            }));
                                            const random = randomizeTheme(paletteOptions, fontOptions, sectionMeta);
                                            // Apply all random settings
                                            customizer.update({
                                                paletteKey: random.paletteKey,
                                                font: random.font,
                                                radius: random.radius,
                                                density: random.density,
                                                elevation: random.elevation,
                                                dark: random.dark,
                                                heroFill: random.heroFill,
                                            });
                                            Object.entries(random.sectionVariants || {}).forEach(([k, v]) => {
                                                customizer.setSectionVariant(k, v);
                                            });
                                            Object.entries(random.sectionColors || {}).forEach(([k, v]) => {
                                                customizer.setSectionColor(k, v);
                                            });
                                        }}
                                        aria-label="Randomize theme"
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
