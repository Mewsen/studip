export const QuestionnaireComponent = {
    props: {
        value: Object
    },
    data () {
        return {val_clone: this.value};
    },
    methods: {
        setDefaultValues(value) {
            this.val_clone = Object.assign(value, this.value);
        }
    },
    watch: {
        val_clone: {
            handler(current) {
                this.$emit('input', current);
            },
            deep: true
        },
        value (new_val) {
            this.val_clone = new_val;
        }
    }
};
