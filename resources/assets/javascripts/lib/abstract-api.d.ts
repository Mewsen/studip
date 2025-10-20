type HttpMethod = 'GET' | 'HEAD' | 'POST' | 'PUT' | 'PATCH' | 'OPTIONS' | 'DELETE';
type HttpMethodLower = Lowercase<HttpMethod>;
type DataType = string | null | object;

interface RequestOptions {
    method?: HttpMethod | HttpMethodLower;
    parameters?: Record<string, unknown>;
    headers?: Record<string, string>;
    data?: unknown;
    overlay?: boolean;
    async?: boolean;
    before?: (() => boolean) | false;
    contentType?: string;
}

declare class APIError extends Error {
    static createWithJqXhr(message: string, jqXhr: JQuery.jqXHR): APIError;
    jqXhr: JQuery.jqXHR | null;
    setJqXhr(jqXhr: JQuery.jqXHR): void;
}

/** Die "normale" (Deferred) API */
declare class AbstractAPI {
    static readonly supportedMethods: readonly HttpMethod[];

    constructor(base_url: string);

    encodeData(data: DataType , method: null|HttpMethod|HttpMethodLower): DataType;

    request<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;

    GET<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    HEAD<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    POST<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    PUT<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    PATCH<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    OPTIONS<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    DELETE<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;

    get<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    head<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    post<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    put<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    patch<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    options<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;
    delete<T = unknown>(url: string | string[], options?: RequestOptions): JQuery.jqXHR<T>;

    withPromises(): AbstractAPIPromises;
}

interface AbstractAPIPromises {
    request<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;

    GET<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    HEAD<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    POST<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    PUT<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    PATCH<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    OPTIONS<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    DELETE<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;

    get<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    head<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    post<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    put<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    patch<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    options<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
    delete<T = unknown>(url: string | string[], options?: RequestOptions): Promise<T>;
}

export { AbstractAPI as default, APIError, RequestOptions, HttpMethod, HttpMethodLower, DataType };
