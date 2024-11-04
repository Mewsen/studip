<template>
    <focus-trap v-model:active="consumModeTrap">
        <div>
            <div
                v-if="validContext"
                :class="{ 'cw-structural-element-consumemode': consumeMode }"
                class="cw-structural-element"
            >
                <div v-if="structuralElement" class="cw-structural-element-content">
                    <courseware-ribbon
                        @blockAdded="updateContainerList">
                        <template #buttons-left>
                            <router-link v-if="prevElement" :to="'/structural_element/' + prevElement.id">
                                <div class="cw-ribbon-button cw-ribbon-button-prev" :title="$gettext('zurück')" />
                            </router-link>
                            <div
                                v-else
                                class="cw-ribbon-button cw-ribbon-button-prev disabled"
                                :title="$gettext('Keine vorherige Seite')"
                            />
                            <router-link v-if="nextElement" :to="'/structural_element/' + nextElement.id">
                                <div class="cw-ribbon-button cw-ribbon-button-next" :title="$gettext('weiter')" />
                            </router-link>
                            <div
                                v-else
                                class="cw-ribbon-button cw-ribbon-button-next disabled"
                                :title="$gettext('Keine nächste Seite')"
                            />
                        </template>
                        <template #breadcrumb-list>
                            <ul>
                                <li
                                    v-for="ancestor in ancestors"
                                    :key="ancestor.id"
                                    :title="ancestor.attributes.title"
                                    class="cw-ribbon-breadcrumb-item"
                                >
                                    <span>
                                        <router-link :to="'/structural_element/' + ancestor.id">{{ ancestor.attributes.title || '–' }}</router-link>
                                    </span>
                                </li>
                                <li
                                    class="cw-ribbon-breadcrumb-item cw-ribbon-breadcrumb-item-current"
                                    :title="structuralElement.attributes.title"
                                >
                                    <span>{{ structuralElement.attributes.title || '–' }}</span>
                                    <span v-if="isTask">
                                        [ {{ (!userIsSolver && userIsReviewer && isPeerReviewAnonymous) ? $gettext('anonym') : solverName }} ]
                                    </span>
                                    <template v-if="inCourse && !(userIsTeacher || userIsReviewer)">
                                        <studip-icon
                                            v-if="complete"
                                            shape="accept"
                                            role="info"
                                            :inline="true"
                                            :title="$gettext('Diese Seite wurde von Ihnen vollständig bearbeitet')"
                                        />
                                        <span
                                            v-else
                                            :title="$gettext(
                                                'Fortschritt: %{progress}%',
                                                {progress: elementProgress}
                                            )"
                                        >
                                            ({{ elementProgress }} %)
                                        </span>
                                    </template>
                                    <studip-five-stars
                                        v-if="showFeedbackInContentbar && hasFeedbackElement"
                                        :amount="hasFeedbackAverage ? feedbackAverage : 5"
                                        :size="16"
                                        :role="hasFeedbackAverage ? 'status-yellow' : 'inactive'"
                                        :title="
                                            hasFeedbackAverage
                                                ? $gettext(
                                                    'Seite wurde mit %{avg} Sternen bewertet',
                                                    { avg: feedbackAverage }
                                                  )
                                                : $gettext('Seite wurde noch nicht bewertet')
                                        "
                                        @click="menuAction('showFeedback')"
                                    />
                                </li>
                            </ul>
                        </template>
                        <template #breadcrumb-fallback>
                            <ul>
                                <li
                                    class="cw-ribbon-breadcrumb-item cw-ribbon-breadcrumb-item-current"
                                    :title="structuralElement.attributes.title"
                                >
                                    <span>{{ structuralElement.attributes.title }}</span>
                                </li>
                            </ul>
                        </template>
                        <template #menu>
                            <studip-action-menu
                                v-if="!consumeMode"
                                :items="menuItems"
                                class="cw-ribbon-action-menu"
                                :context="structuralElement.attributes.title"
                                :collapseAt="1"
                                @editCurrentElement="menuAction('editCurrentElement')"
                                @addElement="menuAction('addElement')"
                                @exportElement="menuAction('exportElement')"
                                @deleteCurrentElement="menuAction('deleteCurrentElement')"
                                @showInfo="menuAction('showInfo')"
                                @setBookmark="menuAction('setBookmark')"
                                @showSuggest="menuAction('showSuggest')"
                                @linkElement="menuAction('linkElement')"
                                @removeLock="menuAction('removeLock')"
                                @activateFullscreen="menuAction('activateFullscreen')"
                                @activateComments="menuAction('activateComments')"
                                @deactivateComments="menuAction('deactivateComments')"
                                @showFeedback="menuAction('showFeedback')"
                                @showFeedbackCreate="menuAction('showFeedbackCreate')"
                                @showNote="menuAction('showNote')"
                                @showPermissions="menuAction('showPermissions')"
                            />
                        </template>
                    </courseware-ribbon>
                    <div class="cw-page-wrapper">
                        <div class="cw-page-content">
                            <courseware-call-to-action-box
                                v-if="canEdit && (hasFeedback || displayFeedback)"
                                class="cw-structural-element-feedback-wrapper"
                                iconShape="exclaim-circle"
                                :actionTitle="callToActionTitleFeedback"
                                :titleClosed="$gettext('Anmerkungen anzeigen')"
                                :titleOpen="$gettext('Anmerkungen ausblenden')"
                                :foldable="true"
                            >
                                <template #content>
                                    <courseware-structural-element-feedback
                                        :structuralElement="structuralElement"
                                        :canEdit="canEdit"
                                    />
                                </template>
                            </courseware-call-to-action-box>
                            <div v-if="structuralElementLoaded && !isLink" class="cw-companion-box-wrapper">
                                <StudipMessageBox v-if="userIsReviewer">
                                    {{ $gettext('Diese Seite gehört zu einer Aufgabe, die von einer anderen Person bearbeitet wird.') }}
                                </StudipMessageBox>
                                <courseware-companion-box
                                    v-if="canNotShow"
                                    :mood="canNotShow.mood"
                                    :msgCompanion="canNotShow.msg"
                                />
                                <courseware-companion-box
                                    v-if="blockedByAnotherUser"
                                    :msgCompanion="
                                        $gettext(
                                            'Die Einstellungen dieser Seite werden im Moment von %{blockingUserName} bearbeitet.',
                                            { blockingUserName: blockingUserName }
                                        )
                                    "
                                    mood="pointing"
                                >
                                    <template #companionActions>
                                        <button v-if="userIsTeacher" class="button" @click="menuAction('removeLock')">
                                            {{ textRemoveLock.title }}
                                        </button>
                                    </template>
                                </courseware-companion-box>
                                <courseware-companion-box
                                    v-for="peerReview in peerReviews"
                                    :key="peerReview.id"
                                    mood="pointing"
                                    :msgCompanion="peerReviewCompanionMessage(peerReview)"
                                    >
                                    <template #companionActions>
                                        <button
                                            class="button"
                                            @click="openPeerReview(peerReview)"
                                            :disabled="!canReadPeerReviewAssessment(peerReview)"
                                            >
                                            {{ peerReviewCompanionAction(peerReview) }}
                                        </button>
                                    </template>
                                </courseware-companion-box>
                                <courseware-empty-element-box
                                    v-if="empty && !showRootLayout"
                                    :canEdit="canEdit"
                                    :noContainers="noContainers"
                                />
                            </div>

                            <courseware-root-content
                                v-if="showRootLayout"
                                :structuralElement="currentElement"
                                :canEdit="canEdit"
                            />

                            <div
                                v-if="canVisit && (!canEdit || hideEditLayout ) && !isLink && !hideRootContent"
                                class="cw-container-wrapper"
                                :class="{
                                    'cw-container-wrapper-consume': consumeMode,
                                }"
                            >
                                <component
                                    v-for="container in containers"
                                    :key="container.id"
                                    :is="containerComponent(container)"
                                    :container="container"
                                    :canEdit="canEdit && !hideEditLayout"
                                    :canAddElements="canAddElements"
                                    :isTeacher="userIsTeacher"
                                    class="cw-container-item"
                                    @containerReady="onContainerReady"
                                />
                            </div>

                            <div
                                v-if="isLink"
                                class="cw-container-wrapper"
                                :class="{
                                    'cw-container-wrapper-consume': consumeMode,
                                }"
                            >
                                <div v-if="canEdit" class="cw-companion-box-wrapper">
                                    <courseware-companion-box
                                        :msgCompanion="
                                            $gettext(
                                                'Dieser Inhalt ist aus den persönlichen Lernmaterialien von %{ ownerName } verlinkt und kann nur dort bearbeitet werden.',
                                                { ownerName: ownerName }
                                            )
                                        "
                                        mood="pointing"
                                    />
                                </div>
                                <component
                                    v-for="container in linkedContainers"
                                    :key="container.id"
                                    :is="containerComponent(container)"
                                    :container="container"
                                    :canEdit="false"
                                    :canAddElements="false"
                                    :isTeacher="userIsTeacher"
                                    class="cw-container-item"
                                />
                            </div>
                            <div v-if="canVisit && canEdit && !hideEditLayout && !isLink && !hideRootContent" class="cw-container-wrapper cw-container-wrapper-edit">
                                <template v-if="!processing">
                                    <span aria-live="assertive" class="assistive-text">{{ assistiveLive }}</span>
                                    <span id="operation" class="assistive-text">
                                        {{ $gettext('Drücken Sie die Leertaste, um neu anzuordnen.') }}
                                    </span>
                                    <draggable
                                        v-bind="dragOptions"
                                        class="cw-structural-element-list"
                                        tag="ol"
                                        role="listbox"
                                        v-model="containerList"
                                        handle=".cw-sortable-handle"
                                        @start="isDragging = true"
                                        @end="dropContainer"
                                        item-key="id"
                                    >
                                        <template #item="{element}">
                                            <li class="cw-container-item-sortable">
                                                <span
                                                    :class="{ 'cw-sortable-handle-dragging': isDragging }"
                                                    class="cw-sortable-handle"
                                                    tabindex="0"
                                                    role="option"
                                                    aria-describedby="operation"
                                                    :ref="'sortableHandle' + element.id"
                                                    @keydown="keyHandler($event, element.id)"
                                                ></span>
                                                <component
                                                    :is="containerComponent(element)"
                                                    :container="element"
                                                    :canEdit="canEdit"
                                                    :canAddElements="canAddElements"
                                                    :isTeacher="userIsTeacher"
                                                    class="cw-container-item"
                                                    ref="containers"
                                                    :class="{
                                                        'cw-container-item-selected': keyboardSelected === element.id,
                                                    }"
                                                    @containerReady="onContainerReady"
                                                />
                                            </li>
                                        </template>
                                    </draggable>
                                </template>
                                <studip-progress-indicator
                                    v-if="processing"
                                    :description="$gettext('Vorgang wird bearbeitet...')"
                                />
                            </div>
                        </div>
                        <courseware-toolbar v-if="canVisit && canEdit && !isLink" />
                    </div>
                    <courseware-call-to-action-box
                        v-if="commentable"
                        class="cw-structural-element-comments-wrapper"
                        iconShape="chat"
                        :actionTitle="callToActionTitleComments"
                        :titleClosed="$gettext('Kommentare anzeigen')"
                        :titleOpen="$gettext('Kommentare ausblenden')"
                        :foldable="true"
                        :open="false"
                    >
                        <template #content>
                            <courseware-structural-element-comments :structuralElement="structuralElement" />
                        </template>
                    </courseware-call-to-action-box>
                </div>
                <courseware-structural-element-dialog-add
                    v-if="showAddDialog"
                    :structuralElement="structuralElement"
                    :isRoot="isRoot"
                    :canEditParent="canEditParent"
                />
                <studip-dialog
                    v-if="showDeleteDialog"
                    :title="textDelete.title"
                    :question="textDelete.alert"
                    height="200"
                    @confirm="deleteCurrentElement"
                    @close="closeDeleteDialog"
                ></studip-dialog>

                <studip-dialog
                    v-if="showRemoveLockDialog"
                    :title="textRemoveLock.title"
                    :question="textRemoveLock.alert"
                    height="200"
                    width="450"
                    @confirm="executeRemoveLock"
                    @close="showElementRemoveLockDialog(false)"
                ></studip-dialog>
                <courseware-structural-element-dialog-settings
                    v-if="showEditDialog"
                    :structuralElement="currentElement"
                    @close="closeEditDialog"
                    @store="selectCurrent"
                />
                <template v-if="showPermissionsDialog && !isTask && !inContent">
                    <studip-dialog
                        v-if="showPermissionScopeDialog"
                        :title="$gettext('Rechte und Sichtbarkeit')"
                        :confirm-text="$gettext('Wechseln')"
                        confirm-class="accept"
                        :close-text="$gettext('Abbrechen')"
                        close-class="cancel"
                        :question="$gettext('Sie haben bereits die Rechte und Sichtbarkeit für das gesamte Lernmaterial eingestellt. Möchten Sie nun die Rechte für einzelne Seiten anpassen? Die bereits festgelegten Rechte werden beibehalten.')"
                        height="250"
                        @close="closeEditDialog"
                        @confirm="switchPermissionScope"
                    >

                    </studip-dialog>
                    <courseware-structural-element-dialog-permissions
                        v-if="showPermissionSettingsDialog"
                        :structuralElement="currentElement"
                        @close="closeEditDialog"
                        @store="selectCurrent"
                    />
                </template>
                <courseware-structural-element-dialog-import v-if="showImportDialog" />
                <courseware-structural-element-dialog-copy v-if="showCopyDialog" />
                <courseware-structural-element-dialog-link v-if="showLinkDialog" />
                <courseware-structural-element-dialog-export-chooser
                    v-if="showExportChooserDialog"
                    :canEdit="canEdit"
                    :canVisit="canVisit"
                />
                <courseware-structural-element-dialog-export
                    v-if="showExportDialog"
                    :structuralElement="currentElement"
                />
                <courseware-structural-element-dialog-export-pdf
                    v-if="showPdfExportDialog"
                    :structuralElement="currentElement"
                />
                <courseware-structural-element-dialog-export-oer
                    v-if="showOerExportDialog"
                    :structuralElement="currentElement"
                />
                <courseware-structural-element-dialog-oer-suggest
                    v-if="showSuggestOerDialog"
                    :structuralElement="structuralElement"
                    :ownerName="ownerName"
                />
                <courseware-structural-element-dialog-add-chooser v-if="showAddChooserDialog" />
                <courseware-structural-element-dialog-info
                    v-if="showInfoDialog"
                    :structuralElement="currentElement"
                    :ownerName="ownerName"
                />
                <courseware-structural-element-dialog-public-link
                    v-if="showPublicLinkDialog && inContent"
                    :structuralElement="structuralElement"
                />
                <PeerReviewAssessmentDialog
                    v-model:show="showPeerReviewAssessment"
                    v-if="selectedPeerReview"
                    :review="selectedPeerReview"
                    />
                <PeerReviewResultDialog
                    v-model:show="showPeerReviewResult"
                    v-if="selectedPeerReview"
                    :review="selectedPeerReview"
                    />
                <feedback-dialog
                    v-if="showFeedbackDialog"
                    :feedbackElementId="parseInt(feedbackElementId)"
                    :currentUser="currentUser"
                    @deleted="loadStructuralElement(currentId)"
                    @close="showStructuralElementFeedbackDialog(false)"
                />
                <feedback-create-dialog
                    v-if="showFeedbackCreateDialog"
                    :defaultQuestion="$gettext('Bewerten Sie die Seite')"
                    rangeType="courseware-structural-elements"
                    :rangeId="currentElement.id"
                    @created="loadStructuralElement(currentElement.id)"
                    @close="showStructuralElementFeedbackCreateDialog(false)"
                />
                <courseware-feedback-popup
                    v-if="showRatingPopup"
                    :feedbackElement="ratingPopupFeedbackElement"
                    @close="showRatingPopup = false"
                    @submit="submitFeedback"
                />
            </div>
            <div v-else>
                <courseware-companion-box
                    v-if="currentElement !== ''"
                    :msgCompanion="$gettext('Die angeforderte Seite ist nicht Teil dieser Courseware.')"
                    mood="sad"
                >
                    <template v-slot:companionActions>
                        <a class="button" :href="unitRootUrl">{{ $gettext('Lernmaterial neu laden') }}</a>
                        <a class="button" :href="shelfURL">{{ $gettext('Zurück zur Lernmaterialübersicht') }}</a>
                    </template>
                </courseware-companion-box>
            </div>
        </div>
    </focus-trap>
