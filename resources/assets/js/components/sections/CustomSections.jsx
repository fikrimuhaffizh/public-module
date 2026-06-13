import React from 'react';
import { Link } from '@inertiajs/react';
import {
  ArrowRight,
  BarChart3,
  BookOpen,
  Building2,
  CalendarDays,
  Check,
  GraduationCap,
  LayoutDashboard,
  ShieldCheck,
  Sparkles,
  Star,
  Users,
  Workflow,
  Menu,
  X,
} from 'lucide-react';
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@public/components/ui/accordion';
import { Badge } from '@public/components/ui/badge';
import { Button } from '@public/components/ui/button';
import { Reveal, SpotlightCard, Stagger } from '@public/components/motion/effects';

function alignClass(section) {
  const align = section?.settings?.text_align || 'center';
  const map = { left: 'text-left', center: 'text-center', right: 'text-right' };
  return map[align] || 'text-center';
}

function SectionHeader({ section, className = "" }) {
  if (!section.title && !section.pre_title && !section.subtitle && !section.post_title) {
    return null;
  }

  return (
    <Reveal className={`${alignClass(section)} mb-12 ${className}`}>
      {section.pre_title && <p className="text-sm font-semibold uppercase tracking-wider text-primary mb-2">{section.pre_title}</p>}
      {section.title && <h2 className="text-2xl md:text-3xl font-bold mb-4">{section.title}</h2>}
      {section.post_title && <p className="text-lg text-muted mb-4">{section.post_title}</p>}
      {section.subtitle && <p className="text-muted">{section.subtitle}</p>}
    </Reveal>
  );
}

export function NavbarSection({ section, data }) {
  const [open, setOpen] = React.useState(false);

  const variant = section.variant || 'navbar_1';

  return (
    <header className="site-header">
      <div className="shell nav-wrap">
        <Link href={data.site.homeUrl} className="brand">
          {data.site.logo ? (
            <img src={data.site.logo} alt={data.site.name} className="brand-logo" />
          ) : (
            <span className="brand-mark">
              <GraduationCap size={24} />
            </span>
          )}
          <span>{data.site.name}</span>
        </Link>
        <nav className="desktop-nav">
          {data.menus.map((menu) =>
            menu.target === '_blank' ? (
              <a key={menu.id} href={menu.url} target="_blank" rel="noreferrer">
                {menu.title}
              </a>
            ) : (
              <Link key={menu.id} href={menu.url}>
                {menu.title}
              </Link>
            )
          )}
        </nav>
        <div className="nav-actions">
          {variant === 'navbar_2' && (
            <Button asChild>
              <a href={data.site.loginUrl}>Masuk</a>
            </Button>
          )}
          <button
            className="mobile-toggle"
            onClick={() => setOpen(!open)}
            aria-label="Buka navigasi"
          >
            {open ? <X /> : <Menu />}
          </button>
        </div>
      </div>
      {open && (
        <nav className="mobile-nav shell">
          {data.menus.map((menu) => (
            <Link key={menu.id} href={menu.url} onClick={() => setOpen(false)}>
              {menu.title}
            </Link>
          ))}
        </nav>
      )}
    </header>
  );
}

export function HeroSection({ section, data }) {
  const variant = section.variant || 'hero_1';
  const hero = data.landing?.hero;

  if (!hero) return null;

  if (variant === 'hero_2') {
    const align = alignClass(section);
    return (
      <section className={`hero-section ${align} py-20`}>
        <div className="shell">
          <Reveal>
            {section.pre_title && <p className="text-sm font-semibold uppercase tracking-wider text-primary mb-2">{section.pre_title}</p>}
            <h1 className="text-4xl md:text-5xl font-bold mb-4">{section.title || hero.title}</h1>
            {section.post_title && <p className="text-xl text-muted mb-4">{section.post_title}</p>}
            {section.subtitle && <p className="text-lg mb-8 max-w-2xl mx-auto">{section.subtitle}</p>}
            {hero.description && !section.subtitle && <p className="text-lg mb-8 max-w-2xl mx-auto">{hero.description}</p>}
            <div className={`flex flex-wrap gap-4 ${align === 'text-center' ? 'justify-center' : align === 'text-right' ? 'justify-end' : 'justify-start'}`}>
              {hero.buttonPrimary?.text && (
                <Button asChild size="lg">
                  <a href={hero.buttonPrimary.link || '#'}>{hero.buttonPrimary.text}</a>
                </Button>
              )}
              {hero.buttonSecondary?.text && (
                <Button asChild variant="outline" size="lg">
                  <a href={hero.buttonSecondary.link || '#'}>{hero.buttonSecondary.text}</a>
                </Button>
              )}
            </div>
          </Reveal>
        </div>
      </section>
    );
  }

  return (
    <section className="hero-section py-16">
      <div className="shell">
        <div className="hero-grid grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
          <Reveal>
            {section.pre_title && <p className="text-sm font-semibold uppercase tracking-wider text-primary mb-2">{section.pre_title}</p>}
            <h1 className="text-3xl md:text-4xl font-bold mb-4">{section.title || hero.title}</h1>
            {section.post_title && <p className="text-xl text-muted mb-4">{section.post_title}</p>}
            {section.subtitle && <p className="mb-8">{section.subtitle}</p>}
            {hero.description && !section.subtitle && <p className="mb-8">{hero.description}</p>}
            <div className="flex flex-wrap gap-4">
              {hero.buttonPrimary?.text && (
                <Button asChild>
                  <a href={hero.buttonPrimary.link || '#'}>{hero.buttonPrimary.text}</a>
                </Button>
              )}
              {hero.buttonSecondary?.text && (
                <Button asChild variant="outline">
                  <a href={hero.buttonSecondary.link || '#'}>{hero.buttonSecondary.text}</a>
                </Button>
              )}
            </div>
          </Reveal>
          {hero.image && (
            <Reveal delay={0.1}>
              <img src={hero.image} alt="Hero" className="rounded-lg shadow-lg w-full" />
            </Reveal>
          )}
        </div>
      </div>
    </section>
  );
}

