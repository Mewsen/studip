<script>
import { h } from 'vue';
import Multiquicksearch from '@/vue/components/Multiquicksearch.vue';

export default {
    props: ['name', 'value', 'searchtype', 'autocomplete', 'addlabel'],
    setup(props) {
        return () => h(Multiquicksearch, props);
    },
};
</script>