</template>

<script>
import ContainerComponents from '../containers/container-components.js';
import StructuralElementComponents from './structural-element-components.js';
import CoursewarePluginComponents from '../plugin-components.js';
import CoursewareRootContent from './CoursewareRootContent.vue';

import CoursewareStructuralElementComments from './CoursewareStructuralElementComments.vue';
import CoursewareStructuralElementFeedback from './CoursewareStructuralElementFeedback.vue';
import CoursewareFeedbackPopup from './CoursewareFeedbackPopup.vue';
import CoursewareStructuralElementDialogAdd from './CoursewareStructuralElementDialogAdd.vue';
import CoursewareStructuralElementDialogAddChooser from './CoursewareStructuralElementDialogAddChooser.vue';
import CoursewareStructuralElementDialogCopy from './CoursewareStructuralElementDialogCopy.vue';
import CoursewareStructuralElementDialogImport from './CoursewareStructuralElementDialogImport.vue';
import CoursewareStructuralElementDialogLink from './CoursewareStructuralElementDialogLink.vue';
import CoursewareStructuralElementDialogExportChooser from './CoursewareStructuralElementDialogExportChooser.vue';
import CoursewareStructuralElementDialogExport from './CoursewareStructuralElementDialogExport.vue';
import CoursewareStructuralElementDialogExportOer from './CoursewareStructuralElementDialogExportOer.vue';
import CoursewareStructuralElementDialogExportPdf from './CoursewareStructuralElementDialogExportPdf.vue';
import CoursewareStructuralElementDialogOerSuggest from './CoursewareStructuralElementDialogOerSuggest.vue';
import CoursewareStructuralElementDialogSettings from './CoursewareStructuralElementDialogSettings.vue';
import CoursewareStructuralElementDialogPermissions from './CoursewareStructuralElementDialogPermissions.vue';
import CoursewareStructuralElementDialogInfo from './CoursewareStructuralElementDialogInfo.vue';
import CoursewareStructuralElementDialogPublicLink from './CoursewareStructuralElementDialogPublicLink.vue';
import CoursewareStructuralElementDiscussion from './CoursewareStructuralElementDiscussion.vue';

