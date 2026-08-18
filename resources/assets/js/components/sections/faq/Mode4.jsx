import React, { useState } from 'react';
import { AnimatePresence, motion, useReducedMotion } from 'framer-motion';
import { Plus } from 'lucide-react';
import { Stagger } from '@public/components/motion/effects';
import { Section, combinedText } from '../index';

/** FAQ Mode 4 — accordion animasi: kartu melebar halus, satu terbuka. Prop: { section, data } */
export default function FaqMode4({ section, data }) {
    const faqs = data.faqs || [];
    const [open, setOpen] = useState(() => (faqs.length ? `faq-${faqs[0].id}` : null));
    const reduceMotion = useReducedMotion();

    if (!faqs.length) return null;
    const limit = section?.limit_data || 8;

    return (
        <Section
            section={section}
            eyebrow={section.pre_title || 'Butuh jawaban?'}
            title={section.title || 'Temukan informasi dengan lebih cepat'}
            text={combinedText(section)}
            narrow
        >
            <Stagger className="faq-anim-list">
                {faqs.slice(0, limit).map((faq, index) => {
                    const value = `faq-${faq.id}`;
                    const isOpen = open === value;

                    return (
                        <div key={faq.id} className={`faq-anim-card gen-card${isOpen ? ' is-open' : ''}`}>
                            <button
                                type="button"
                                className="faq-anim-trigger"
                                aria-expanded={isOpen}
                                aria-controls={`${value}-panel`}
                                onClick={() => setOpen(isOpen ? null : value)}
                            >
                                <span className="faq-anim-num">{String(index + 1).padStart(2, '0')}</span>
                                <span className="faq-anim-q">
                                    {faq.question}
                                    {faq.category && <em className="faq-anim-cat">{faq.category}</em>}
                                </span>
                                <span className="faq-anim-icon"><Plus size={18} /></span>
                            </button>
                            <AnimatePresence initial={false}>
                                {isOpen && (
                                    <motion.div
                                        id={`${value}-panel`}
                                        className="faq-anim-panel"
                                        initial={reduceMotion ? false : { height: 0, opacity: 0 }}
                                        animate={{ height: 'auto', opacity: 1 }}
                                        exit={reduceMotion ? undefined : { height: 0, opacity: 0 }}
                                        transition={{ duration: 0.32, ease: [0.22, 1, 0.36, 1] }}
                                    >
                                        <p>{faq.answer}</p>
                                    </motion.div>
                                )}
                            </AnimatePresence>
                        </div>
                    );
                })}
            </Stagger>
        </Section>
    );
}
