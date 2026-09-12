import React from 'react';
import { SectionVariantRenderer } from '../components/sections/renderer';
import { sectionKey } from '../components/sections/keys';
import { useThemeCustomizer } from '../components/theme/ThemeCustomizerContext';

/** Templates differ through their preset; rendering and editor overrides share one implementation. */
export default function SectionTemplate({ data }) {
    const customizer = useThemeCustomizer();
    return <>{(data.sections || []).map(section => {
        const key = sectionKey(section);
        const active = customizer?.sectionSettings?.[key]?.active ?? section.is_active;
        return active ? <SectionVariantRenderer key={section.landing_section_id || key} section={section} data={data} /> : null;
    })}</>;
}
