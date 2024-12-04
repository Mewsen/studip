<script>
import { h } from 'vue';

let uuid = 0;
export default {
    name: 'studip-proxy-checkbox',
    emits: ['update:selected'],
    props: {
        name: String,
        id: String,
        total: {
            type: Array,
            required: true
        },
       selected: {
            type: Array,
            required: true,
        },
    },
    methods: {
        changeProxy () {
            this.$emit('update:selected', this.checked ? [] : [...this.total] );
        }
    },
    computed: {
        proxyId () {
            return this.id ?? `proxy-checkbox-${uuid++}`;
        },
        checked () {
            return this.selected.length === this.total.length;
        },
        indeterminate () {
            return this.selected.length > 0 && this.selected.length !== this.total.length;
        }
    },
    render () {
        return h('input', {
            type: 'checkbox',
            name: this.name,
            id: this.proxyId,
            checked: this.checked ? true : null,
            indeterminate: this.indeterminate ? true : null,
            onChange: this.changeProxy,
        });
    }
};
</script>
