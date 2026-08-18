import React from 'react';
import { motion, useMotionTemplate, useMotionValue, useSpring, useTransform } from 'framer-motion';
import { Section, combinedText } from '../index';

/** Kartu tilt 3D — miring mengikuti kursor + kilau halus (framer-motion). */
function TiltCard({ children }) {
    const x = useMotionValue(0);
    const y = useMotionValue(0);
    const rotateX = useSpring(useTransform(y, [-0.5, 0.5], [9, -9]), { stiffness: 220, damping: 18 });
    const rotateY = useSpring(useTransform(x, [-0.5, 0.5], [-9, 9]), { stiffness: 220, damping: 18 });
    const glareX = useSpring(useTransform(x, [-0.5, 0.5], [15, 85]), { stiffness: 200, damping: 20 });
    const glareY = useSpring(useTransform(y, [-0.5, 0.5], [15, 85]), { stiffness: 200, damping: 20 });
    const glare = useMotionTemplate`radial-gradient(300px circle at ${glareX}% ${glareY}%, rgba(255,255,255,.32), transparent 65%)`;

    const onMouseMove = event => {
        const rect = event.currentTarget.getBoundingClientRect();
        x.set((event.clientX - rect.left) / rect.width - 0.5);
        y.set((event.clientY - rect.top) / rect.height - 0.5);
    };
    const onMouseLeave = () => {
        x.set(0);
        y.set(0);
    };

    return (
        <motion.div
            className="feature-tilt"
            style={{ rotateX, rotateY, transformStyle: 'preserve-3d' }}
            onMouseMove={onMouseMove}
            onMouseLeave={onMouseLeave}
        >
            <div style={{ transform: 'translateZ(34px)' }}>{children}</div>
            <motion.span className="feature-tilt__glare" style={{ background: glare }} aria-hidden="true" />
        </motion.div>
    );
}

/** Fitur Mode 4 — grid kartu ikon dengan efek tilt 3D mengikuti kursor. Prop: { section, data } */
export default function FeatureMode4({ section, data }) {
    const features = data.landing?.features || [];
    if (!features.length) return null;
    const limit = section?.limit_data || 6;

    return (
        <Section
            section={section}
            id="keunggulan"
            eyebrow={section.pre_title || 'Apa yang Kami Tawarkan'}
            title={section.title || 'Fitur Unggulan'}
            text={combinedText(section)}
        >
            <div className="feature-grid feature-grid--tilt">
                {features.slice(0, limit).map(feature => (
                    <TiltCard key={feature.id}>
                        <div className="feature-icon-card">
                            {feature.icon && <span className={`feature-icon ${feature.icon}`} aria-hidden="true" />}
                            <h3>{feature.title}</h3>
                            <p>{feature.description}</p>
                        </div>
                    </TiltCard>
                ))}
            </div>
        </Section>
    );
}
