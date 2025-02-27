/** @type {import('@docusaurus/types').DocusaurusConfig} */
module.exports = {
  title: 'Stud.IP Entwicklung',
  tagline: 'Dokumentation für die Entwicklung von Stud.IP',
  url: 'https://docs.gitlab.studip.de',
  baseUrl: '/entwicklung/',
  onBrokenLinks: 'warn',
  onBrokenMarkdownLinks: 'warn',
  favicon: 'img/favicon.ico',
  organizationName: 'Stud.IP',
  projectName: 'entwicklung',
  favicon: 'https://www.studip.de/favicon.ico',
  trailingSlash: false,
  markdown: {
    mermaid: true,
  },
  i18n: {
    defaultLocale: 'de',
    locales: ['de'],
  },
  themeConfig: {
    prism: {
      additionalLanguages: ['php', 'sass'],
    },
    navbar: {
      logo: {
        alt: 'Stud.IP Entwicklung',
        src: 'img/studip-hilfe.png',
      },
      items: [
        {
          to: 'docs/quickstart/',
          activeBasePath: 'docs/quickstart',
          label: 'Quickstart',
          position: 'left',
        },
        {
          to: 'docs/start',
          activeBasePath: 'docs/start',
          label: 'Dokumentation',
          position: 'left',
        },
        {
          to: 'docs/rules/introduction',
          activeBasePath: 'docs/rules/introduction',
          label: 'Organisation',
          position: 'left',
        },
        {
          href: 'https://docs.gitlab.studip.de/api',
          label: 'API',
          position: 'right',
        },
        {
          href: 'https://gitlab.studip.de',
          label: 'Stud.IP GitLab',
          position: 'right',
        },
      ],
    }
  },
  presets: [
    [
      '@docusaurus/preset-classic',
      {
        docs: {
          sidebarPath: require.resolve('./sidebars.js'),
          // Please change this to your repo.
          editUrl: 'https://gitlab.studip.de/docs/entwicklung/-/tree/main/website',
          showLastUpdateTime: true,
          showLastUpdateAuthor: true,
        },
        theme: {
          customCss: require.resolve('./src/css/custom.css'),
        },
      },
    ],
  ],
  plugins: [
    [
      require.resolve("@cmfcmf/docusaurus-search-local"),
      {
        language: 'de'// Options here
      },
    ],
  ],
  themes: ['@docusaurus/theme-mermaid'],
};
