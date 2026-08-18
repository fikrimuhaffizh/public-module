import React from 'react';
import { CtaSection } from '../index';

/** CTA Mode 1 — terpusat standar (CtaSection). Prop: { section, data } */
export default function CtaMode1({ section, data }) {
    return <CtaSection site={data.site} section={section} />;
}
