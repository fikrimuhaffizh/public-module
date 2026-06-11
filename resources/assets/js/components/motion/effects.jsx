import React from 'react';
import { motion, useMotionTemplate, useMotionValue, useReducedMotion } from 'framer-motion';
import { cn } from '@public/lib/utils';

export function Reveal({ children, className, delay = 0, y = 26 }) {
    const reduceMotion = useReducedMotion();
    return (
        <motion.div
            className={className}
            initial={reduceMotion ? false : { opacity: 0, y }}
            whileInView={reduceMotion ? undefined : { opacity: 1, y: 0 }}
            viewport={{ once: true, amount: 0.2 }}
            transition={{ duration: 0.65, delay, ease: [0.22, 1, 0.36, 1] }}
        >
            {children}
        </motion.div>
    );
}

export function Stagger({ children, className }) {
    const reduceMotion = useReducedMotion();
    return (
        <motion.div
            className={className}
            initial={reduceMotion ? false : 'hidden'}
            whileInView={reduceMotion ? undefined : 'visible'}
            viewport={{ once: true, amount: 0.12 }}
            variants={{ hidden: {}, visible: { transition: { staggerChildren: 0.08 } } }}
        >
            {React.Children.map(children, (child, index) => (
                <motion.div key={index} variants={{ hidden: { opacity: 0, y: 22 }, visible: { opacity: 1, y: 0, transition: { duration: 0.55 } } }}>
                    {child}
                </motion.div>
            ))}
        </motion.div>
    );
}

export function SpotlightCard({ children, className }) {
    const mouseX = useMotionValue(-200);
    const mouseY = useMotionValue(-200);
    const background = useMotionTemplate`radial-gradient(280px circle at ${mouseX}px ${mouseY}px, rgba(64, 124, 255, .16), transparent 72%)`;

    return (
        <motion.div
            className={cn('spotlight-card', className)}
            onMouseMove={(event) => {
                const rect = event.currentTarget.getBoundingClientRect();
                mouseX.set(event.clientX - rect.left);
                mouseY.set(event.clientY - rect.top);
            }}
            whileHover={{ y: -5 }}
            transition={{ type: 'spring', stiffness: 260, damping: 22 }}
        >
            <motion.div className="spotlight-card__glow" style={{ background }} />
            <div className="spotlight-card__content">{children}</div>
        </motion.div>
    );
}

export function BackgroundBeams() {
    return (
        <div className="background-beams" aria-hidden="true">
            {Array.from({ length: 8 }).map((_, index) => (
                <motion.span
                    key={index}
                    style={{ left: `${8 + index * 13}%` }}
                    animate={{ opacity: [0.08, 0.35, 0.08], y: [0, -28, 0] }}
                    transition={{ duration: 6 + index * 0.35, delay: index * -0.6, repeat: Infinity, ease: 'easeInOut' }}
                />
            ))}
        </div>
    );
}

export function Marquee({ items }) {
    const content = [...items, ...items];
    return (
        <div className="marquee" aria-label="Keunggulan institusi">
            <motion.div className="marquee-track" animate={{ x: ['0%', '-50%'] }} transition={{ duration: 28, repeat: Infinity, ease: 'linear' }}>
                {content.map((item, index) => <span key={`${item}-${index}`}>{item}<i /></span>)}
            </motion.div>
        </div>
    );
}