import CoursewareWelcomeScreen from './CoursewareWelcomeScreen.vue';
import CoursewareRibbon from "./CoursewareRibbon.vue";
import PeerReviewAssessmentDialog from '../tasks/peer-review/AssessmentDialog.vue';
import PeerReviewResultDialog from '../tasks/peer-review/ResultDialog.vue';
import { getProcessStatus, ProcessStatus } from '../tasks/peer-review/definitions.ts';
import CoursewareExport from '@/vue/mixins/courseware/export.js';

import colorMixin from '@/vue/mixins/courseware/colors.js';
import wizardMixin from '@/vue/mixins/courseware/wizard.js';
import CoursewareCallToActionBox from '../layouts/CoursewareCallToActionBox.vue';
import CoursewareDateInput from '../layouts/CoursewareDateInput.vue';
import StudipDialog from '../../StudipDialog.vue';
import { FocusTrap } from 'focus-trap-vue';
import FeedbackDialog from '../../feedback/FeedbackDialog.vue';
import FeedbackCreateDialog from '../../feedback/FeedbackCreateDialog.vue';
import StudipFiveStars from '../../feedback/StudipFiveStars.vue';
import StudipMessageBox from '../../StudipMessageBox.vue';
import StudipProgressIndicator from '../../StudipProgressIndicator.vue';
import draggable from 'vuedraggable';
import containerMixin from '@/vue/mixins/courseware/container.js';
import { mapActions, mapGetters } from 'vuex';
import { store } from "../../../../assets/javascripts/chunks/vue";

