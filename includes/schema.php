<?php
/**
 * JSON-LD Schema generator
 *
 * Outputs GeneralContractor schema on every page.
 * Additional schemas passed via $extra_schemas array.
 */

$org_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'GeneralContractor',
    '@id' => SITE_URL . '/#organization',
    'name' => SITE_NAME,
    'url' => SITE_URL,
    'logo' => SITE_URL . '/assets/images/branding/logo-white-horizontal.svg',
    'image' => SITE_URL . '/assets/images/og-home.jpg',
    'description' => 'Moksha Construction is a licensed general contractor in Clarksville, TN serving Nashville, Atlanta, and the Southeast with residential, commercial, industrial, and religious construction services.',
    'telephone' => SITE_PHONE_RAW,
    'email' => SITE_EMAIL,
    'foundingLocation' => 'Clarksville, TN',
    'areaServed' => [
        ['@type' => 'State', 'name' => 'Tennessee'],
        ['@type' => 'State', 'name' => 'Texas'],
        ['@type' => 'State', 'name' => 'North Carolina'],
        ['@type' => 'State', 'name' => 'Georgia'],
        ['@type' => 'State', 'name' => 'South Carolina'],
        ['@type' => 'State', 'name' => 'Florida'],
    ],
    'address' => [
        [
            '@type' => 'PostalAddress',
            'streetAddress' => OFFICE_NASHVILLE['street'],
            'addressLocality' => OFFICE_NASHVILLE['city'],
            'addressRegion' => OFFICE_NASHVILLE['state'],
            'postalCode' => OFFICE_NASHVILLE['zip'],
            'addressCountry' => OFFICE_NASHVILLE['country'],
        ],
        [
            '@type' => 'PostalAddress',
            'streetAddress' => OFFICE_ATLANTA['street'],
            'addressLocality' => OFFICE_ATLANTA['city'],
            'addressRegion' => OFFICE_ATLANTA['state'],
            'postalCode' => OFFICE_ATLANTA['zip'],
            'addressCountry' => OFFICE_ATLANTA['country'],
        ],
    ],
    'sameAs' => [
        SOCIAL_INSTAGRAM,
        SOCIAL_FACEBOOK,
        SOCIAL_LINKEDIN,
    ],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Construction Services',
        'itemListElement' => [
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'General Contracting']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Construction Management']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Design & Build']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Residential Construction']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Commercial Construction']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Industrial Construction']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Religious & Cultural Construction']],
        ],
    ],
];

echo '<script type="application/ld+json">' . json_encode($org_schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

// Breadcrumb schema (if $breadcrumbs is set)
if (!empty($breadcrumbs)) {
    $breadcrumb_schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [],
    ];
    foreach ($breadcrumbs as $i => $crumb) {
        $item = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $crumb['name'],
        ];
        if (isset($crumb['url'])) {
            $item['item'] = SITE_URL . $crumb['url'];
        }
        $breadcrumb_schema['itemListElement'][] = $item;
    }
    echo '<script type="application/ld+json">' . json_encode($breadcrumb_schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

// FAQ schema (if $faqs is set)
if (!empty($faqs)) {
    $faq_schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [],
    ];
    foreach ($faqs as $faq) {
        $faq_schema['mainEntity'][] = [
            '@type' => 'Question',
            'name' => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['a'],
            ],
        ];
    }
    echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}

// Extra schemas (if $extra_schemas is set)
if (!empty($extra_schemas)) {
    foreach ($extra_schemas as $schema) {
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
