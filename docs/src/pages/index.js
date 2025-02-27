import React from 'react';
import clsx from 'clsx';
import Layout from '@theme/Layout';
import Link from '@docusaurus/Link';
import useDocusaurusContext from '@docusaurus/useDocusaurusContext';
import useBaseUrl from '@docusaurus/useBaseUrl';
import styles from './styles.module.css';

const features = [
    {
        title: 'Fehler / BIEST berichten',
        imageUrl: 'https://develop.studip.de/studip/assets/images/icons/blue/exclaim-circle-full.svg',
        url: 'https://gitlab.studip.de/studip/studip/-/issues/new',
        description: (
            <>
                Haben Sie einen Fehler gefunden? Dann melden Sie diesen hier
            </>
        ),
        target: '__blank'
    },
    {
        title: 'Kontakt zur Community',
        imageUrl: 'https://develop.studip.de/studip/assets/images/icons/blue/community.svg',
        url: 'https://develop.studip.de/',
        description: (
            <>
               Kommen Sie und werden Sie ein Teil der Community
            </>
        ),
        target: '__blank'
    },
    {
        title: 'Entwicklungs-Chat',
        imageUrl: 'https://develop.studip.de/studip/assets/images/icons/blue/chat.svg',
        url: 'https://matrix.to/#/%23Stud.IP:matrix.org',
        description: (
            <>
                Hier bekommen Sie schnell und unkompliziert Hilfe
            </>
        ),
        target: '__blank'
    },
];

function Feature({imageUrl, title, description, url, target}) {
    const imgUrl = useBaseUrl(imageUrl);
    return (
        <div className={clsx('col col--4 landing-page-box', styles.feature)}>
            <a href={url} target={target}>
                {imgUrl && (
                    <div className="text--center">
                        <img className={styles.featureImage} src={imgUrl} alt={title} />
                    </div>
                )}
                <h3 className="text--center">{title}</h3>
                <p className="text--center">{description}</p>
            </a>
        </div>
    );
}
import {Redirect} from '@docusaurus/router';

export default function Home() {
    const context = useDocusaurusContext();
    const {siteConfig = {}} = context;
    return (
        <Layout
            title={`${siteConfig.title}`}
            description="Description will go into a meta tag in <head />">
            <header className={clsx('hero hero--primary', styles.heroBanner)}>
                <div className="container">
                    <h1 className="hero__title">{siteConfig.title}</h1>
                    <p className="hero__subtitle">{siteConfig.tagline}</p>
                    <div className={styles.buttons}>
                        <Link
                            className={clsx(
                                'button button--outline button--secondary button--lg main-entry-button',
                                styles.getStarted,
                            )}
                            to={useBaseUrl('docs/quickstart')}>
                            Zur Dokumentation
                        </Link>
                    </div>
                </div>
            </header>
            <main>
                {features && features.length > 0 && (
                    <section className={styles.features}>
                        <div className="container">
                            <div className="row">
                                {features.map((props, idx) => (
                                    <Feature key={idx} {...props} />
                                ))}
                            </div>
                        </div>
                    </section>
                )}
            </main>
        </Layout>
    );
}