export default {
    name: 'courseware-structural-element',
    components: Object.assign(StructuralElementComponents, {
        CoursewareRootContent,
        CoursewareStructuralElementComments,
        CoursewareStructuralElementFeedback,
        CoursewareStructuralElementDialogAdd,
        CoursewareStructuralElementDialogAddChooser,
        CoursewareStructuralElementDialogCopy,
        CoursewareStructuralElementDialogImport,
        CoursewareStructuralElementDialogLink,
        CoursewareStructuralElementDialogExport,
        CoursewareStructuralElementDialogExportChooser,
        CoursewareStructuralElementDialogExportOer,
        CoursewareStructuralElementDialogExportPdf,
        CoursewareStructuralElementDialogOerSuggest,
        CoursewareStructuralElementDialogSettings,
        CoursewareStructuralElementDialogPermissions,
        CoursewareStructuralElementDialogInfo,
        CoursewareStructuralElementDialogPublicLink,
        CoursewareStructuralElementDiscussion,
        CoursewareWelcomeScreen,
        CoursewareCallToActionBox,
        CoursewareDateInput,
        CoursewareFeedbackPopup,
        FeedbackDialog,
        FeedbackCreateDialog,
        StudipFiveStars,
        FocusTrap,
        PeerReviewAssessmentDialog,
        PeerReviewResultDialog,
        StudipDialog,
        StudipMessageBox,
        StudipProgressIndicator,
        draggable,
        CoursewareRibbon,
    }),
    props: ['canVisit', 'orderedStructuralElements', 'structuralElement'],

    mixins: [CoursewareExport, colorMixin, wizardMixin, containerMixin],

    emits: ['select'],

    data() {
        return {
            currentElement: '',
            textRemoveLock: {
                title: this.$gettext('Sperre aufheben'),
                alert: this.$gettext('Möchten Sie die Sperre der Seite wirklich aufheben?'),
            },
            containerList: [],
            isDragging: false,
            dragOptions: {
                animation: 0,
                group: 'description',
                disabled: false,
                ghostClass: 'container-ghost',
            },
            errorEmptyChapterName: false,
            consumModeTrap: false,
            keyboardSelected: null,
            assistiveLive: '',
            showPeerReviewAssessment: false,
            showPeerReviewResult: false,
            selectedPeerReview: null,
            displayFeedback: false,
            showRatingPopup: false,
            ratingPopupFeedbackElement: null,
            storing: false,

            handleDebouncedScroll: null,
            scrollHasBeenPerformed: false,

            showPermissionScopeDialog: false,
            showPermissionSettingsDialog: false,

            containerStatus: {},
        };
    },

    computed: {
        consumeMode() {
            return store.state.studip.consumeMode;
        },
        ...mapGetters({
            courseware: 'courseware',
            rootId: 'rootId',
            currentUnit: 'currentUnit',
            context: 'context',
            containerById: 'courseware-containers/byId',
            relatedContainers: 'courseware-containers/related',
            relatedPeerReviewProcesses: 'courseware-peer-review-processes/related',
            relatedPeerReviews: 'courseware-peer-reviews/related',
            relatedStructuralElements: 'courseware-structural-elements/related',
            getRelatedFeedback: 'courseware-structural-element-feedback/related',
            getRelatedComments: 'courseware-structural-element-comments/related',
            relatedTaskGroups: 'courseware-task-groups/related',
            relatedUsers: 'users/related',
            structuralElementById: 'courseware-structural-elements/byId',
            userIsTeacher: 'userIsTeacher',
            pluginManager: 'pluginManager',
            showEditDialog: 'showStructuralElementEditDialog',
            showAddDialog: 'showStructuralElementAddDialog',
            showAddChooserDialog: 'showStructuralElementAddChooserDialog',
            showImportDialog: 'showStructuralElementImportDialog',
            showCopyDialog: 'showStructuralElementCopyDialog',
            showLinkDialog: 'showStructuralElementLinkDialog',
            showExportDialog: 'showStructuralElementExportDialog',
            showExportChooserDialog: 'showStructuralElementExportChooserDialog',
            showPdfExportDialog: 'showStructuralElementPdfExportDialog',
            showInfoDialog: 'showStructuralElementInfoDialog',
            showDeleteDialog: 'showStructuralElementDeleteDialog',
            showOerExportDialog: 'showStructuralElementOerDialog',
            showSuggestOerDialog: 'showSuggestOerDialog',
            showPublicLinkDialog: 'showStructuralElementPublicLinkDialog',
            showRemoveLockDialog: 'showStructuralElementRemoveLockDialog',
            showFeedbackDialog: 'showStructuralElementFeedbackDialog',
            showFeedbackCreateDialog: 'showStructuralElementFeedbackCreateDialog',
            showPermissionsDialog: 'showStructuralElementPermissionsDialog',
            oerCampusEnabled: 'oerCampusEnabled',
            oerEnableSuggestions: 'oerEnableSuggestions',
            licenses: 'licenses',
            userId: 'userId',
            taskById: 'courseware-tasks/byId',
            userById: 'users/byId',
            lastCreatedElement: 'courseware-structural-elements/lastCreated',
            groupById: 'status-groups/byId',

            blocked: 'currentElementBlocked',
            blockerId: 'currentElementBlockerId',
            blockedByThisUser: 'currentElementBlockedByThisUser',
            blockedByAnotherUser: 'currentElementBlockedByAnotherUser',
            isLink: 'currentElementisLink',

            templates: 'courseware-templates/all',
            progressData: 'progresses',

            showRootElement: 'showRootElement',
            childrenById: 'courseware-structure/children',

            rootLayout: 'rootLayout',
            hideEditLayout: 'hideEditLayout',
            isFeedbackActivated: 'isFeedbackActivated',
            canCreateFeedbackElement: 'canCreateFeedbackElement',
            getFeedbackElementById: 'feedback-elements/byId',
            feedbackEntries: 'feedback-entries/all',

            currentUser: 'currentUser',
            processing: 'processing',
        }),

        currentId() {
            return this.structuralElement?.id;
        },
        countSiblings() {
            if (this.parent) {
                return this.childrenById(this.parent.id).length;
            }

            return 0;
        },

        inCourse() {
            return this.context.type === 'courses';
        },

        inContent() {
            // The rights tab in contents will be only visible to the owner.
            return this.context.type === 'users' && this.userId === this.currentElement.relationships.user.data.id;
        },

        textDelete() {
            let textDelete = {};
            textDelete.title = this.$gettext('Seite unwiderruflich löschen');
            textDelete.alert = this.$gettext('Möchten Sie die Seite wirklich löschen?');
            if (this.structuralElementLoaded) {
                textDelete.alert = this.$gettext(
                    'Möchten Sie die Seite %{ pageTitle } und alle ihre Unterseiten wirklich löschen?',
                    { pageTitle: this.structuralElement.attributes.title }
                );
            }

            return textDelete;
        },

        validContext() {
            if (this.context.type === 'sharedusers') {
                if (this.context.id === this.courseware.relationships.root.data.id) {
                    return true;
                }
            }

            if (this.context.type === 'public') {
                return true;
            }

            if (this.context.unit !== this.currentElement.relationships?.unit?.data?.id) {
                return false;
            }

            if (this.context.type === 'courses' && this.currentElement.relationships) {
                if (
                    this.currentElement.relationships.course &&
                        this.context.id === this.currentElement.relationships.course.data.id
                ) {
                    return true;
                }
            }

            if (this.context.type === 'users' && this.currentElement.relationships) {
                if (
                    this.currentElement.relationships.user &&
                        this.context.id === this.currentElement.relationships.user.data.id
                ) {
                    return true;
                }
            }

            return false;
        },

        structuralElementLoaded() {
            return this.structuralElement !== null;
        },

        ancestors() {
            if (!this.structuralElement) {
                return [];
            }

            const finder = (parent) => {
                const parentId = parent.relationships?.parent?.data?.id;
                if (!parentId) {
                    return null;
                }
                const element = this.structuralElementById({ id: parentId });
                if (!element) {
                    console.error(`CoursewareStructuralElement#ancestors: Could not find parent by ID: "${parentId}".`);
                    return null;
                }
                if (element.relationships.parent.data === null && !this.showRootElement) {
                    return null;
                }

                return element;
            };

            const visitAncestors = function* (node) {
                const parent = finder(node);
                if (parent) {
                    yield parent;
                    yield* visitAncestors(parent);
                }
            };

            return [...visitAncestors(this.structuralElement)].reverse();
        },
        prevElement() {
            const currentIndex = this.orderedStructuralElements.indexOf(this.structuralElement.id);
            if (currentIndex <= 0) {
                return null;
            }
            const previousId = this.orderedStructuralElements[currentIndex - 1];
            const previous = this.structuralElementById({ id: previousId });

            if (previous.relationships.parent.data === null && !this.showRootElement) {
                return null;
            }

            return previous;
        },
        nextElement() {
            const currentIndex = this.orderedStructuralElements.indexOf(this.structuralElement.id);
            const lastIndex = this.orderedStructuralElements.length - 1;
            if (currentIndex === -1 || currentIndex === lastIndex) {
                return null;
            }
            const nextId = this.orderedStructuralElements[currentIndex + 1];
            const next = this.structuralElementById({ id: nextId });

            return next;
        },
        empty() {
            if (this.containers === null) {
                return true;
            } else {
                return !this.containers.some((container) => container.relationships.blocks.data.length > 0);
            }
        },
        containers() {
            if (!this.structuralElement) {
                return [];
            }

            return (
                this.relatedContainers({
                    parent: this.structuralElement,
                    relationship: 'containers',
                }) ?? []
            );
        },
        noContainers() {
            if (this.containers === null) {
                return true;
            } else {
                return this.containers.length === 0;
            }
        },

        canEdit() {
            if (!this.structuralElement) {
                return false;
            }
            return this.structuralElement.attributes['can-edit'];
        },

        parent() {
            const parentId = this.structuralElement?.relationships?.parent?.data?.id;
            if (!parentId) {
                return null;
            }

            return this.structuralElementById({ id: parentId });
        },

        canEditParent() {
            if (this.isRoot) {
                return false;
            }
            if (!parent) {
                return false;
            }

            return this.parent.attributes['can-edit'];
        },

        isRoot() {
            return this.structuralElement.relationships.parent.data === null;
        },
        showRootLayout() {
            return this.isRoot && this.rootLayout !== 'classic';
        },
        hideRootContent() {
            return this.isRoot && this.rootLayout === 'none';
        },
        deletable() {
            if (this.isRoot) {
                return false;
            }

            if (!this.showRootElement && this.countSiblings <= 1) {
                return false;
            }

            return true;
        },

        feedbackElementId() {
            return this.currentElement?.relationships?.['feedback-element']?.data?.id;
        },
        hasFeedbackElement() {
            return this.feedbackElementId !== undefined;
        },
        showFeedbackInContentbar() {
            return this.courseware.attributes['show-feedback-in-contentbar'];
        },
        feedbackElement() {
            return this.getFeedbackElementById({ id: this.feedbackElementId });
        },
        feedbackAverage() {
            return this.feedbackElement?.attributes?.['average-rating'] ?? 0;
        },
        hasFeedbackAverage() {
            return this.feedbackAverage > 0;
        },

        menuItems() {
            let menu = [];

            if (this.canEdit) {
                menu.push({ id: 1, label: this.$gettext('Seite hinzufügen'), icon: 'add', emit: 'addElement' });
                menu.push({ id: 2, label: this.$gettext('Seite exportieren'), icon: 'export', emit: 'exportElement' });
                menu.push({ id: 3, type: 'separator'});

                if (this.blockedByAnotherUser && this.userIsTeacher) {
                    menu.push({
                        id: 4,
                        label: this.textRemoveLock.title,
                        icon: 'lock-unlocked',
                        emit: 'removeLock',
                    });
                }
                if (!this.blockedByAnotherUser) {
                    menu.push({
                        id: 5,
                        label: this.$gettext('Seiteneinstellungen'),
                        icon: 'settings',
                        emit: 'editCurrentElement',
                    });
                    if (this.userIsTeacher) {
                        if (!this.isTask && !this.inContent && !this.isRoot) {
                            menu.push({
                                id: 6,
                                label: this.$gettext('Rechte und Sichtbarkeit'),
                                icon: 'lock-unlocked',
                                emit: 'showPermissions'
                            });
                        }
                        menu.push({ id: 8, type: 'separator'});
                        menu.push({
                            id: 9,
                            label: this.commentable
                                ? this.$gettext('Kommentare abschalten')
                                : this.$gettext('Kommentare aktivieren'),
                            icon: 'comment2',
                            emit: this.commentable ? 'deactivateComments' : 'activateComments',
                        });
                        if (!this.hasFeedback && !this.displayFeedback) {
                            menu.push({
                                id: 10,
                                label: this.$gettext('Anmerkungen aktivieren'),
                                icon: 'exclaim-circle',
                                emit: 'showNote'
                            });
                        }
                    }
                    menu.push({ id: 12, type: 'separator'});
                }

                if (this.deletable && this.canEdit && !this.isTask && !this.blocked) {
                    menu.push({
                        id: 7,
                        label: this.$gettext('Seite löschen'),
                        icon: 'trash',
                        emit: 'deleteCurrentElement',
                    });
                }
            }
            if (this.isFeedbackActivated) {
                if (this.canCreateFeedbackElement && !this.hasFeedbackElement) {
                    menu.push({
                        id: 11,
                        label: this.$gettext('Feedback aktivieren'),
                        icon: 'feedback',
                        emit: 'showFeedbackCreate',
                    });
                }
                if (this.hasFeedbackElement) {
                    menu.push({
                        id: 11,
                        label: this.$gettext('Feedback anzeigen'),
                        icon: 'feedback',
                        emit: 'showFeedback',
                    });
                }
            }
            menu.push({ id: 13, label: this.$gettext('Lesezeichen setzen'), icon: 'star', emit: 'setBookmark' });

            if (this.oerEnableSuggestions && this.inCourse && this.userId !== this.structuralElement.relationships.owner.data.id) {
                menu.push(
                    { id: 14, label: this.$gettext('Seite für OER Campus vorschlagen'), icon: 'oer-campus',
                        emit: 'showSuggest' }
                );
            }

            if (this.context.type === 'users') {
                menu.push({
                    id: 15,
                    label: this.$gettext('Öffentlichen Link erzeugen'),
                    icon: 'group',
                    emit: 'linkElement',
                });
            }

            if (!document.documentElement.classList.contains('responsive-display')) {
                menu.push({ id: 16, type: 'separator'});
                menu.push(
                    { id: 17, label: this.$gettext('Als Vollbild anzeigen'), icon: 'screen-full',
                        emit: 'activateFullscreen'},
                );
            }

            menu.sort((a, b) => a.id - b.id);

            return menu;
        },
        colors() {
            return this.mixinColors.filter((color) => color.darkmode);
        },

        blockingUser() {
            if (this.blockedByAnotherUser) {
                return this.userById({ id: this.blockerId });
            }

            return null;
        },
        blockingUserName() {
            return this.blockingUser ? this.blockingUser.attributes['formatted-name'] : '';
        },
        pdfExportURL() {
            if (this.context.type === 'users') {
                return STUDIP.URLHelper.getURL(
                    'dispatch.php/contents/courseware/pdf_export/' + this.structuralElement.id
                );
            }
            if (this.context.type === 'courses') {
                return STUDIP.URLHelper.getURL(
                    'dispatch.php/course/courseware/pdf_export/' + this.structuralElement.id
                );
            }

            return '';
        },
        isTask() {
            return this.structuralElement?.relationships.task.data !== null;
        },
        task() {
            if (!this.isTask) {
                return null;
            }

            return this.taskById({ id: this.structuralElement.relationships.task.data.id });
        },
        solver() {
            if (this.task) {
                const solver = this.task.relationships.solver.data;
                if (solver?.type === 'users') {
                    return this.userById({ id: solver.id });
                }
                if (solver?.type === 'status-groups') {
                    return this.groupById({ id: solver.id });
                }
            }

            return null;
        },
        solverName() {
            if (this.solver) {
                if (this.solver.type === 'users') {
                    return this.solver.attributes['formatted-name'];
                }
                if (this.solver.type === 'status-groups') {
                    return this.solver.attributes.name;
                }
            }
            return null;
        },
        canAddElements() {
            if (!this.isTask) {
                return true;
            }

            // still loading
            if (!this.task) {
                return false;
            }

            const taskGroup = this.relatedTaskGroups({ parent: this.task, relationship: 'task-group' });

            return taskGroup?.attributes['solver-may-add-blocks'];
        },

        linkedElement() {
            if (this.isLink) {
                return this.structuralElementById({ id: this.structuralElement.attributes['target-id'] });
            }

            return null;
        },

        linkedContainers() {
            let containers = [];
            let relatedContainers = this.linkedElement?.relationships?.containers?.data;

            if (relatedContainers) {
                for (const container of relatedContainers) {
                    containers.push(this.containerById({ id: container.id }));
                }
            }

            return containers;
        },
        owner() {
            const owner = this.relatedUsers({
                parent: this.structuralElement,
                relationship: 'owner',
            });
            return owner ?? null;
        },

        ownerName() {
            return this.owner?.attributes['formatted-name'] ?? '?';
        },
        complete() {
            return this.elementProgress === 100;
        },
        elementProgress() {
            if (this.structuralElementLoaded) {
                return this.progressData?.[this.structuralElement.id].progress?.self ?? 0;
            }

            return 0;
        },
        progressTitle() {
            return '';
        },
        shelfURL() {
            return STUDIP.URLHelper.getURL('dispatch.php/course/courseware/', { cid: this.context.id });
        },
        unitRootUrl() {
            return STUDIP.URLHelper.getURL('dispatch.php/course/courseware/courseware/' + this.context.unit, {
                cid: this.context.id,
            });
        },
        commentable() {
            return this.currentElement?.attributes?.commentable ?? false;
        },
        feedback() {
            const parent = {
                type: this.currentElement.type,
                id: this.currentElement.id,
            };

            return this.getRelatedFeedback({ parent, relationship: 'feedback' });
        },
        feedbackCounter() {
            return this.feedback?.length ?? 0;
        },
        hasFeedback() {
            if (this.feedback === null || this.feedbackCounter === 0) {
                return false;
            }

            return true;
        },
        callToActionTitleFeedback() {
            return this.$ngettext(
                '%{length} Anmerkung zur Seite (Nur für Nutzende mit Schreibrechten sichtbar)',
                '%{length} Anmerkungen zur Seite (Nur für Nutzende mit Schreibrechten sichtbar)',
                this.feedbackCounter,
                { length: this.feedbackCounter }
            );
        },
        comments() {
            const parent = {
                type: this.currentElement.type,
                id: this.currentElement.id,
            };

            return this.getRelatedComments({ parent, relationship: 'comments' });
        },
        commentsCounter() {
            return this.comments?.length ?? 0;
        },
        callToActionTitleComments() {
            return this.$ngettext(
                '%{length} Kommentar zur Seite',
                '%{length} Kommentare zur Seite',
                this.commentsCounter,
                { length: this.commentsCounter }
            );
        },
        userIsReviewer() {
            return this.peerReviews.some((peerReview) => peerReview.attributes['is-reviewer']);
        },
        userIsSolver() {
            return this.peerReviews.some((peerReview) => peerReview.attributes['is-submitter']);
        },
        peerReviews() {
            if (this.task) {
                return this.relatedPeerReviews({
                    parent: { id: this.task.id, type: this.task.type },
                    relationship: 'peer-reviews',
                }) ?? [];
            }
            return [];
        },
        isPeerReviewAnonymous() {
            return this.peerReviews.every(({ id, type }) => {
                const process = this.relatedPeerReviewProcesses({
                    parent: { id, type },
                    relationship: 'process',
                });
                return process.attributes.configuration.anonymous;
            });
        },
        canNotShow() {
            if (!this.canVisit) {
                if (this.isTask && this.userIsTeacher) {
                    return { msg: this.$gettext('Sie können die Aufgabe erst nach Abgabe betrachten.'), mood: 'pointing' };
                }

                if (!this.structuralElement.meta['can-read-sequential']) {
                    return { msg: this.$gettext('Bitte bearbeiten Sie alle Inhalte der vorhergehenden Seite, um diese Seite freizuschalten.'), mood: 'pointing' };
                }
                
                return { msg: this.$gettext('Diese Seite steht Ihnen leider nicht zur Verfügung.'), mood: 'sad' };
            }
            return null;
        }
    },

    methods: {
        ...mapActions({
            deleteStructuralElement: 'deleteStructuralElement',
            lockObject: 'lockObject',
            unlockObject: 'unlockObject',
            addBookmark: 'addBookmark',
            companionInfo: 'companionInfo',
            companionWarning: 'companionWarning',
            companionError: 'companionError',
            companionSuccess: 'companionSuccess',
            showElementEditDialog: 'showElementEditDialog',
            showElementAddDialog: 'showElementAddDialog',
            showElementAddChooserDialog: 'showElementAddChooserDialog',
            showElementExportChooserDialog: 'showElementExportChooserDialog',
            showElementExportDialog: 'showElementExportDialog',
            showElementPdfExportDialog: 'showElementPdfExportDialog',
            showElementInfoDialog: 'showElementInfoDialog',
            showElementDeleteDialog: 'showElementDeleteDialog',
            showElementPublicLinkDialog: 'showElementPublicLinkDialog',
            showElementRemoveLockDialog: 'showElementRemoveLockDialog',
            updateShowSuggestOerDialog: 'updateShowSuggestOerDialog',
            showStructuralElementFeedbackDialog: 'showStructuralElementFeedbackDialog',
            showStructuralElementFeedbackCreateDialog: 'showStructuralElementFeedbackCreateDialog',
            showStructuralElementPermissionsDialog: 'showStructuralElementPermissionsDialog',
            updateContainer: 'updateContainer',
            createContainer: 'createContainer',
            sortContainersInStructualElements: 'sortContainersInStructualElements',
            loadTask: 'loadTask',
            loadStructuralElement: 'loadStructuralElement',
            setCurrentElementId: 'coursewareCurrentElement',
            loadProgresses: 'loadProgresses',
            activateStructuralElementComments: 'activateStructuralElementComments',
            deactivateStructuralElementComments: 'deactivateStructuralElementComments',
            loadRelatedFeedback: 'courseware-structural-element-feedback/loadRelated',
            createFeedback: 'feedback-elements/create',
            loadFeedbackElement: 'feedback-elements/loadById',
            setProcessing: 'setProcessing',
            updateUnit: 'courseware-units/update',
            loadUnit: 'courseware-units/loadById',
        }),

        initCurrent() {
            this.currentElement = _.cloneDeep(this.structuralElement);
            this.containerStatus = {};
            this.loadFeedback();
        },
        async menuAction(action) {
            if (['editCurrentElement', 'showPermissions'].includes(action)) {
                await this.loadStructuralElement(this.currentId);
                    if (this.blockedByAnotherUser) {
                        this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });

                        return false;
                    }
                    try {
                        await this.lockObject({ id: this.currentId, type: 'courseware-structural-elements' });
                    } catch (error) {
                        if (error.status === 409) {
                            this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });
                        } else {
                            console.log(error);
                        }

                        return false;
                    }
            }
            switch (action) {
                case 'removeLock':
                    this.displayRemoveLockDialog();
                    break;
                case 'editCurrentElement':
                    this.showElementEditDialog(true);
                    break;
                case 'showPermissions':
                    this.showStructuralElementPermissionsDialog(true);
                    break;
                case 'addElement':
                    this.errorEmptyChapterName = false;
                    this.showElementAddChooserDialog(true);
                    break;
                case 'exportElement':
                    this.showElementExportChooserDialog(true);
                    break;
                case 'deleteCurrentElement':
                    await this.loadStructuralElement(this.currentId);
                    if (this.blockedByAnotherUser) {
                        this.companionInfo({
                            info: this.$gettext(
                                'Löschen nicht möglich, da %{blockingUserName} die Seite bearbeitet.',
                                { blockingUserName: this.blockingUserName }
                            ),
                        });

                        return false;
                    }
                    await this.lockObject({ id: this.currentId, type: 'courseware-structural-elements' });
                    this.showElementDeleteDialog(true);
                    break;
                case 'showInfo':
                    this.showElementInfoDialog(true);
                    break;
                case 'showSuggest':
                    this.updateShowSuggestOerDialog(true);
                    break;
                case 'setBookmark':
                    this.setBookmark();
                    break;
                case 'linkElement':
                    this.showElementPublicLinkDialog(true);
                    break;
                case 'activateFullscreen':
                    STUDIP.Fullscreen.activate();
                    break;
                case 'activateComments':
                    this.activateStructuralElementComments({ element: this.currentElement });
                    break;
                case 'deactivateComments':
                    this.deactivateStructuralElementComments({ element: this.currentElement });
                    break;
                case 'showFeedback':
                    this.showStructuralElementFeedbackDialog(true);
                    break;
                case 'showFeedbackCreate':
                    this.showStructuralElementFeedbackCreateDialog(true);
                    break;
                case 'showNote':
                    this.displayFeedback = true;
                    break;
            }
        },
        selectCurrent() {
            this.$emit('select', this.currentId);
        },
        async closeEditDialog() {
            if (this.blockedByThisUser) {
                await this.unlockObject({ id: this.currentId, type: 'courseware-structural-elements' });
                await this.loadStructuralElement(this.currentElement.id);
            }
            this.showPermissionScopeDialog = false;
            this.showPermissionSettingsDialog = false;
            this.showElementEditDialog(false);
            this.showStructuralElementPermissionsDialog(false);
        },
        async switchPermissionScope() {
            const unit = {
                id: this.currentUnit.id,
                type: 'courseware-units',
                attributes: {
                    'permission-scope': 'structural_element',
                },
            };
            await this.updateUnit(unit);
            await this.loadUnit({ id: this.currentUnit.id });
            this.showPermissionScopeDialog = false;
            this.showPermissionSettingsDialog = true;
        },
        closeAddDialog() {
            this.showElementAddDialog(false);
        },
        dropContainer() {
            this.isDragging = false;
            this.storeSort();
        },

        async storeSort() {
            const timeout = setTimeout(() => this.setProcessing(true), 800);
            if (this.blockedByAnotherUser) {
                this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });
                clearTimeout(timeout);
                this.processing = false;
                return false;
            }
            try {
                await this.lockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            } catch (error) {
                if (error.status === 409) {
                    this.companionInfo({ info: this.$gettext('Diese Seite wird bereits bearbeitet.') });
                } else {
                    console.log(error);
                }

                clearTimeout(timeout);
                this.setProcessing(false);
                return false;
            }

            await this.sortContainersInStructualElements({
                structuralElement: this.structuralElement,
                containers: this.containerList,
            });
            this.selectCurrent();

            clearTimeout(timeout);
            this.setProcessing(false);
        },

        async closeDeleteDialog() {
            await this.loadStructuralElement(this.currentElement.id);
            if (this.blockedByThisUser) {
                await this.unlockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            }
            this.showElementDeleteDialog(false);
        },
        async deleteCurrentElement() {
            await this.loadStructuralElement(this.currentElement.id);
            if (!this.deletable) {
                this.companionWarning({
                    info: this.$gettext('Diese Seite darf nicht gelöscht werden'),
                });
                this.showElementDeleteDialog(false);
                return false;
            }
            if (this.blockedByAnotherUser) {
                this.companionWarning({
                    info: this.$gettext(
                        'Löschen nicht möglich, da %{blockingUserName} die Bearbeitung übernommen hat.',
                        { blockingUserName: this.blockingUserName }
                    ),
                });
                this.showElementDeleteDialog(false);
                return false;
            }
            const redirect_id = this.prevElement.id;
            this.showElementDeleteDialog(false);
            this.companionInfo({ info: this.$gettext('Lösche Seite und alle darunter liegenden Elemente.') });
            this.deleteStructuralElement({
                id: this.currentId,
                parentId: this.structuralElement.relationships.parent.data.id,
            })
                .then(() => {
                    this.$router.push(redirect_id);
                    this.companionInfo({ info: this.$gettext('Die Seite wurde gelöscht.') });
                })
                .catch(() => {
                    this.companionError({ info: this.$gettext('Die Seite konnte nicht gelöscht werden.') });
                });
        },
        containerComponent(container) {
            return 'courseware-' + container.attributes['container-type'] + '-container';
        },
        setBookmark() {
            this.addBookmark(this.structuralElement);
            this.companionInfo({ info: this.$gettext('Das Lesezeichen wurde gesetzt.') });
        },
        displayRemoveLockDialog() {
            this.showElementRemoveLockDialog(true);
        },
        async executeRemoveLock() {
            await this.unlockObject({ id: this.currentId, type: 'courseware-structural-elements' });
            await this.loadStructuralElement(this.currentElement.id);
            this.showElementRemoveLockDialog(false);
        },
        updateContainerList() {
            this.containerList = this.containers;
            const containerRefs = this.$refs.containers;
            for (let ref of containerRefs) {
                ref.initCurrentData();
            }
        },
        loadFeedback() {
            const parent = {
                type: this.currentElement.type,
                id: this.currentElement.id,
            };
            return this.loadRelatedFeedback({
                parent,
                relationship: 'feedback',
                options: {
                    include: 'user',
                },
            }).catch((error) => {
                console.error("Could not load feedback", error);
            });
        },
        keyHandler(e, containerId) {
            switch (e.keyCode) {
                case 27: // esc
                    this.abortKeyboardSorting(containerId);
                    break;
                case 13: // enter
                    e.preventDefault();
                    if (this.keyboardSelected) {
                        this.storeKeyboardSorting(containerId);
                    } else {
                        this.keyboardSelected = containerId;
                        const container = this.containerById({ id: containerId });
                        const index = this.containerList.findIndex((c) => c.id === container.id);
                        this.assistiveLive = this.$gettext(
                            '%{containerTitle} Abschnitt ausgewählt. Aktuelle Position in der Liste: %{pos} von %{listLength}. Drücken Sie die Aufwärts- und Abwärtspfeiltasten, um die Position zu ändern, die Leertaste zum Ablegen, die Escape-Taste zum Abbrechen.',
                            {
                                containerTitle: container.attributes.title,
                                pos: index + 1,
                                listLength: this.containerList.length,
                            }
                        );
                    }
                    break;
            }
            if (this.keyboardSelected) {
                switch (e.keyCode) {
                    case 9: //tab
                        this.abortKeyboardSorting(containerId);
                        break;
                    case 38: // up
                        e.preventDefault();
                        this.moveItemUp(containerId);
                        break;
                    case 40: // down
                        e.preventDefault();
                        this.moveItemDown(containerId);
                        break;
                }
            }
        },
        moveItemUp(containerId) {
            const currentIndex = this.containerList.findIndex((container) => container.id === containerId);
            if (currentIndex !== 0) {
                const container = this.containerById({ id: containerId });
                const newPos = currentIndex - 1;
                this.containerList.splice(newPos, 0, this.containerList.splice(currentIndex, 1)[0]);
                this.assistiveLive = this.$gettext(
                    '%{containerTitle} Abschnitt. Aktuelle Position in der Liste: %{pos} von %{listLength}.',
                    {
                        containerTitle: container.attributes.title,
                        pos: newPos + 1,
                        listLength: this.containerList.length,
                    }
                );
            }
        },
        moveItemDown(containerId) {
            const currentIndex = this.containerList.findIndex((container) => container.id === containerId);
            if (this.containerList.length - 1 > currentIndex) {
                const container = this.containerById({ id: containerId });
                const newPos = currentIndex + 1;
                this.containerList.splice(newPos, 0, this.containerList.splice(currentIndex, 1)[0]);
                this.assistiveLive = this.$gettext(
                    '%{containerTitle} Abschnitt. Aktuelle Position in der Liste: %{pos} von %{listLength}.',
                    {
                        containerTitle: container.attributes.title,
                        pos: newPos + 1,
                        listLength: this.containerList.length,
                    }
                );
            }
        },
        abortKeyboardSorting(containerId) {
            const container = this.containerById({ id: containerId });
            this.keyboardSelected = null;
            this.assistiveLive = this.$gettext(
                '%{containerTitle} Abschnitt, Neuordnung abgebrochen.',
                { containerTitle: container.attributes.title }
            );
            this.selectCurrent();
        },
        storeKeyboardSorting(containerId) {
            const container = this.containerById({ id: containerId });
            const currentIndex = this.containerList.findIndex((container) => container.id === containerId);
            this.keyboardSelected = null;
            this.assistiveLive = this.$gettext(
                '%{containerTitle} Abschnitt, abgelegt. Entgültige Position in der Liste: %{pos} von %{listLength}.',
                {
                    containerTitle: container.attributes.title,
                    pos: currentIndex + 1,
                    listLength: this.containerList.length,
                }
            );
            this.storeSort();
        },

        activateFeedback() {
            const data = {
                attributes: {
                    question: this.$gettext('Bewerten Sie das Lernmaterial'),
                    description: '',
                    mode: 1,
                    'results-visible': true,
                    'is-commentable': true,
                    'anonymous-entries': true,
                },
                relationships: {
                    range: {
                        data: {
                            type: 'courseware-structural-elements',
                            id: this.currentElement.id,
                        },
                    },
                },
            };
            this.createFeedback(data).then(() => {
                this.loadStructuralElement(this.currentElement.id);
            });
        },
        async showFeedbackPopup(to) {
            let showRatingPopup = false;
            let ratingPopupFeedbackElement = null;
            const toId = to.params.id;
            const toElem = this.structuralElementById({ id: toId });
            if (toId === this.nextElement?.id && toElem.relationships.parent.data.id === this.rootId) {
                const firstLevelElement = await this.findFirstLevelParent(this.currentElement);
                const feedbackElementId = firstLevelElement?.relationships?.['feedback-element']?.data?.id;
                if (feedbackElementId) {
                    await this.loadFeedbackElement({ id: feedbackElementId, options: { include: 'entries' } });
                    ratingPopupFeedbackElement = this.getFeedbackElementById({ id: feedbackElementId });
                    const hasUserEntry = this.feedbackEntries.filter(
                        (entry) =>
                            parseInt(entry.relationships?.['feedback-element']?.data?.id) == feedbackElementId &&
                            this.currentUser.id === entry.relationships?.author?.data?.id
                    ).length > 0;

                    if (this.currentUser.id !== ratingPopupFeedbackElement?.relationships?.author?.data?.id && !hasUserEntry) {
                        showRatingPopup = true;
                    } else {
                        ratingPopupFeedbackElement = null;
                    }
                }
            }
            this.showRatingPopup = showRatingPopup;
            this.ratingPopupFeedbackElement = ratingPopupFeedbackElement;
        },
        async findFirstLevelParent(elem) {
            const parentId = elem.relationships.parent.data.id;
            if (!parentId) {
                return null;
            }
            if (parentId == this.rootId) {
                await this.loadStructuralElement(elem.id);
                return this.structuralElementById({ id: elem.id });
            }
            const parent = this.structuralElementById({ id: parentId });

            return this.findFirstLevelParent(parent);
        },
        submitFeedback() {
            this.showRatingPopup = false;
            this.companionSuccess({ info: this.$gettext('Feedback wurde abgegeben.') });
        },

        handleScroll() {
            this.handleContainersScroll();
        },

        handleContainersScroll() {
            let containerItems = document.querySelectorAll('.cw-container-item');
            if (containerItems && containerItems?.length) {
                let lastInView = null;
                for (let container of containerItems) {
                    const rect = container.getBoundingClientRect();
                    let isInView = (
                        rect.top >= 0 &&
                        rect.left >= 0 &&
                        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                    );
                    if (isInView) {
                        lastInView = container;
                    }
                }

                if (lastInView) {
                    let containerId = lastInView.getAttribute('id');
                    this.addRemoveRouteContainerHash(containerId);
                }
            }
        },

        addRemoveRouteContainerHash(hash_id = null) {
            let current_url = window.location.href;
            let hash_list = current_url.split('#');
            let last_hash = hash_list.pop();
            if (last_hash && last_hash.includes('cw_container')) {
                current_url = current_url.replace('#' + last_hash, '');
            }
            this.$router.hash = hash_id;
            let new_href = current_url;
            if (hash_id) {
                new_href += '#' + hash_id;
            }
            window.history.replaceState({}, null, new_href);
        },

        scrollToContainerHash() {
            this.$nextTick(() => {
                if (!this.scrollHasBeenPerformed) {
                    let current_url = window.location.href;
                    let hash_list = current_url.split('#');
                    let last_hash = hash_list.pop();
                    if (last_hash && last_hash.includes('cw_container')) {
                    let containerElement = document.getElementById(last_hash);
                    if (containerElement) {
                            containerElement.scrollIntoView({ behavior: 'smooth', block: "start", inline: "nearest" });
                            this.scrollHasBeenPerformed = true;
                            setTimeout(() => {
                                this.addRemoveRouteContainerHash(last_hash);
                            }, 250);
                        }
                    }
                }
            });
        },

        getPeerReviewProcess(review) {
            return this.relatedPeerReviewProcesses({
                parent: { id: review.id, type: review.type },
                relationship: 'process',
            });
        },
        canReadPeerReviewAssessment(peerReview) {
            if (peerReview.attributes['is-reviewer']) {
                return true;
            }
            const process = this.getPeerReviewProcess(peerReview);
            const isAfter = getProcessStatus(process)?.status === ProcessStatus.After;
            return (this.userIsTeacher || peerReview.attributes['is-submitter']) && isAfter;
        },
        openPeerReview(peerReview) {
            this.selectedPeerReview = peerReview;
            if (peerReview.attributes['is-reviewer']) {
                this.showPeerReviewAssessment = true;
            } else {
                this.showPeerReviewResult = true;
            }
        },
        peerReviewCompanionAction(peerReview) {
            const process = this.getPeerReviewProcess(peerReview);
            if (peerReview.attributes['is-reviewer'] && getProcessStatus(process)?.status === ProcessStatus.Active) {
                return this.$gettext('Peer-Review geben');
            }
            return this.$gettext('Peer-Review einsehen');
        },
        peerReviewCompanionMessage(peerReview) {
            let message;
            if (peerReview.attributes['is-reviewer']) {
                message = this.$gettext('Sie beurteilen diese Aufgabe im Rahmen eines Peer-Reviews.');
            } else if (peerReview.attributes['is-submitter']) {
                message = this.$gettext('Sie haben zu Ihrer Aufgabe ein Peer-Review erhalten.');
            } else {
                message = this.$gettext('Diese Aufgabe hat ein Peer-Review erhalten.');
            }

            if (this.canReadPeerReviewAssessment(peerReview)) {
                return message;
            }

            return `${message} ${this.$gettext('Sie können es jedoch nicht öffnen, da der Bearbeitungszeitraum noch nicht abgelaufen ist.')}`;
        },
        onContainerReady({ containerId, ready }) {
             this.containerStatus[containerId] = ready;

            const allReady = Object.values(this.containerStatus).every(v => v === true)
            if (allReady) {
                this.onAllContainersReady()
            }
        },
        onAllContainersReady() {
            this.loadProgresses();
  }
    },
    created() {
        this.pluginManager.registerComponentsLocally(this);
    },

    watch: {
        $route: {
            handler(to, from) {
                if (this.courseware.attributes['show-feedback-popup']) {
                    this.showFeedbackPopup(to, from);
                }
            },
            deep: true,
        },
        structuralElement: {
            async handler() {
                this.setCurrentElementId(this.structuralElement.id);
                this.initCurrent();
                if (this.isTask) {
                    this.loadTask({
                        taskId: this.structuralElement.relationships.task.data.id,
                    });
                }

                if (this.isLink) {
                    this.loadStructuralElement(this.structuralElement.attributes['target-id']);
                }

                if (this.inCourse && this.courseware.attributes['sequential-progression'] && !this.userIsTeacher) {
                    this.loadProgresses();
                }

                if (this.inCourse && this.hasFeedbackElement) {
                    this.loadFeedbackElement({ id: this.feedbackElementId });
                }
            },
            deep: true,
        },
        containers: {
            handler() {
                this.containerList = this.containers;
                this.scrollToContainerHash();
            },
            deep: true
        },
        containerList: {
            handler() {
                if (this.keyboardSelected) {
                    this.$nextTick(() => {
                        const selected = this.$refs['sortableHandle' + this.keyboardSelected][0];
                        selected.focus();
                        selected.scrollIntoView({behavior: 'smooth', block: 'center'});
                    });
                }
            },
            deep: true
        },
        consumeMode(newState) {
            this.consumModeTrap = newState;
        },
        showPermissionsDialog(newVal) {
            if (newVal) {
                if (this.currentUnit.attributes['permission-scope'] !== 'structural_element') {
                    this.showPermissionScopeDialog = true;
                    this.showPermissionSettingsDialog = false;
                } else {
                    this.showPermissionScopeDialog = false;
                    this.showPermissionSettingsDialog = true;
                }
            }
        }
    },

    // this line provides all the components to courseware plugins
    provide: () => ({
        containerComponents: ContainerComponents,
        coursewarePluginComponents: CoursewarePluginComponents,
    }),

    mounted () {
        this.handleDebouncedScroll = _.debounce(this.handleScroll, 250);
        window.addEventListener('scroll', this.handleDebouncedScroll);
    },

    beforeUnmount() {
        if (this.handleDebouncedScroll) {
            window.removeEventListener('scroll', this.handleDebouncedScroll);
        }
        this.addRemoveRouteContainerHash();
    },
};
</script>
