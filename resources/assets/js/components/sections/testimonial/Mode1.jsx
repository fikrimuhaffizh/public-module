import React from 'react';
import { TestimonialSection } from '../index';

/** Testimoni Mode 1 — grid kartu testimoni. Prop: { section, data } */
export default function TestimonialMode1({ section, data }) {
    return <TestimonialSection testimonials={data.testimonials} section={section} />;
}
