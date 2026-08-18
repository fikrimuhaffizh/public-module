import React, { useState } from 'react';
import { ChevronDown } from 'lucide-react';
import { Section, combinedText } from '../index';
import FaqReveal from './FaqReveal';

/**
 * FAQ Mode 3 — kartu Q&A: grid dua kolom kartu, tiap kartu bisa dibuka
 * independen (multi-open). Kartu memakai .gen-card — bahasa visual
 * sama dengan kartu Produk/Harga/Fitur.
 * Jawaban muncul dengan animasi height + opacity (FaqReveal).
 * Prop: { section, data }
 */
export default function FaqMode3({ section, data }) {
    const faqs = data.faqs || [];
    if (!faqs.length) return null;
    const limit = section?.limit_data || 8;
    const [open, setOpen] = useState(() => new Set([0]));

    const toggle = (id) => {
        setOpen((prev) => {
            const next = new Set(prev);
            if (next.has(id)) next.delete(id);
            else next.add(id);
            return next;
        });
    };

    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Butuh jawaban?'}
            title={section.title || 'Temukan informasi dengan lebih cepat'}
            text={combinedText(section)}
        >
            <div className="faq-cards">
                {faqs.slice(0, limit).map((faq, index) => (
                    <article key={faq.id} className={`faq-card gen-card${open.has(index) ? ' is-open' : ''}`}>
                        <button
                            type="button"
                            className="faq-card-q"
                            onClick={() => toggle(index)}
                            aria-expanded={open.has(index)}
                        >
                            <span className="faq-card-cat">{faq.category || 'FAQ'}</span>
                            <span className="faq-card-text">{faq.question}</span>
                            <span className="faq-card-icon" aria-hidden="true"><ChevronDown size={17} /></span>
                        </button>
                        <FaqReveal open={open.has(index)}>
                            <p className="faq-card-a">{faq.answer}</p>
                        </FaqReveal>
                    </article>
                ))}
            </div>
        </Section>
    );
}
