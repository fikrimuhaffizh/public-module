import React from 'react';
import { AnimatePresence, motion, useReducedMotion } from 'framer-motion';

/**
 * Panel jawaban FAQ dengan transisi halus tinggi + opacity (pola Mode4 FAQ).
 * Dipakai bersama oleh FAQ Mode 1 (garis editorial) dan Mode 3 (kartu)
 * supaya seluruh mode FAQ punya bahasa animasi yang konsisten.
 * Prop: { open, children }
 */
export default function FaqReveal({ open, children }) {
    const reduceMotion = useReducedMotion();

    return (
        <AnimatePresence initial={false}>
            {open && (
                <motion.div
                    style={{ overflow: 'hidden' }}
                    initial={reduceMotion ? false : { height: 0, opacity: 0 }}
                    animate={{ height: 'auto', opacity: 1 }}
                    exit={reduceMotion ? undefined : { height: 0, opacity: 0 }}
                    transition={{ duration: 0.32, ease: [0.22, 1, 0.36, 1] }}
                >
                    {children}
                </motion.div>
            )}
        </AnimatePresence>
    );
}
