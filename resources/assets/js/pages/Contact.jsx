import React from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { CircleCheck, Mail, MapPin, Phone, Send } from 'lucide-react';
import { SiteLayout } from '@public/layouts/PublicLayout';
import { Alert, AlertDescription } from '@public/components/ui/alert';
import { Button } from '@public/components/ui/button';
import { Card } from '@public/components/ui/card';
import { Input } from '@public/components/ui/input';
import { Label } from '@public/components/ui/label';
import { Textarea } from '@public/components/ui/textarea';
import { Reveal } from '@public/components/motion/effects';

export default function Contact() {
    const { site, menus, template, sections, flash } = usePage().props;
    const form = useForm({ name: '', email: '', subject: '', message: '' });
    const submit = event => {
        event.preventDefault();
        form.post(site.contactUrl, { preserveScroll: true, onSuccess: () => form.reset() });
    };
    const contactItems = [[MapPin, 'Alamat', site.address || 'Alamat institusi belum diatur'], [Mail, 'Email', site.email || 'Email institusi belum diatur'], [Phone, 'Telepon', site.phone || 'Nomor telepon belum diatur']];
    return <SiteLayout title="Hubungi Kami" site={site} menus={menus} template={template} sections={sections}><main className="inner-page"><div className="shell">
        <Reveal className="inner-hero"><span className="eyebrow">Hubungi kami</span><h1>Mari memulai percakapan</h1><p>Sampaikan pertanyaan, kebutuhan layanan, atau peluang kolaborasi kepada tim kami.</p></Reveal>
        <div className="contact-grid"><div className="contact-info">{contactItems.map(([Icon, label, value]) => <Card className="contact-info-card" key={label}><Icon /><div><strong>{label}</strong><span>{value}</span></div></Card>)}</div>
        <Card className="contact-form-card">{flash?.success && <Alert className="form-success"><CircleCheck /><AlertDescription>{flash.success}</AlertDescription></Alert>}<form onSubmit={submit}><div className="form-grid"><Field label="Nama" name="name" form={form} /><Field label="Email" name="email" type="email" form={form} /><Field label="Subjek" name="subject" form={form} full /><Field label="Pesan" name="message" form={form} textarea full /></div><Button type="submit" disabled={form.processing}>{form.processing ? 'Mengirim...' : 'Kirim pesan'} <Send size={17} /></Button></form></Card></div>
    </div></main></SiteLayout>;
}

function Field({ label, name, form, type = 'text', textarea = false, full = false }) {
    const common = { id: name, value: form.data[name], onChange: event => form.setData(name, event.target.value) };
    return <div className={full ? 'form-field form-field--full' : 'form-field'}><Label htmlFor={name}>{label}</Label>{textarea ? <Textarea {...common} rows="6" /> : <Input {...common} type={type} />}{form.errors[name] && <small>{form.errors[name]}</small>}</div>;
}
