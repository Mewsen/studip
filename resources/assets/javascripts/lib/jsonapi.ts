import AbstractAPI from './abstract-api.js';

type APIOptions = Record<string, unknown>;

// Actual JSONAPI object
class JSONAPI extends AbstractAPI
{
    constructor(version: number = 1) {
        super(`jsonapi.php/v${version}`);
    }

    encodeData (data: object, method: string): string|null|object {
        data = super.encodeData(data);

        if (['DELETE', 'GET', 'HEAD'].includes(method)) {
            return data;
        }

        if (Object.keys(data).length === 0) {
            return null;
        }

        return JSON.stringify(data);
    }

    request (url: string, options: APIOptions = {}) {
        options.contentType = 'application/vnd.api+json';
        return super.request(url, options);
    }
}

export default JSONAPI;
export const jsonapi: JSONAPI = new JSONAPI();