export function StatsSection({ section, data }) {
  const variant = section.variant || 'stats_1';
  const stats = data.landing?.statistics || [];

  if (!stats.length) return null;

  return (
    <section className="stats-section py-12">
      <div className="shell">
        <SectionHeader section={section} />
        <Stagger className="grid grid-cols-2 md:grid-cols-4 gap-8">
          {stats.slice(0, section.limit_data || 4).map((stat) => (
            <div key={stat.id} className="text-center">
              <div className="text-4xl font-bold text-primary mb-2">{stat.value}</div>
              <div className="text-muted">{stat.label}</div>
            </div>
          ))}
        </Stagger>
      </div>
    </section>
  );
}

export function FeatureSection({ section, data }) {
  const variant = section.variant || 'feature_1';
  const features = data.landing?.features || [];

  if (!features.length) return null;

  return (
    <section className="feature-section py-16">
      <div className="shell">
        <SectionHeader section={section} />
        <Stagger className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {features.slice(0, section.limit_data || 6).map((feature) => (
            <SpotlightCard key={feature.id} className="feature-card">
              {feature.icon && <div className="text-3xl text-primary mb-4">{feature.icon}</div>}
              <h3 className="text-xl font-semibold mb-2">{feature.title}</h3>
              {feature.description && <p className="text-muted">{feature.description}</p>}
            </SpotlightCard>
          ))}
        </Stagger>
      </div>
    </section>
  );
}

export function ProductSection({ section, data }) {
  const variant = section.variant || 'product_1';
  const products = data.landing?.products || [];

  if (!products.length) return null;

  return (
    <section className="product-section py-16 bg-gray-50">
      <div className="shell">
        <SectionHeader section={section} />
        <Stagger className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {products.slice(0, section.limit_data || 6).map((product) => (
            <SpotlightCard key={product.id} className="product-card">
              {product.image && <img src={product.image} alt={product.name} className="w-full h-48 object-cover rounded-t-lg" />}
              <div className="p-4">
                <h3 className="text-xl font-semibold mb-2">{product.name}</h3>
                {product.shortDescription && <p className="text-muted mb-4">{product.shortDescription}</p>}
                {product.demoUrl && (
                  <Button asChild variant="outline">
                    <a href={product.demoUrl}>Lihat Demo</a>
                  </Button>
                )}
              </div>
            </SpotlightCard>
          ))}
        </Stagger>
      </div>
    </section>
  );
}

export function TestimonialSection({ section, data }) {
  const variant = section.variant || 'testimonial_1';
  const testimonials = data.testimonials || [];

  if (!testimonials.length) return null;

  return (
    <section className="testimonial-section py-16">
      <div className="shell">
        <SectionHeader section={section} />
        <Stagger className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {testimonials.slice(0, section.limit_data || 3).map((testimonial) => (
            <SpotlightCard key={testimonial.id} className="testimonial-card">
              <div className="testimonial-rating mb-4">
                {Array.from({ length: testimonial.rating || 5 }).map((_, i) => (
                  <Star key={i} size={16} fill="currentColor" className="text-yellow-500" />
                ))}
              </div>
              <blockquote className="mb-4">"{testimonial.quote}"</blockquote>
              <div className="flex items-center gap-3">
                {testimonial.photo ? (
                  <img src={testimonial.photo} alt={testimonial.name} className="w-12 h-12 rounded-full" />
                ) : (
                  <span className="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold">
                    {testimonial.name.charAt(0).toUpperCase()}
                  </span>
                )}
                <div>
                  <strong>{testimonial.name}</strong>
                  {testimonial.position && (
                    <small className="block text-muted">{testimonial.position}</small>
                  )}
                </div>
              </div>
            </SpotlightCard>
          ))}
        </Stagger>
      </div>
    </section>
  );
}

