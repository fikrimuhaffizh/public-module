import React, { useState } from 'react';
import { Section, combinedText } from '../index';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@public/components/ui/accordion';

/**
 * FAQ Mode 2 — filter kategori: pill di atas, daftar accordion di bawah.
 * Tiap pill menampilkan jumlah pertanyaan kategori-nya.
 * Klik kategori → hanya pertanyaan kategori itu yang tampil.
 * Prop: { section, data }
 */
export default function FaqMode2({ section, data }) {
    const faqs = data.faqs || [];
    if (!faqs.length) return null;

    const countBy = {};
    faqs.forEach((f) => {
        const key = f.category || 'FAQ';
        countBy[key] = (countBy[key] || 0) + 1;
    });
    const cats = ['Semua', ...Object.keys(countBy)];
    const [cat, setCat] = useState('Semua');
    const filtered = cat === 'Semua' ? faqs : faqs.filter((f) => (f.category || 'FAQ') === cat);

    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Butuh jawaban?'}
            title={section.title || 'Temukan informasi dengan lebih cepat'}
            text={combinedText(section)}
        >
            {cats.length > 1 && (
                <div className="faq-cats" role="tablist" aria-label="Filter kategori">
                    {cats.map((c) => (
                        <button
                            key={c}
                            type="button"
                            role="tab"
                            aria-selected={cat === c}
                            className={`faq-cat${cat === c ? ' is-active' : ''}`}
                            onClick={() => setCat(c)}
                        >
                            <span>{c}</span>
                            <span className="faq-cat-count">
                                {c === 'Semua' ? faqs.length : countBy[c]}
                            </span>
                        </button>
                    ))}
                </div>
            )}
            <Accordion type="single" collapsible className="faq-list">
                {filtered.map((faq) => (
                    <AccordionItem key={faq.id} value={`faq-${faq.id}`}>
                        <AccordionTrigger>{faq.question}</AccordionTrigger>
                        <AccordionContent>{faq.answer}</AccordionContent>
                    </AccordionItem>
                ))}
            </Accordion>
        </Section>
    );
}
