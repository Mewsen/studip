export const QuestionnaireComponent = {
    emits: ['update:model-value'],
    props: {
        modelValue: Object
    },
    data () {
        return {
            val_clone: {...this.modelValue}
        };
    },
    methods: {
        setDefaultValues(value) {
            this.val_clone = Object.assign(value, this.modelValue);
        }
    },
    watch: {
        val_clone: {
            handler(current) {
                this.$emit('update:model-value', current);
            },
            deep: true
        },
        modelValue(new_val) {
            this.val_clone = new_val;
        }
    }
};
