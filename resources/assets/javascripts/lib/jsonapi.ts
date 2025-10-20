import AbstractAPI, {HttpMethod, HttpMethodLower, DataType} from './abstract-api.js';
import {isObject} from "lodash";

type APIOptions = Record<string, unknown>;

// Actual JSONAPI object
class JSONAPI extends AbstractAPI
{
    constructor(version: number = 1) {
        super(`jsonapi.php/v${version}`);
    }

    encodeData(data: DataType, method: null|HttpMethod|HttpMethodLower): DataType {
        data = super.encodeData(data, method);

        if (method && ['DELETE', 'GET', 'HEAD'].includes(method)) {
            return data;
        }

        if (isObject(data) && Object.keys(data).length === 0) {
            return null;
        }

        return JSON.stringify(data);
    }

    request<T = unknown>(url: string, options: APIOptions = {}): JQuery.jqXHR<T> {
        options.contentType = 'application/vnd.api+json';
        return super.request(url, options);
    }
}

export default JSONAPI;
export const jsonapi: JSONAPI = new JSONAPI();
