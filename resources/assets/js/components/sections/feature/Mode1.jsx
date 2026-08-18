import React from 'react';
import { PlatformOverview } from '../index';

/** Fitur Mode 1 — PlatformOverview: teks + visual split. Prop: { section, data } */
export default function FeatureMode1({ section, data }) {
    return (
        <PlatformOverview
            site={data.site}
            image={data.landing?.hero?.image}
            pageCount={data.pages.length}
            section={section}
        />
    );
}
