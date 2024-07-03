import axios from "axios";

interface ChunkedRequest {
    url: string,
    parameters: object,
    resolve(value: any): any,
    reject(): any,
}

export default class ChunkedRequester
{
    #requests: ChunkedRequest[] = [];

    readonly #delay: number;
    readonly #limit: number;
    #timeout: any = null;

    constructor(limit: number = 16, delay: number = 500) {
        if (limit < 1) {
            throw new Error('Limit must be positive');
        }

        this.#limit = limit;
        this.#delay = delay;
    }

    addRequest(url: string, parameters: object = {}): Promise<any>
    {
        return new Promise((resolve, reject) => {
            this.#requests.push({
                url,
                parameters,
                resolve,
                reject
            });
            this.#startRequests();
        });
    }

    #startRequests(): void
    {
        if (this.#requests.length === 0) {
            return;
        }

        if (this.#requests.length < this.#limit) {
            this.clearTimeout();
        }

        if (this.#timeout !== null) {
            return;
        }

        this.#timeout = setTimeout(
            () => {
                Promise.all(
                    this.#requests
                        .splice(0, this.#limit)
                        .map(({url, parameters, resolve, reject}) => {
                            return axios.get(url, {params: parameters}).then(resolve, reject);
                        })
                ).then(() => {
                    this.clearTimeout();
                    this.#startRequests();
                });
            }
            , this.#delay
        );
    }

    clearTimeout(): void
    {
        clearTimeout(this.#timeout);
        this.#timeout = null;
    }
}
