import React, { useState } from 'react';
import { Plus } from 'lucide-react';
import { Section, combinedText } from '../index';
import FaqReveal from './FaqReveal';

/**
 * FAQ Mode 1 — tanya-jawab editorial: daftar bersih dengan garis pembatas,
 * ikon + berputar jadi ×. Ringan, fokus teks, tanpa kartu.
 * Jawaban muncul dengan animasi height + opacity (FaqReveal).
 * Prop: { section, data }
 */
export default function FaqMode1({ section, data }) {
    const faqs = data.faqs || [];
    if (!faqs.length) return null;
    const limit = section?.limit_data || 8;
    const [open, setOpen] = useState(0);

    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Butuh jawaban?'}
            title={section.title || 'Temukan informasi dengan lebih cepat'}
            text={combinedText(section)}
            narrow
        >
            <div className="faq-lines">
                {faqs.slice(0, limit).map((faq, index) => (
                    <div key={faq.id} className={`faq-line${open === index ? ' is-open' : ''}`}>
                        <button
                            type="button"
                            className="faq-line-q"
                            onClick={() => setOpen(open === index ? null : index)}
                            aria-expanded={open === index}
                        >
                            <span>{faq.question}</span>
                            <span className="faq-line-icon" aria-hidden="true"><Plus size={16} /></span>
                        </button>
                        <FaqReveal open={open === index}>
                            <p className="faq-line-a">{faq.answer}</p>
                        </FaqReveal>
                    </div>
                ))}
            </div>
        </Section>
    );
}
