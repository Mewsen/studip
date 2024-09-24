import Overlay from './overlay.js';

class APIError extends Error
{
    static createWithJqXhr(message, jqXhr) {
        const error = new APIError(message);
        error.setJqXhr(jqXhr);
        return error;
    }

    jqXhr = null;

    setJqXhr(jqXhr) {
        this.jqXhr = jqXhr;
    }
}

class AbstractAPI
{
    static get supportedMethods() {
        return ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'OPTIONS', 'DELETE'];
    }

    // Helper function that normalizes options
    static adjustOptions (options = {}) {
        return Object.assign({}, {
            method: 'get',
            parameters: {},
            headers: {},
            data: {},
            overlay: false,
            async: false,
            before: false
        }, options || {});
    }

    constructor (base_url) {
        if (this.constructor === AbstractAPI) {
            throw new TypeError('You should not instantiate the abstract api');
        }

        this.total_requests = 0;
        this.request_count = 0;
        this.queue = [];
        this.base_url = base_url;
    }

    encodeData (data, method) {
        if (data instanceof Function) {
            data = data();
        }
        return data;
    }

    request (url, options = {}) {
        // Normalize parameters
        if (Array.isArray(url)) {
            // Remove empty trailing chunks
            while (url[url.length - 1] === '') {
                delete url[url.length - 1];
            }
            // Convert array to string
            url = url.join('/');
        }

        options = this.constructor.adjustOptions(options);

        var deferred;

        const request = this.#createRequest(url, options);

        if (options.async && this.request_count > 0) {
            // Request should be sent asynchronous after every other request
            // is finished. The configuration for this particular request is
            // stored in a deferred which is then queued for execution.
            deferred = $.Deferred();
            deferred.then(() => this.request(url, options));

            this.queue.push(deferred);
        } else if (options.before instanceof Function && !options.before()) {
            // A before function was defined and returned false, so the request
            // is canceled
            deferred = $.Deferred((dfd) => dfd.reject());
        } else {
            // Increase request counters, show overlay if neccessary
            if (this.request_count === 0 && options.overlay) {
                Overlay.show(true, null, true);
            }
            this.request_count += 1;
            this.total_requests += 1;

            // Actual request
            deferred = $.ajax(request.url, {
                contentType: options.contentType || 'application/x-www-form-urlencoded; charset=UTF-8',
                method: options.method.toUpperCase(),
                data: this.encodeData(request.data, options.method.toUpperCase()),
                headers: options.headers
            }).always(() => {
                // Decrease request counter, remove overlay if neccessary
                this.request_count -= 1;
                if (this.request_count === 0 && options.overlay) {
                    Overlay.hide();
                }
            });
        }
        return deferred.always(() => {
            // Check if any request was queued
            if (this.request_count === 0 && this.queue.length > 0) {
                this.queue.shift().resolve();
            }
        }).promise();
    }

    #createRequest(url, options) {
        const hasBody = ['post', 'put', 'patch'].includes(options.method.toLowerCase());
        const query = hasBody ? '' : `?${this.convertDataToRequestParameters(options.data)}`;

        return {
            url: STUDIP.URLHelper.getURL(`${this.base_url}/${url}${query}`, {}, true),
            data: hasBody ? options.data : {},
        };
    }

    convertDataToRequestParameters(data, prefix = '') {
        return Object.entries(data).filter(([key, value]) => {
            return value !== null;
        }).map(([key, value]) => {
            const name = prefix ? `${prefix}[${key}]` : `${key}`;
            if (value.constructor?.name === 'Object') {
                return this.convertDataToRequestParameters(value, name);
            } else {
                return `${name}=${value}`;
            }
        }).join('&');
    }

    withPromises() {
        return new Proxy(this, {
            get(target, prop, receiver) {
                // This will allow http methods to be written as lowercase when called as methods
                // (e.g. api.patch() instead of api.PATCH())
                if (target[prop] === undefined && AbstractAPI.supportedMethods.includes(prop.toUpperCase())) {
                    prop = prop.toUpperCase();
                }

                // Only handle calls to request methods
                if (prop !== 'request') {
                    return Reflect.get(target, prop, receiver);
                }

                // Return a wrapped promise that handles the deferred
                return (url, options = {}) => new Promise((resolve, reject) => {
                    target[prop].apply(target, [url, options]).then(
                        (response) => resolve(response),
                        (jqXhr, textStatus, errorThrown) => reject(APIError.createWithJqXhr(errorThrown || textStatus, jqXhr))
                    );
                });
            }
        })
    }
}

// Create shortcut methods for easier access by method
AbstractAPI.supportedMethods.forEach((method) => {
    AbstractAPI.prototype[method] = function (url, options = {}) {
        options = this.constructor.adjustOptions(options);
        options.method = method;

        return this.request.call(this, url, options);
    };
});

export default AbstractAPI;
