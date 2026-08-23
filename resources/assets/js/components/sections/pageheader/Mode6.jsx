import React from 'react';
import { motion } from 'framer-motion';
import { Home } from 'lucide-react';

/**
 * Pageheader Mode 6 — Gradient bg: gradient animasi + title center.
 */
export default function PageheaderMode6({ context, site }) {
    const { breadcrumb = [], title = '', subtitle = '' } = context || {};
    const ease = [0.22, 1, 0.36, 1];

    return (
        <section className="pageheader pageheader--gradient">
            <div className="shell" style={{ textAlign: 'center' }}>
                <motion.div
                    initial={{ opacity: 0, y: 16 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.5, ease }}
                >
                    <nav className="pageheader-breadcrumb pageheader-breadcrumb--light">
                        <Home size={14} />
                        {breadcrumb.map((b, i) => (
                            <React.Fragment key={i}>
                                <span className="pageheader-breadcrumb-sep">/</span>
                                {b.url ? <a href={b.url} style={{ color: 'rgba(255,255,255,.8)' }}>{b.label}</a> : <span style={{ color: '#fff' }}>{b.label}</span>}
                            </React.Fragment>
                        ))}
                    </nav>
                    <h1 style={{ color: '#fff' }}>{title}</h1>
                    {subtitle && <p className="pageheader-excerpt" style={{ color: 'rgba(255,255,255,.85)' }}>{subtitle}</p>}
                </motion.div>
            </div>
        </section>
    );
}
