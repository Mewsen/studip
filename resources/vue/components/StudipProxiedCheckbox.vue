<script>
import { h } from 'vue';

let uuid = 0;
export default {
    name: 'studip-proxied-checkbox',
    emits: ['update:selected'],
    props: {
        name: String,
        id: String,
        value: {
            required: true
        },
        selected: {
            type: Array,
            required: true
        }
    },
    methods: {
        changeCollection () {
            const selected = new Set(this.selected);

            if (this.checked) {
                selected.delete(this.value);
            } else {
                selected.add(this.value);
            }

            this.$emit('update:selected', [...selected.values()]);
        }
    },
    computed: {
        proxiedId () {
            return this.id ?? `proxied-checkbox-${uuid++}`;
        },
        checked () {
            return this.selected.includes(this.value);
        },
    },
    render () {
        return h('input', {
            type: 'checkbox',
            name: this.name,
            id: this.proxiedId,
            value: this.value,
            checked: this.checked ? true : null,
            onChange: this.changeCollection,
        });
    }
};
</script>
