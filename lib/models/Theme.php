<?php

/**
 * Theme.php - Stud.IP Theme Model Class
 * 
 * @author    Ron Lucke <lucke@elan-ev.de>
 * @license   GPL2 or any later version
 * 
 * @property string $name
 * @property string $origin
 * @property string $version
 * @property string $studip_min_version
 * @property string $studip_max_version
 * @property string $author
 * @property string $description
 * @property \JSONArrayObject $values
 * @property string $type
 * @property int $mkdate database column
 * @property int $chdate database column
 */

 class Theme extends SimpleORMap
 {
    public const COLOR_KEY_CATEGORIES = [
        'brand' => [
            '--color--brand-primary' => '#28497c',
            '--color--brand-primary-contrast' => '#ffffff',
            '--color--brand-secondary' => '#28497c',
            '--color--brand-secondary-contrast' => '#ffffff',
        ],
        'general' => [
            '--color--global-background' => '#ffffff',
        ],
        'text' => [
            '--color--font-primary' => '#101010',
            '--color--font-secondary' => '#3c454e',
            '--color--font-inactive' => '#676767',
            '--color--font-inverted' => '#ffffff',
        ],
        'navigation' => [
            '--color--main-navigation-item' => '#28497c',
        ],
        'sidebar' => [
            '--color--sidebar-item' => '#28497c',
            '--color--sidebar-item-hover' => '#d60000',
        ],
        'content' => [
            '--color--highlight' => '#28497c',
            '--color--highlight-hover' => '#d60000',
            '--color--content-link' => '#28497c',
            '--color--content-link-hover' => '#d60000',
        ],
    ];

    protected static function configure($config = [])
    {
        $config['db_table'] = 'themes';
        $config['serialized_fields']['values'] = JSONArrayObject::class;

        parent::configure($config);
    }

    /**
     * @return static[]
     */
    public static function getActiveThemes(): array
    {
        return [
            'light' => self::getActiveLightTheme(),
            'dark' => self::getActiveDarkTheme(),
            'high-contrast' => self::getActiveHighContrastTheme(),
        ];
    }

    public static function getActiveLightTheme(): ?static
    {
        return self::findOneBySQL('active = 1 AND type = "light"');
    }

    public static function getActiveDarkTheme(): ?static
    {
        return self::findOneBySQL('active = 1 AND type = "dark"');
    }

    public static function getActiveHighContrastTheme(): ?static
    {
        return self::findOneBySQL('active = 1 AND type = "high-contrast"');
    }

    public static function getcolorKeyCategories(): array
    {
        return self::COLOR_KEY_CATEGORIES;
    }

 }