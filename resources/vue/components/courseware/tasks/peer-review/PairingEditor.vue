<template>
    <div>
        <form class="default">
            <div>
                <label>
                    {{ $gettext('Lösung von') }}
                    <select v-model="selectedSubmitter" size="10">
                        <option v-for="solver in selectableSubmitters" :key="solver.id" :value="solver">
                            <span v-if="isUser(solver)">
                                {{ solver.attributes['formatted-name'] }}
                            </span>
                            <span v-if="isStatusGroup(solver)">
                                {{ solver.attributes.name }}
                            </span>
                        </option>
                        <option v-if="!selectableSubmitters?.length" disabled>{{ $gettext('--leer--') }}</option>
                    </select>
                </label>
            </div>
            <div>
                <label>
                    {{ $gettext('Peer-Review von') }}
                    <select v-model="selectedReviewer" size="10">
                        <option v-for="solver in selectableReviewers" :key="solver.id" :value="solver">
                            <span v-if="isUser(solver)">
                                {{ solver.attributes['formatted-name'] }}
                            </span>
                            <span v-if="isStatusGroup(solver)">
                                {{ solver.attributes.name }}
                            </span>
                        </option>
                        <option v-if="!selectableReviewers?.length" disabled>{{ $gettext('--leer--') }}</option>
                    </select>
                </label>
            </div>
            <div>
                <div>
                    <div>{{ $gettext('Paarungen') }}</div>
                    <div>
                        <button
                            class="button button-icon"
                            type="button"
                            :disabled="!(selectedSubmitter && selectedReviewer)"
                            @click="onAdd"
                        >
                            <StudipIcon shape="arr_2right" role="info_alt" />
                            <StudipIcon shape="arr_2right" />
                            <span class="sr-only">{{ $gettext('Hinzufügen') }}</span>
                        </button>
                        <table>
                            <tr v-for="({ submitter, reviewer }, index) in localPairings" :key="index">
                                <td>
                                    <span v-if="submitter.type === 'users'">
                                        {{ submitter.attributes['formatted-name'] }}
                                    </span>
                                    <span v-if="submitter.type === 'status-groups'">
                                        {{ submitter.attributes.name }}
                                    </span>
                                </td>

                                <td><span>»</span></td>
                                <td>
                                    <span v-if="reviewer.type === 'users'">
                                        {{ reviewer.attributes['formatted-name'] }}
                                    </span>
                                    <span v-if="reviewer.type === 'status-groups'">
                                        {{ reviewer.attributes.name }}
                                    </span>
                                </td>
                                <td>
                                    <button @click="onTrash(index)" class="button button-icon" type="button">
                                        <StudipIcon shape="trash" role="info_alt" />
                                        <StudipIcon shape="trash" />
                                        <span class="sr-only">{{ $gettext('Entfernen') }}</span>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import _ from 'lodash';
import { mapGetters } from 'vuex';
import StudipIcon from '../../../StudipIcon.vue';

export default {
    model: {
        prop: 'pairings',
        event: 'update',
    },
    components: { StudipIcon },
    props: {
        pairings: {
            type: Array,
            required: true,
        },
        solvers: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            localPairings: [],
            selectedSubmitter: null,
            selectedReviewer: null,
        };
    },
    computed: {
        selectableReviewers() {
            const selected = this.localPairings.map(({ reviewer }) => reviewer.id);
            return this.solvers.filter(({ id }) => !selected.includes(id));
        },
        selectableSubmitters() {
            const selected = this.localPairings.map(({ submitter }) => submitter.id);
            return this.solvers.filter(({ id }) => !selected.includes(id));
        },
    },
    methods: {
        isStatusGroup(object) {
            return object.type === 'status-groups';
        },
        isUser(object) {
            return object.type === 'users';
        },
        onAdd() {
            this.localPairings.push({
                reviewer: this.selectedReviewer,
                submitter: this.selectedSubmitter,
            });
            this.selectedReviewer = null;
            this.selectedSubmitter = null;
        },
        onTrash(index) {
            this.localPairings = [...this.localPairings.slice(0, index), ...this.localPairings.slice(index + 1)];
        },
        resetLocalState() {
            this.localPairings = [...this.pairings];
        },
    },
    mounted() {
        this.resetLocalState();
    },
    watch: {
        localPairings(newP, oldP) {
            if (!_.isEqual(this.localPairings, this.pairings)) {
                this.$emit('update', [...this.localPairings]);
            }
        },
        pairings() {
            if (!_.isEqual(this.localPairings, this.pairings)) {
                this.resetLocalState();
            }
        },
        selectedReviewer() {
            if (this.selectedReviewer === this.selectedSubmitter) {
                this.selectedSubmitter = null;
            }
        },
        selectedSubmitter() {
            if (this.selectedReviewer === this.selectedSubmitter) {
                this.selectedReviewer = null;
            }
        },
    },
};
</script>

<style scoped>
form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

form > * {
    flex-grow: 1;
}

form > :nth-child(1) select,
form > :nth-child(2) select {
    max-width: 15rem;
}

form > :nth-child(3) {
    flex-basis: 15rem;
}

tr > :nth-child(2),
tr > :nth-child(4) {
    padding-inline: 0.5rem;
}

button.button-icon {
    min-width: auto;
    line-height: 1.5rem;
    padding: 0;
    width: 1.5rem;
}
button.button-icon > img {
    vertical-align: middle;
}
button.button-icon > img:first-child {
    display: none;
}
button.button-icon:hover > img:first-child {
    display: inline;
}
button.button-icon > img:first-child {
    display: hidden;
}
button.button-icon:hover > img:nth-child(2) {
    display: none;
}
</style>
