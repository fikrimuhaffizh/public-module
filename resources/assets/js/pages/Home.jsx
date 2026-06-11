import React from 'react';
import { usePage } from '@inertiajs/react';
import { SiteLayout, TemplatePicker } from '@public/layouts/PublicLayout';
import InstitutionalTemplate from '@public/templates/InstitutionalTemplate';
import ModernTemplate from '@public/templates/ModernTemplate';
import EditorialTemplate from '@public/templates/EditorialTemplate';
import CorporateTemplate from '@public/templates/CorporateTemplate';

const templates = {
    institutional: InstitutionalTemplate,
    modern: ModernTemplate,
    editorial: EditorialTemplate,
    corporate: CorporateTemplate,
};

export default function Home() {
    const data = usePage().props;
    const Template = templates[data.template] || InstitutionalTemplate;
    return <SiteLayout {...data}>{data.preview && <TemplatePicker template={data.template} />}<Template data={data} /></SiteLayout>;
}
