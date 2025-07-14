type CacheOption = 'off' | 'session' | 'local';

class IconLoader
{
    readonly #cacheKey: string = 'studip/svg-icons';

    #baseUrl: string;
    #useCache: CacheOption = 'off';

    #cache: Map<string, string>;
    #promises: Map<string, Promise<string>>;

    constructor(baseUrl: string, useCache: CacheOption = 'off')
    {
        this.#baseUrl = baseUrl;
        this.#useCache = useCache;

        this.#cache = new Map<string, string>(this.#initialState());
        this.#promises = new Map<string, Promise<string>>();
    }

    async load(shape: string): Promise<string>
    {
        if (this.#cache.has(shape)) {
            return this.#cache.get(shape)!;
        }

        if (this.#promises.has(shape)) {
            return this.#promises.get(shape)!;
        }

        const containsUrl = (shape: string): boolean => /\bhttps?:\/\/[^\s]+/i.test(shape);

        const url = containsUrl(shape) ? shape : `${this.#baseUrl}images/icons/blue/${shape}.svg`;

        const promise = (async () => {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    return '';
                }
                let svg = await response.text();

                svg = svg.replace(/fill="(?!none)[^"]+"/g, 'fill="currentColor"');
                svg = svg.replace(/(width|height)="[^"]+"/g, '');

                this.store(shape, svg);

                return svg;
            } catch(error) {
                console.error(`IconLoader: Fehler beim Laden von ${shape}`, error);
                return '';
            } finally {
                this.#promises.delete(shape);
            }
        })();

        this.#promises.set(shape, promise);

        return promise;
    }

    store(shape: string, svg: string): void
    {
        this.#cache.set(shape, svg);

        this.#getStorage()?.setItem(
            this.#cacheKey,
            JSON.stringify([...this.#cache])
        )
    }

    #getStorage(): Storage|null
    {
        if (this.#useCache === 'off') {
            return null;
        }
        return this.#useCache === 'session' ? sessionStorage : localStorage;
    }

    #initialState(): [string, string][]
    {
        const cached = this.#getStorage()?.getItem(this.#cacheKey);
        if (!cached) {
            return [];
        }

        try {
            return JSON.parse(cached);
        } catch {
            return [];
        }
    }
}

const defaultLoader = new IconLoader(window.STUDIP.ASSETS_URL, 'session');

export default defaultLoader;
export { IconLoader };
