const ActivityFeed = {
    user_id : null,
    polling: null,
    initial: true,
    scrolledfrom: null,
    maxheight: null,
    filter: null,

    init() {
        STUDIP.ActivityFeed.maxheight = parseInt($('#stream-container').css('max-height').replace(/[^-\d.]/g, ''));

        STUDIP.ActivityFeed.loadFeed(STUDIP.ActivityFeed.filter);

        $('#stream-container').scroll(() => {
            const scrollBottom = $('#stream-container').scrollTop() + $('#stream-container').height() + 250;

            if ($('#stream-container').prop('scrollHeight') < scrollBottom) {
                STUDIP.ActivityFeed.loadFeed(STUDIP.ActivityFeed.filter);
            }
        });


        $(document).on('click', '.provider_circle', function () {
            $(this).parent().parent().children('.activity-content').toggle();
        }).on('click', '#toggle-all-activities,#toggle-user-activities', function () {
            const toggled = $(this).is(':not(.toggled)');
            $(this).toggleClass('toggled', toggled);

            STUDIP.ActivityFeed.setToggleStatus();

            return false;
        });
    },

    getTemplate: _.memoize(name => {
        return _.template($(`script.${name}`).html());
    }),

    loadFeed(filtertype) {
        if (STUDIP.ActivityFeed.user_id === null) {
            console.log('Could not retrieve activities, no valid user id found!');
            return false;
        }

        if (STUDIP.ActivityFeed.polling || !STUDIP.ActivityFeed.scrolledfrom) {
            return false;
        }

        STUDIP.ActivityFeed.polling = true;

        const url = STUDIP.URLHelper.getURL('dispatch.php/activityfeed/load', {
            filtertype: JSON.stringify(filtertype),
            scrollfrom: STUDIP.ActivityFeed.scrolledfrom,
        });
        fetch(url).then(
            response => response.json(),
        ).then(activities => {
            const stream = STUDIP.ActivityFeed.getTemplate('activity_stream');
            const activity = STUDIP.ActivityFeed.getTemplate('activity');
            const activity_urls = STUDIP.ActivityFeed.getTemplate('activity-urls');
            const num_entries = Object.keys(activities).length;
            const lastelem = $(activities).last();

            if (lastelem[0]) {
                STUDIP.ActivityFeed.scrolledfrom  = lastelem[0].mkdate;
            } else {
                STUDIP.ActivityFeed.scrolledfrom = false;
            }

            STUDIP.ActivityFeed.writeToStream(stream({
                stream        : activities,
                num_entries   : num_entries,
                activity      : activity,
                activity_urls : activity_urls,
                user_id       :  STUDIP.ActivityFeed.user_id
            }));

            STUDIP.ActivityFeed.setToggleStatus();

            if ($('#stream-container').height() < STUDIP.ActivityFeed.maxheight) {
                STUDIP.ActivityFeed.loadFeed('');
            }
        }).catch(() => {
            const template = STUDIP.ActivityFeed.getTemplate('activity-load-error');
            STUDIP.ActivityFeed.writeToStream(template());
        }).finally(() => {
            STUDIP.ActivityFeed.polling = false;
        });
    },

    writeToStream(html) {
        if (STUDIP.ActivityFeed.initial) {
            // replace data in DOM
            $('#stream-container').html('');

            STUDIP.ActivityFeed.initial = false;
        }

        $('#stream-container').append(html);
    },

    setToggleStatus() {
        const show_details = $('#toggle-all-activities').is('.toggled');
        const show_own = $('#toggle-user-activities').is('.toggled');

        // update toggle status fir activity contents
        $('.activity-content').toggle(show_details);

        // update toggle status for user's own activities
        $('.activity:has(.provider_circle.right)').toggle(show_own);
    },

    updateFilter(filter) {
        STUDIP.ActivityFeed.filter = filter;
        STUDIP.ActivityFeed.initial = true;
        STUDIP.ActivityFeed.scrolledfrom = Math.floor(Date.now() / 1000);

        $('#stream-container').html('<div class="loading-indicator">'
            + '<span class="load-1"></span>'
            + '<span class="load-2"></span>'
            + '<span class="load-3"></span>'
            + '</div>');

        STUDIP.ActivityFeed.init();
    }
};

export default ActivityFeed;
