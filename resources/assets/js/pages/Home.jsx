import React from 'react';
import { usePage } from '@inertiajs/react';
import { SiteLayout, TemplatePicker } from '@public/layouts/PublicLayout';
import ModernTemplate from '@public/templates/ModernTemplate';
import EditorialTemplate from '@public/templates/EditorialTemplate';
import CorporateTemplate from '@public/templates/CorporateTemplate';
import LaunchTemplate from '@public/templates/LaunchTemplate';

const templates = {
  modern: ModernTemplate,
  editorial: EditorialTemplate,
  corporate: CorporateTemplate,
  launch: LaunchTemplate,
};

export default function Home() {
    const data = usePage().props;
    const Template = templates[data.template] || ModernTemplate;
    return <SiteLayout {...data}>{data.preview && <TemplatePicker template={data.template} />}<Template data={data} /></SiteLayout>;
}
