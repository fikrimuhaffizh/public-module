import React, { useEffect, useState } from 'react';
import { router, usePage } from '@inertiajs/react';
import {
    AlignCenter,
    AlignLeft,
    AlignRight,
    Check,
    Palette,
    RotateCcw,
    Trash2,
    Upload,
    X,
} from 'lucide-react';
import {
    SECTION_COLOR_PRESETS,
    SECTION_PATTERNS,
} from './presets';


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
export default function SectionEditPopover({ item, sectionKey, currentText = {}, currentColors = {}, palette, active, onApplyText, onApplyTextLive, onToggleActive, onApplyColor, onClearColor, heroFill, onToggleHeroFill, dark, onToggleDark, showLogin, onToggleLogin }) {
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
                            {uploading ? 'Mengunggah…' : 'Upload gambar dari komputer'}
                            <input
                                type="file"
                                accept="image/png,image/jpeg,image/webp,image/gif,image/svg+xml"
                                disabled={uploading}
                                onChange={(e) => uploadImage(e.target.files?.[0])}
                            />
                        </label>
                        <div className="theme-sec-url-group">
                            <span className="theme-sec-url-label">atau tempel URL gambar</span>
                            <input
                                type="url"
                                className="theme-sec-img-url"
                                value={colorForm.image}
                                placeholder="https://images.unsplash.com/photo-..."
                                onChange={(e) => setImage(e.target.value)}
                                aria-label="URL gambar latar"
                            />
                            <span className="theme-sec-url-hint">Cari gambar gratis di Unsplash, Pexels, atau Google Images</span>
                        </div>
                        {colorForm.image && (
                            <div className="theme-sec-img-preview">
                                <img src={colorForm.image} alt="Preview" onError={(e) => e.target.style.display = 'none'} />
                                <button type="button" className="theme-sec-img-remove" onClick={() => setImage('')} title="Hapus gambar">
                                    <X size={13} /> Hapus
                                </button>
                            </div>
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
