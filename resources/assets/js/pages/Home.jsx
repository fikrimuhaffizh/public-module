import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import { PublicPageLayout, TemplatePicker } from '@public/layouts/PublicLayout';
import ModernTemplate from '@public/templates/ModernTemplate';
import EditorialTemplate from '@public/templates/EditorialTemplate';
import CorporateTemplate from '@public/templates/CorporateTemplate';
import LaunchTemplate from '@public/templates/LaunchTemplate';
import AuroraTemplate from '@public/templates/AuroraTemplate';
import EnterpriseTemplate from '@public/templates/EnterpriseTemplate';
import RegistrationTemplate from '@public/templates/RegistrationTemplate';
import ProfileTemplate from '@public/templates/ProfileTemplate';
import CampusTemplate from '@public/templates/CampusTemplate';
import AdmissionsTemplate from '@public/templates/AdmissionsTemplate';
import TracerTemplate from '@public/templates/TracerTemplate';

const templates = {
  modern: ModernTemplate,
  editorial: EditorialTemplate,
  corporate: CorporateTemplate,
  launch: LaunchTemplate,
  aurora: AuroraTemplate,
  enterprise: EnterpriseTemplate,
  registration: RegistrationTemplate,
  profile: ProfileTemplate,
  campus: CampusTemplate,
  admissions: AdmissionsTemplate,
  tracer: TracerTemplate,
};

export default function Home() {
    const data = usePage().props;
    const Template = templates[data.template] || ModernTemplate;
    return <>
        <Head title={`${data.site.name}`}>
            <meta head-key="description" name="description" content={data.seo?.description || data.site.tagline || ''} />
            {data.seo?.title && <meta head-key="og:title" property="og:title" content={data.seo.title} />}
            {data.seo?.description && <meta head-key="og:description" property="og:description" content={data.seo.description} />}
        </Head>
        {data.preview && <TemplatePicker template={data.template} />}
        <Template data={data} />
    </>;
}

Home.layout = PublicPageLayout;
