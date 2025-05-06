<script>
import { h } from 'vue';
import ContentBar from '@/vue/components/ContentBar.vue';
import ContentBarBreadcrumbs from '@/vue/components/ContentBarBreadcrumbs.vue';

export default {
    props: ['icon', 'isContentBar', 'toc'],
    setup(props, context) {
        return () =>
            h(
                ContentBar,
                {
                    icon: props.icon,
                    isContentBar: props.isContentBar,
                    toc: props.toc,
                },
                {
                    'breadcrumb-list': () => h(ContentBarBreadcrumbs, { toc: props.toc }),
                    ...context.slots,
                },
            );
    },
};
</script>
