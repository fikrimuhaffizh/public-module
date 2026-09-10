import React from 'react';
import { usePage } from '@inertiajs/react';
import { useThemeCustomizer } from './ThemeCustomizerContext';

const Drawer = React.lazy(() => import('./ThemeSettingsDrawer').then(module => ({ default: module.ThemeSettingsDrawer })));

export default function ThemeEditor() {
    const { canCustomizeDesign } = usePage().props;
    const { isOpen, setOpen } = useThemeCustomizer();
    if (!canCustomizeDesign) return null;
    if (!isOpen) return <button type="button" className="theme-settings-btn" onClick={() => setOpen(true)}>Theme Settings</button>;
    return <React.Suspense fallback={<span role="status">Memuat pengaturan…</span>}><Drawer /></React.Suspense>;
}
