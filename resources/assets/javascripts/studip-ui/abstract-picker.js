class AbstractPicker
{
    static dataHandlers = {};

    static supportsNativeInput() {
        return true;
    }

    static init() {
        const initIndex = this.name.toLowerCase();

        Array.from(document.querySelectorAll(this.selector)).filter(node => {
            return node.dataset[initIndex] === undefined;
        }).forEach(element => {
            element.dataset[initIndex] = true;

            const picker = new this(element);
        });

    }

    node;

    constructor(node) {
        if (new.target === AbstractPicker) {
            throw new TypeError('Cannot construct an abstract picker');
        }

        if (this.constructor.selector === undefined) {
            throw new Error('No selector getter defined');
        }

        if (this.constructor.datasetIndex === undefined) {
            throw new Error('No datasetIndex getter defined');
        }

        this.node = node;

        this.setup();

        this.refresh();
        node.addEventListener('change', () => this.refresh());
    }

    setup() {
    }

    refresh() {
        const options = this.node.dataset[this.constructor.datasetIndex] !== undefined
            ? JSON.parse(this.node.dataset[this.constructor.datasetIndex])
            : {};

        console.log('in refresh', options);

        Object.entries(options).forEach(([key, value]) => {
            if (this.constructor.dataHandlers[key] !== undefined) {
                console.log('match', key, value);
                this.constructor.dataHandlers[key](this.node, value);
            }
        });
    }
}

export default AbstractPicker;
