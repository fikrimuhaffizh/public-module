export function sectionKey(section) {
    const key = section?.section_key || section?.key;
    return ({ products: 'product', stats: 'statistic', features: 'feature', testimonials: 'testimonial', clients: 'client', announcement: 'pengumuman' })[key] || key;
}