export function ClientSection({ section, data }) {
  const variant = section.variant || 'logos_1';
  const clients = data.landing?.clients || [];

  if (!clients.length) return null;

  return (
    <section className="client-section py-12 bg-gray-50">
      <div className="shell">
        <SectionHeader section={section} />
        <div className="flex flex-wrap justify-center gap-8 items-center">
          {clients.slice(0, section.limit_data || 8).map((client) => (
            <div key={client.id} className="opacity-70 hover:opacity-100 transition-opacity">
              {client.logo ? (
                <img src={client.logo} alt={client.name} className="h-12" />
              ) : (
                <span className="font-bold text-lg">{client.name}</span>
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

export function FaqSection({ section, data }) {
  const variant = section.variant || 'faq_1';
  const faqs = data.faqs || [];

  if (!faqs.length) return null;

  return (
    <section className="faq-section py-16">
      <div className="shell">
        <SectionHeader section={section} />
        <Accordion type="single" collapsible className="max-w-3xl mx-auto">
          {faqs.slice(0, section.limit_data || 8).map((faq) => (
            <AccordionItem key={faq.id} value={`faq-${faq.id}`}>
              <AccordionTrigger>{faq.question}</AccordionTrigger>
              <AccordionContent>
                <div dangerouslySetInnerHTML={{ __html: faq.answer }} />
              </AccordionContent>
            </AccordionItem>
          ))}
        </Accordion>
      </div>
    </section>
  );
}

export function AnnouncementSection({ section, data }) {
  const variant = section.variant || 'announcement_1';
  const announcements = data.announcements || [];

  if (!announcements.length) return null;

  return (
    <section className="announcement-section py-16 bg-gray-50">
      <div className="shell">
        <SectionHeader section={section} />
        <Stagger className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {announcements.slice(0, section.limit_data || 6).map((announcement) => (
            <SpotlightCard key={announcement.id} className="announcement-card">
              <img src={announcement.image} alt={announcement.title} className="w-full h-48 object-cover rounded-t-lg" />
              <div className="p-4">
                <div className="flex gap-2 mb-2">
                  <Badge>{announcement.type}</Badge>
                  <span className="text-sm text-muted">{announcement.date}</span>
                </div>
                <h3 className="text-xl font-semibold mb-2">
                  <Link href={announcement.url}>{announcement.title}</Link>
                </h3>
                <p className="text-muted mb-4">{announcement.excerpt}</p>
                <Link href={announcement.url} className="text-primary">
                  Baca Selengkapnya <ArrowRight size={16} className="inline" />
                </Link>
              </div>
            </SpotlightCard>
          ))}
        </Stagger>
      </div>
    </section>
  );
}

export function CtaSection({ section, data }) {
  const variant = section.variant || 'cta_1';
  const cta = data.landing?.cta;
  const align = alignClass(section);

  return (
    <section className="cta-section py-16 bg-primary text-white">
      <div className={`shell ${align}`}>
        <Reveal>
          {section.pre_title && <p className="text-sm font-semibold uppercase tracking-wider text-white/80 mb-2">{section.pre_title}</p>}
          {section.title && <h2 className="text-2xl md:text-3xl font-bold mb-4">{section.title}</h2>}
          {section.post_title && <p className="text-lg text-white/80 mb-4">{section.post_title}</p>}
          {section.subtitle && <p className="text-white/80 mb-8">{section.subtitle}</p>}
          {cta?.buttonText && (
            <Button asChild size="lg" variant="secondary">
              <a href={cta.buttonLink || '#'}>{cta.buttonText}</a>
            </Button>
          )}
        </Reveal>
      </div>
    </section>
  );
}

export function FooterSection({ section, data }) {
  const variant = section.variant || 'footer_1';

  return (
    <footer className="site-footer bg-gray-900 text-white py-12">
      <div className="shell footer-grid grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <div className="brand brand--footer flex items-center gap-2 mb-4">
            <span className="brand-mark">
              <GraduationCap size={24} />
            </span>
            <span className="text-xl font-bold">{data.site.name}</span>
          </div>
          <p className="text-gray-400">{data.site.tagline}</p>
        </div>
        <div>
          <strong className="block mb-4">Navigasi</strong>
          <div className="flex flex-col gap-2">
            <Link href={data.site.homeUrl} className="text-gray-400 hover:text-white">Beranda</Link>
            <Link href={data.site.contactUrl} className="text-gray-400 hover:text-white">Hubungi Kami</Link>
          </div>
        </div>
        <div>
          <strong className="block mb-4">Kontak</strong>
          <div className="flex flex-col gap-2 text-gray-400">
            {data.site.address && <span>{data.site.address}</span>}
            {data.site.email && (
              <a href={`mailto:${data.site.email}`} className="hover:text-white">{data.site.email}</a>
            )}
            <span>&copy; {new Date().getFullYear()} {data.site.name}</span>
          </div>
        </div>
      </div>
    </footer>
  );
}
