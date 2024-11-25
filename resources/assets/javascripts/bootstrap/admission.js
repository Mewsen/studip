/* ------------------------------------------------------------------------
 * Anmeldeverfahren und -sets
 * ------------------------------------------------------------------------ */

STUDIP.ready(function () {

    /**
     * Check for admission rules with Vue components
     * @type {NodeListOf<Element>}
     */
    const containers = document.querySelectorAll('[data-admission-rule]');

    containers.forEach(container => {

        const ruleType = container.dataset.admissionRule;

        if (STUDIP.Admission.availableRules[ruleType] !== undefined) {

            import('@/vue/components/admission/' + STUDIP.Admission.availableRules[ruleType])
                .then(result => {
                    const components = {};
                    components[ruleType] = result.default;

                    STUDIP.Vue.load().then(({ createApp }) => {
                        createApp({
                            el: container,
                            components: components
                        });
                    });
                });

        }
    });

    $(document).on('change', 'tr.course input', function(i) {
        STUDIP.Admission.toggleNotSavedAlert();
    });

    $('a.userlist-delete-user').on('click', function(event) {
        $(this).closest('tr').remove();
        return false;
    });

    $('#courseset-form .autosave').on('click', (event) => {
        STUDIP.Admission.autosaveCourseset();
    });

    STUDIP.ready(() => {
        $('#toggle-date-link').on('click', (event) => {
            $('#admissionrule-valid-date').toggleClass('hidden-js');
        });
    });
});
