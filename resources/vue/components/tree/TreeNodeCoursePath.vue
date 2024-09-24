<template>
    <div>
        <button type="button"
                @click.prevent="togglePathInfo"
                :title="showPaths
                    ? $gettext('Pfad im Verzeichnis ausblenden')
                    : $gettext('Pfad im Verzeichnis anzeigen')">
            <studip-icon shape="info-circle"></studip-icon>
        </button>
        <ul v-if="showPaths" class="studip-tree-course-path">
            <li v-for="(path, pindex) in paths" :key="pindex">
                <button @click.prevent="openNode(path[path.length - 1].id)">
                    <template v-for="(segment) in path">
                        / {{ segment.name }}
                    </template>
                </button>
            </li>
        </ul>
    </div>
</template>
<script>
import axios from 'axios';
import StudipIcon from '../StudipIcon.vue';

export default {
    name: 'TreeNodeCoursePath',
    components: { StudipIcon },
    props: {
        courseId: {
            type: String,
            required: true
        },
        nodeClass: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            paths: [],
            showPaths: false
        }
    },
    methods: {
        openNode(id) {
            STUDIP.eventBus.emit('load-tree-node', this.nodeClass + '_' + id);
        },
        togglePathInfo() {
            this.showPaths = !this.showPaths;
        }
    },
    mounted() {
        axios.get(
            STUDIP.URLHelper.getURL('jsonapi.php/v1/tree-node/course/pathinfo/' + this.nodeClass + '/' + this.courseId)
        ).then(response => {
            this.paths = response.data;
        });
    }
}
</script>
