<?php
namespace Assets;

use Assets;
use League\Uri\Contracts\UriInterface;
use League\Uri\Uri;
use ScssPhp\ScssPhp\Importer\Importer;
use ScssPhp\ScssPhp\Importer\ImporterResult;
use ScssPhp\ScssPhp\Syntax;
use ScssPhp\ScssPhp\ValueConverter;

use ScssPhp\ScssPhp\Compiler as ScssCompiler;
use ScssPhp\ScssPhp\OutputStyle;
use Studip\Cache\Factory;

/**
 * SCSS Compiler for assets.
 *
 * Uses scssphp <https://scssphp.github.io/scssphp/>.
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since   Stud.IP 4.4
 */
class SASSCompiler implements Compiler
{
    const CACHE_KEY = '/assets/sass-prefix';

    private static $instance = null;

    /**
     * Returns an instance of the compiler
     * @return Assets\SASSCompiler instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to enforce singleton.
     */
    private function __construct()
    {
    }

    /**
     * Compiles a scss string. This method will add all neccessary imports
     * and variables for Stud.IP so almost all mixins and variables of the
     * core system can be used. This includes colors and icons.
     *
     * @param String $input      Scss content to compile
     * @param array  $variables Additional variables for the SCSS compilation
     * @return String containing the generated CSS
     */
    public function compile($input, array $variables = [])
    {
        $scss = $this->getPrefix() . $input;

        $variables['image-path'] = Assets::url('images');

        $variables = array_map(
            [ValueConverter::class, 'fromPhp'],
            $variables
        );

        $compiler = new ScssCompiler();
        $compiler->addImportPath("{$GLOBALS['STUDIP_BASE_PATH']}/resources/");
        $compiler->addImporter($this->getCustomImporter());
        $compiler->addVariables($variables);
        if (\Studip\ENV === 'production') {
            $compiler->setOutputStyle(OutputStyle::COMPRESSED);
        } else {
            $compiler->setOutputStyle(OutputStyle::EXPANDED);
            $compiler->setSourceMap(ScssCompiler::SOURCE_MAP_INLINE);
        }
        $css = $compiler->compileString($scss)->getCss();
        $css = preg_replace('~/\*.*?\*/~s', '', $css);
        $css = trim($css);
        return $css;
    }

    /**
     * Generates the scss prefix containing the variables and mixins of the
     * Stud.IP core system.
     * This prefix will be cached in Stud.IP's cache in order to minimize
     * disk accesses.
     *
     * @return String containing the neccessary prefix
     */
    private function getPrefix()
    {
        $cache = Factory::getCache();

        $prefix = $cache->read(self::CACHE_KEY);

        if ($prefix === false) {
            $prefix = '';

            // Load mixins and change relative to absolute filenames
            $mixin_file = $GLOBALS['STUDIP_BASE_PATH'] . '/resources/assets/stylesheets/mixins.scss';
            foreach (file($mixin_file) as $mixin) {
                if (!preg_match('/@import "(.*)";/', $mixin, $match)) {
                    continue;
                }

                $core_file = "assets/stylesheets/{$match[1]}";
                $prefix .= sprintf('@import "%s";' . "\n", $core_file);
            }

            // Add adjusted image paths
            $prefix .= sprintf('$image-path: "%s";', Assets::url('images')) . "\n";
            $prefix .= '$icon-path: "#{$image-path}/icons";' . "\n";

            $cache->write(self::CACHE_KEY, $prefix);
        }

        return $prefix;
    }

    /**
     * Creates a custom importer that maps @studip-ui to the corresponding
     * directory. The importer tries the .sass and .scss extension if no
     * extensio is present and will also try to find a corresponding file
     * with a _ in front of it.
     */
    private function getCustomImporter(): Importer
    {
        return new class extends Importer
        {
            private const MAPPINGS = [
                '@studip-ui' => 'packages/studip-ui/src',
            ];

            public function __toString(): string
            {
                return '';
            }

            public function canonicalize(UriInterface $url): ?UriInterface
            {
                foreach (self::MAPPINGS as $package => $location) {
                    if (str_starts_with($url->getPath(), $package . '/')) {
                        $absolutePath = str_replace(
                            $package . '/',
                            $GLOBALS['STUDIP_BASE_PATH'] . '/' . $location . '/',
                            $url->getPath()
                        );

                        return Uri::new('file://' . $absolutePath);
                    }
                }


                return null;
            }

            private function getCandidates(UriInterface $url): array
            {
                $extensions = [''];
                $candidates = [$url->getPath()];

                $extension = pathinfo($url->getPath(), PATHINFO_EXTENSION);
                if (!$extension) {
                    $extensions = ['.sass', '.scss'];
                    $candidates = array_map(
                        fn($ext) => $url->getPath() . $ext,
                        $extensions
                    );
                }

                $dir = pathinfo($url->getPath(), PATHINFO_DIRNAME);
                $base = pathinfo($url->getPath(), PATHINFO_BASENAME);

                if (!str_starts_with($base, '_')) {
                    foreach ($extensions as $ext) {
                        $candidates[] = $dir . '/_' . $base . $ext;
                    }
                }

                return $candidates;
            }

            public function load(UriInterface $url): ?ImporterResult
            {
                $contents = false;

                foreach ($this->getCandidates($url) as $candidate) {
                    if (file_exists($candidate)) {
                        $contents = file_get_contents($candidate);
                        break;
                    }
                }

                if ($contents === false) {
                    throw new \Exception("Could not read file {$url->getPath()}");
                }

                return new ImporterResult(
                    $contents,
                    Syntax::forPath($url->getPath()),
                    $url
                );
            }
        };
    }
}
