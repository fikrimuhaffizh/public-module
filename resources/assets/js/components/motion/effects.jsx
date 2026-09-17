import React from 'react';
import { motion, useMotionTemplate, useMotionValue, useReducedMotion } from 'framer-motion';
import { cn } from '@public/lib/utils';

/**
 * Motion budget - maksimal animasi "ekstra" (di luar heading section)
 * yang boleh hidup per halaman. Satu momen terorkestrasi, bukan belasan
 * reveal yang saling berebut perhatian.
 *
 * Mekanisme: komponen yang memakai `budgeted` mengklaim satu slot saat
 * mount (urutan DOM = urutan klaim). Slot habis ke render statis, tanpa
 * error. Provider di-remount per navigasi (key = URL) sehingga budget
 * segar di tiap halaman. Aman StrictMode: klaim dilepas saat unmount.
 */
export const MOTION_BUDGET = 6;

const MotionBudgetContext = React.createContext(null);

export function MotionBudgetProvider({ max = MOTION_BUDGET, children }) {
    const used = React.useRef(0);
    const api = React.useMemo(() => ({
        claim: () => {
            if (used.current >= max) return false;
            used.current += 1;
            return true;
        },
        release: () => {
            used.current = Math.max(0, used.current - 1);
        },
    }), [max]);
    return <MotionBudgetContext.Provider value={api}>{children}</MotionBudgetContext.Provider>;
}

function useMotionSlot(budgeted) {
    const budget = React.useContext(MotionBudgetContext);
    // Tanpa budgeted / tanpa provider: perilaku lama (selalu animasi).
    const [allowed, setAllowed] = React.useState(() => !budgeted || !budget);
    React.useEffect(() => {
        if (!budgeted || !budget) { setAllowed(true); return; }
        let active = true;
        if (budget.claim()) {
            if (active) setAllowed(true);
        }
        return () => { active = false; budget.release(); };
    }, [budgeted, budget]);
    return allowed;
}

export function Reveal({ children, className, delay = 0, y = 26, x = 0, scale = 1, budgeted = false }) {
    const reduceMotion = useReducedMotion();
    const allowed = useMotionSlot(budgeted);
    const from = scale !== 1 ? { opacity: 0, scale } : x ? { opacity: 0, x } : { opacity: 0, y };
    const to = scale !== 1 ? { opacity: 1, scale: 1 } : x ? { opacity: 1, x: 0 } : { opacity: 1, y: 0 };
    if (reduceMotion || !allowed) {
        return <div className={className}>{children}</div>;
    }
    return (
        <motion.div
            className={className}
            initial={from}
            whileInView={to}
            viewport={{ once: true, amount: 0.2 }}
            transition={{ duration: 0.65, delay, ease: [0.22, 1, 0.36, 1] }}
        >
            {children}
        </motion.div>
    );
}

export function Stagger({ children, className, itemClassName, budgeted = false }) {
    const reduceMotion = useReducedMotion();
    const allowed = useMotionSlot(budgeted);
    if (reduceMotion || !allowed) {
        return (
            <div className={className}>
                {React.Children.map(children, (child, index) => (
                    itemClassName
                        ? <div key={index} className={itemClassName}>{child}</div>
                        : <React.Fragment key={index}>{child}</React.Fragment>
                ))}
            </div>
        );
    }
    return (
        <motion.div
            className={className}
            initial="hidden"
            whileInView="visible"
            viewport={{ once: true, amount: 0.12 }}
            variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.08 } } }}
        >
            {React.Children.map(children, (child, index) => (
                <motion.div key={index} className={itemClassName} variants={{ hidden: { opacity: 0, y: 22 }, visible: { opacity: 1, y: 0, transition: { duration: 0.55 } } }}>
                    {child}
                </motion.div>
            ))}
        </motion.div>
    );
}

export function SpotlightCard({ children, className }) {
    const reduceMotion = useReducedMotion();
    const mouseX = useMotionValue(-200);
    const mouseY = useMotionValue(-200);
    const background = useMotionTemplate`radial-gradient(280px circle at ${mouseX}px ${mouseY}px, rgba(64, 124, 255, .16), transparent 72%)`;

    return (
        <motion.div
            className={cn('spotlight-card', className)}
            onMouseMove={(event) => {
                if (reduceMotion) return;
                const rect = event.currentTarget.getBoundingClientRect();
                mouseX.set(event.clientX - rect.left);
                mouseY.set(event.clientY - rect.top);
            }}
            whileHover={reduceMotion ? undefined : { y: -5 }}
            transition={{ type: 'spring', stiffness: 260, damping: 22 }}
        >
            {!reduceMotion && <motion.div className="spotlight-card__glow" style={{ background }} />}
            <div className="spotlight-card__content">{children}</div>
        </motion.div>
    );
}

export function BackgroundBeams() {
    const reduceMotion = useReducedMotion();
    return (
        <div className="background-beams" aria-hidden="true">
            {Array.from({ length: 8 }).map((_, index) => (
                <motion.span
                    key={index}
                    style={{ left: `${8 + index * 13}%` }}
                    animate={reduceMotion ? undefined : { opacity: [0.08, 0.35, 0.08], y: [0, -28, 0] }}
                    transition={reduceMotion ? undefined : { duration: 6 + index * 0.35, delay: index * -0.6, repeat: Infinity, ease: 'easeInOut' }}
                />
            ))}
        </div>
    );
}

export function Marquee({ items }) {
    const reduceMotion = useReducedMotion();
    const content = [...items, ...items];
    return (
        <div className="marquee" aria-label="Keunggulan institusi">
            {reduceMotion ? (
                <div className="marquee-track marquee-track--static">
                    {content.map((item, index) => <span key={`${item}-${index}`}>{item}<i /></span>)}
                </div>
            ) : (
                <motion.div className="marquee-track" animate={{ x: ['0%', '-50%'] }} transition={{ duration: 28, repeat: Infinity, ease: 'linear' }}>
                    {content.map((item, index) => <span key={`${item}-${index}`}>{item}<i /></span>)}
                </motion.div>
            )}
        </div>
    );
}
