import {$gettext} from '../lib/gettext';

class Resources
{

    static addUserToPermissionList(user_id, table_element) {
        if (!user_id || !table_element) {
            return;
        }

        var is_temporary_table = false;
        var table_id = jQuery(table_element).attr('id');
        if (table_id === 'TemporaryPermissionList') {
            is_temporary_table = true;
        }

        var template_row = jQuery(table_element).find('tr.resource-permission-list-template')[0];
        if (!template_row) {
            //Something is wrong with the HTML
            return;
        }
        var temp_perms_row = false;

        if (jQuery(template_row).attr('data-temp-perms') === '1') {
            temp_perms_row = true;
        }

        if (!is_temporary_table) {
            //Check if the user is already in the list:
            var trs = jQuery(table_element).find('tr');
            for (var tr of trs) {
                if (jQuery(tr).data('user_id') === user_id) {
                    //We have found a table entry for the user specified by
                    //user_id. Nothing to do here.
                    return;
                }
            }
        }
        var insert_function = function (user_id = null, username = null) {
            var new_row = jQuery(template_row).clone(true);
            jQuery(new_row).removeClass('invisible');
            jQuery(new_row).removeClass('resource-permission-list-template');

            jQuery(new_row).attr('data-user_id', user_id);

            var row_tds = jQuery(new_row).children('td');

            //Set the name-TD's content:
            var user_td_index = 1;
            jQuery(row_tds[user_td_index]).children('input').removeAttr('disabled');

            if (username) {
                jQuery('<span>').text(username).appendTo(row_tds[user_td_index]);
            } else {
                jQuery(row_tds[user_td_index]).append('ID ' + user_id);
            }
            var user_id_input = jQuery(row_tds[user_td_index]).children('input')[0];
            if (!user_id_input) {
                return;
            }
            jQuery(user_id_input).val(user_id);

            if (temp_perms_row) {
                //Set the time input fields to useful values:

                var begin = new Date();
                begin.setHours(begin.getHours() + 1);

                var begin_month = (begin.getMonth() + 1).toString();
                if (begin_month.length === 1) {
                    begin_month = '0' + begin_month;
                }
                var begin_date = begin.getDate()
                    + '.'
                    + begin_month
                    + '.'
                    + begin.getFullYear();

                var begin_time = begin.getHours() + ':00';
                if (begin.getHours() < 10) {
                    begin_time = '0' + begin_time;
                }

                var end = new Date();
                end.setDate(end.getDate() + 14);
                var end_month = (end.getMonth() + 1).toString();
                if (end_month.length === 1) {
                    end_month = '0' + end_month;
                }

                var end_date = end.getDate()
                    + '.'
                    + end_month
                    + '.'
                    + end.getFullYear();

                var end_time = end.getHours() + ':00';
                if (end.getHours() < 10) {
                    end_time = '0' + end_time;
                }

                var begin_td_inputs = jQuery(row_tds[user_td_index + 2]).children();

                jQuery(begin_td_inputs[0]).addClass('has-date-picker');
                jQuery(begin_td_inputs[1]).addClass('has-time-picker');
                jQuery(begin_td_inputs[1]).timepicker({timeFormat: 'HH:mm'});
                jQuery(begin_td_inputs[0]).val(begin_date);
                jQuery(begin_td_inputs[1]).val(begin_time);

                var end_td_inputs = jQuery(row_tds[user_td_index + 3]).children();
                jQuery(end_td_inputs[0]).addClass('has-date-picker');
                jQuery(end_td_inputs[1]).addClass('has-time-picker');
                jQuery(end_td_inputs[1]).timepicker({timeFormat: 'HH:mm'});
                jQuery(end_td_inputs[0]).val(end_date);
                jQuery(end_td_inputs[1]).val(end_time);

            }

            var last_tr = jQuery(table_element).find('tr:last')[0];
            if (!last_tr) {
                //Something is wrong with the HTML.
                return;
            }
            jQuery(last_tr).parent().append(new_row);

            //Make the empty permission list message box
            //invisible if it is still visible:
            jQuery('#ResourceEmptyPermissionListMessage').addClass('invisible');

            //Trigger a table update so that the tablesorter will re-sort
            //the table:
            jQuery(table_element).trigger('update');
        };

        STUDIP.jsonapi.GET(`users/${user_id}`).done(data => {
            const attributes = data.data.attributes;

            let username = `${attributes['family-name']}, ${attributes['given-name']}`;
            if (attributes['name-prefix']) {
                username += `, ${attributes['name-prefix']}`;
            }
            if (attributes['name-suffix']) {
                username += ` ${attributes['name-suffix']}`;
            }
            username += ` (${attributes.username}) (${attributes.permission})`;
            insert_function(user_id, username);
        }).fail(() => {
            insert_function(user_id);
        });
    }


    static addCourseUsersToPermissionList(course_id, table_element) {
        if (!course_id || !table_element) {
            return;
        }

        STUDIP.jsonapi.GET(`courses/${course_id}/memberships`, {data: {page: {limit: 1000000}}}).done(data => {
            data.data.forEach(membership => {
                STUDIP.Resources.addUserToPermissionList(
                    membership.relationships.user.data.id,
                    table_element
                );
            });
        });
    }


    static removeUserFromPermissionList(html_node) {
        if (!html_node) {
            return;
        }

        var row = jQuery(html_node).parent().parent();
        var tbody = jQuery(row).parent();

        STUDIP.Dialog.confirm(
            $gettext('Soll die ausgewählte Berechtigung wirklich entfernt werden?')
        ).done(function () {
            jQuery(row).remove();
            if (jQuery(tbody).children().length < 3) {
                //No special permissions available: show the empty permission list
                //message box:
                jQuery('#ResourceEmptyPermissionListMessage').removeClass('invisible');
            }
        });
    }


    //Room search related methods:


    static addSearchCriteriaToRoomSearchWidget(select_node) {
        if (!select_node) {
            return;
        }

        let selected_option = jQuery(select_node).find(":selected")[0];
        if (!selected_option) {
            return;
        }

        let option_value = jQuery(selected_option).val();
        if (!option_value) {
            //The first option which is left blank intentionally
            //has been selected.
            return;
        }
        let option_title = jQuery(selected_option).data('title');
        let option_type = jQuery(selected_option).data('type');
        let option_select_options = jQuery(selected_option).data('select_options').split(';;');
        let option_range_search = jQuery(selected_option).data('range-search');
        let template;

        if (option_type === 'bool') {
            template = jQuery(select_node).parent().parent().find(
                '.criteria-list .template[data-template-type="'
                + option_type
                + '"]'
            )[0];
        } else if (option_type === 'select') {
            template = jQuery(select_node).parent().parent().find(
                '.criteria-list .template[data-template-type="select"]'
            )[0];
        } else if (option_type === 'date') {
            if (option_range_search) {
                template = jQuery(select_node).parent().parent().find(
                    '.criteria-list .template[data-template-type="date_range"]'
                )[0];
            } else {
                template = jQuery(select_node).parent().parent().find(
                    '.criteria-list .template[data-template-type="date"]'
                )[0];
            }
        } else if (option_type === 'num') {
            if (option_range_search) {
                template = jQuery(select_node).parent().parent().find(
                    '.criteria-list .template[data-template-type="range"]'
                )[0];
            } else {
                template = jQuery(select_node).parent().parent().find(
                    '.criteria-list .template[data-template-type="num"]'
                )[0];
            }
        } else {
            template = jQuery(select_node).parent().parent().find(
                '.criteria-list .template[data-template-type="other"]'
            )[0];
        }

        if (!template) {
            return;
        }

        let criteria_list = jQuery(template).parent();
        let new_criteria = jQuery(template).clone();
        jQuery(new_criteria).attr('class', 'item');
        jQuery(new_criteria).attr('data-criteria', option_value);

        let new_criteria_text_field = jQuery(new_criteria).find('span')[0];
        jQuery(new_criteria_text_field).text(option_title);

        if (option_type === 'bool') {
            let new_criteria_input = jQuery(new_criteria).find('input[type=checkbox]');
            jQuery(new_criteria_input).attr('name', option_value);
            let new_criteria_hidden_input = jQuery(new_criteria).find('input[type=hidden]');
            jQuery(new_criteria_hidden_input).attr('name', 'options_' + option_value);
        } else if (option_type === 'select') {
            let new_criteria_select = jQuery(new_criteria).find('select')[0];
            jQuery(new_criteria_select).attr('name', option_value);
            //Build the option elements from the data-options field:
            if (!option_select_options) {
                //Something is wrong.
                return;
            }
            let options = [];
            for (let option of option_select_options) {
                const opt = $('<option>', {
                    value: option,
                    text: option,
                });
                options.push(opt);
            }
            $(new_criteria_select).append(options);
        } else if (option_type === 'date') {
            let time_inputs = jQuery(new_criteria).find('input[data-time="yes"]');
            let date_inputs = jQuery(new_criteria).find('input[type="date"]');

            if (time_inputs.length < 2) {
                //Something is wrong with the HTML.
                return;
            }
            let now = new Date();

            jQuery(time_inputs[0]).attr('name', option_value + '_begin_time');
            jQuery(time_inputs[1]).attr('name', option_value + '_end_time');
            jQuery(time_inputs[0]).val(
                now.getHours() + ':00'
            );
            jQuery(time_inputs[1]).val(
                (now.getHours() + 2) + ':00'
            );

            if (option_range_search) {
                //We must fill two date fields.
                if (date_inputs.length < 2) {
                    //Something is wrong with the HTML.
                    return;
                }

                jQuery(date_inputs[0]).attr('name', option_value + '_begin_date');
                jQuery(date_inputs[1]).attr('name', option_value + '_end_date');
                jQuery(date_inputs[0]).val(
                    now.getFullYear() + '-'
                    + (now.getMonth() + 1) + '-'
                    + (now.getDate() + 1)
                );
                jQuery(date_inputs[1]).val(
                    now.getFullYear() + '-'
                    + (now.getMonth() + 1) + '-'
                    + (now.getDate() + 2)
                );
            } else {
                //One date field, two time fields.
                if (date_inputs.length < 1) {
                    //Something is wrong with the HTML.
                    return;
                }
                jQuery(date_inputs[0]).attr('name', option_value + '_date');
                jQuery(date_inputs[0]).val(
                    now.getFullYear() + '-'
                    + (now.getMonth() + 1) + '-'
                    + (now.getDate() + 1)
                );
            }

        } else {
            if (option_type === 'num' && option_range_search) {
                let new_criteria_inputs = jQuery(new_criteria).find('input');
                jQuery(new_criteria_inputs[0]).attr('name', option_value);
                let min_input = new_criteria_inputs[1];
                let max_input = new_criteria_inputs[2];
                jQuery(min_input).attr({name: option_value + '_min', type: 'number'});
                jQuery(max_input).attr({name: option_value + '_max', type: 'number'});
            } else {
                let new_criteria_input = jQuery(new_criteria).find('input')[0];
                jQuery(new_criteria_input).attr('name', option_value);
                if (option_type === 'num') {
                    jQuery(new_criteria_input).attr('type', 'number');
                } else {
                    jQuery(new_criteria_input).attr('type', 'text');
                }
            }
        }

        jQuery(criteria_list).append(new_criteria);

        //hide the criteria in the select list:
        jQuery(selected_option).addClass('invisible');
        //set the select field to the first option:
        jQuery(select_node).val('');
    }


    static removeSearchCriteriaFromRoomSearchWidget(icon_node) {
        if (!icon_node) {
            return;
        }

        let input = jQuery(icon_node).parent().find('label').find('input');
        let criteria_name = jQuery(input).attr('name');

        let form = jQuery(icon_node).parents('form')[0];

        if (!form) {
            return;
        }

        let select_element = jQuery(form).find('select.criteria-selector');

        jQuery(icon_node).parent().remove();

        //enable the option in the select field:
        let disabled_option = jQuery(select_element).find(
            'option[value="' + criteria_name + '"]'
        )[0];

        jQuery(disabled_option).removeClass('invisible');

        //Trigger change event:
        jQuery(form).find('.room-search-widget_criteria-list_input').trigger('change');
    }


    static submitRoomSearchWidgetForm(input_node) {
        if (!input_node) {
            return;
        }

        //find the form element:
        var form = jQuery(input_node).parents('form')[0];
        if (!form) {
            return;
        }

        jQuery(form).submit();
    }


    //Resource request related methods:


    static addPropertyToRequest(event) {
        var select = jQuery(event.target).siblings('select.requestable-properties-select')[0];
        if (!select) {
            return;
        }

        var table = jQuery(event.target).parents('.resource-request-properties-table')[0];
        if (!table) {
            return;
        }
        var tbody = jQuery(table).find('tbody')[0];
        if (!tbody) {
            return;
        }

        var selected_option = jQuery(select).find(':selected')[0];
        if (!selected_option) {
            return;
        }

        var property_id = jQuery(selected_option).val();
        var option_html = jQuery(selected_option).data('input-html');
        if (!property_id || !option_html) {
            return;
        }

        var template = jQuery(tbody).find('tr.template')[0];
        if (!template) {
            return;
        }

        var new_row = jQuery(template).clone();
        if (!new_row) {
            return;
        }

        jQuery(new_row).removeClass('template');
        jQuery(new_row).removeClass('invisible');
        jQuery(new_row).attr('data-property_id', property_id);
        var row_cells = jQuery(new_row).find('td');
        jQuery(row_cells[0]).text(jQuery(selected_option).text());
        jQuery(row_cells[1]).html(option_html);

        jQuery(tbody).append(new_row);
        jQuery(tbody).find('.empty-table-message').addClass('invisible');
        jQuery(selected_option).attr('disabled', 'disabled');
        jQuery(selected_option).removeAttr('selected');
        jQuery(select).val([]);
    }


    //ResourceBookingInterval methods:


    static toggleBookingIntervalStatus(event) {
        event.preventDefault();
        let button = event.target.closest('button');
        let intervalId = button.dataset.interval_id;

        if (!intervalId) {
            return;
        }

        const url = STUDIP.URLHelper.getURL(`dispatch.php/resources/ajax/toggle_takes_place_field/${intervalId}`);
        fetch(url)
            .then(response => response.json())
            .then(response => {
                if (response['takes_place'] === undefined) {
                    //Something went wrong: do nothing.
                    return;
                }

                const cell = button.closest('td');
                cell.previousElementSibling.classList.toggle('not-taking-place');
                cell.querySelectorAll('button').forEach(node => node.classList.toggle('invisible'));
            });
    }


    static addResourcePropertyToTable(event) {
        var select = jQuery(event.target).siblings('select')[0];
        if (!select) {
            //Something is wrong with the HTML
            return;
        }

        var selected_property_id = jQuery(select).val();
        var selected_property = jQuery(select).children(
            'option:selected'
        )[0];
        if (!selected_property) {
            return;
        }
        var selected_property_name = jQuery(selected_property).text();

        if (!selected_property_id || !selected_property_name) {
            //Invalid option
            return;
        }

        var table = jQuery(event.target).parents(
            'table'
        )[0];
        if (!table) {
            return;
        }

        var template = jQuery(table).find('tr.template')[0];
        if (!template) {
            return;
        }

        var new_row = jQuery(template).clone();
        if (!new_row) {
            return;
        }

        var columns = jQuery(new_row).find('td');
        var text_field = jQuery(new_row).find('.name');
        jQuery(text_field).text(selected_property_name);
        var set_input = jQuery(new_row).find('.property-input');
        jQuery(set_input).attr(
            'name',
            'prop[' + selected_property_id + ']'
        );
        var value_input = jQuery(new_row).find('.value-input');
        jQuery(value_input).attr(
            'name',
            'prop_value[' + selected_property_id + ']'
        );
        var requestable_input = jQuery(new_row).find('.requestable-input');
        jQuery(requestable_input).attr(
            'name',
            'prop_requestable[' + selected_property_id + ']'
        );
        var protected_input = jQuery(new_row).find('.protected-input');
        jQuery(protected_input).attr(
            'name',
            'prop_protected[' + selected_property_id + ']'
        );

        var tbody = jQuery(table).find('tbody')[0];
        if (!tbody) {
            return;
        }

        jQuery(new_row).removeClass('invisible');
        jQuery(new_row).removeClass('template');
        jQuery(new_row).data('property_id', selected_property_id);
        jQuery(tbody).append(new_row);
        jQuery(selected_property).attr('disabled', 'disabled');
        jQuery(selected_property).removeAttr('selected');
        jQuery(select).val([]);
    }


    //Methods for opening or closing of ressource tree elements:


    static toggleTreeNode(treenode) {
        var arr = treenode.children("img");
        if (arr.hasClass('rotated')) {
            arr.attr('style', 'transform: rotate(0deg)');
        } else {
            arr.attr('style', 'transform: rotate(90deg)');
        }
        arr.toggleClass('rotated');
        treenode.children(".resource-tree").children("li").toggle();
    }


    static moveTimeOptions(bookingtype_val) {
        if (bookingtype_val === 'single') {
            $(".time-option-container").hide();
            $(".block-booking-item").hide();
            $(".repetition-booking-item").hide();
            $("#BookingStartDateInput").show();
            $(".semester-selector").parent().hide();
            $(".manual-time-option").prop('checked', true).trigger('change');
        } else {
            var time_options = $(".time-option-container");
            $(".time-option-container").detach();
            if (bookingtype_val === 'block') {
                $("#BlockBookingFieldset").prepend(time_options);

                $("#BlockEndLabel").show();
                $("#RepetitionEndLabel").hide();

                $(".block-booking-item").show();
                $(".repetition-booking-item").hide();
            } else {
                $("#RepetitionBookingFieldset").prepend(time_options);

                $("#RepetitionEndLabel").show();
                $("#BlockEndLabel").hide();

                $(".repetition-booking-item").show();
                $(".block-booking-item").hide();
            }
            $(".time-option-container").show();
        }
    }


    //Fullcalendar specialisations:


    static updateEventUrlsInCalendar(calendarEvent) {
        if (!calendarEvent) {
            return;
        }

        let parentObjectId = calendarEvent.extendedProps.studip_parent_object_id;
        let begin = STUDIP.Fullcalendar.toRFC3339String(calendarEvent.start);
        let end = STUDIP.Fullcalendar.toRFC3339String(calendarEvent.end);

        const url = STUDIP.URLHelper.getURL(
            `dispatch.php/resources/ajax/get_resource_booking_intervals/${parentObjectId}`,
            {begin, end}
        );
        fetch(url)
            .then(response => response.json())
            .then(response => {
                if (!response || response.length === 0) {
                    return;
                }
                let newIntervalId = response[0].intervalId;
                calendarEvent.setExtendedProp('studip_object_id', newIntervalId);
                if (newIntervalId) {
                    let moveUrl = calendarEvent.extendedProps.studip_api_urls['move'];
                    let resizeUrl = calendarEvent.extendedProps.studip_api_urls['resize'];
                    moveUrl = moveUrl.replace(
                        /&interval_id=([0-9a-f]{32})/,
                        '&interval_id=' + newIntervalId
                    );
                    resizeUrl = resizeUrl.replace(
                        /&interval_id=([0-9a-f]{32})/,
                        '&interval_id=' + newIntervalId
                    );
                    let studipApiUrls = calendarEvent.extendedProps.studip_api_urls;
                    studipApiUrls['move'] = moveUrl;
                    studipApiUrls['resize'] = resizeUrl;
                    calendarEvent.setExtendedProp('studip_api_urls', studipApiUrls);
                }
            })
    }


    static resizeEventInRoomGroupBookingPlan(info) {
        STUDIP.Fullcalendar.defaultResizeEventHandler(info);
        STUDIP.Resources.updateEventUrlsInCalendar(info.event);
    }

    static dropEventInRoomGroupBookingPlan(info) {
        STUDIP.Fullcalendar.defaultDropEventHandler(info);
        STUDIP.Resources.updateEventUrlsInCalendar(info.event);
    }

    static toggleRequestMarked(sourceNode) {
        if (!sourceNode) {
            return;
        }

        let requestId = sourceNode.dataset.request_id

        if (!requestId) {
            return;
        }

        fetch(STUDIP.URLHelper.getURL(`dispatch.php/resources/ajax/toggle_marked/${requestId}`))
            .then(response => response.json())
            .then(response => {
                sourceNode.dataset.marked = response.marked;
                sourceNode.parentNode.dataset.sortValue = response.marked;
                sourceNode.closest('table.request-list').dispatchEvent(new Event('update'));
            })
    }

    static bookAllCalendarRequests() {
        let calendarSection = $('*[data-resources-fullcalendar="1"]')[0];
        if (calendarSection) {
            let calendar = calendarSection.calendar;
            if (calendar) {
                if (!$('#loading-spinner').length) {
                    jQuery('#content').append(
                        $('<div id="loading-spinner" style="position: absolute; top: calc(50% - 55px); left: calc(50% + 135px); z-index: 9001;">').html(
                            $('<img>').attr('src', STUDIP.ASSETS_URL + 'images/loading-indicator.svg')
                                .css({
                                    width: 64,
                                    height: 64
                                })
                        )
                    );
                }
                $('.fc-request-event').each(function () {
                    let objectData = $(this).data();
                    let existingRequestEvent = calendar.getEventById(objectData.eventId);
                    if (existingRequestEvent) {
                        let bookingURL = 'dispatch.php/resources/room_request/quickbook/'
                            + objectData.eventRequest + '/'
                            + objectData.eventResource + '/'
                            + objectData.eventMetadate;
                        jQuery.ajax(
                            STUDIP.URLHelper.getURL(bookingURL),
                            {
                                method: 'get',
                                dataType: 'json',
                                async: false
                            }
                        );
                    }
                });
                document.location.reload(true);
            }
        }
    }

}


//Class properties:


Resources.definedResourceClasses = [
    'Resource', 'Room', 'Building', 'Location'
];


class Messages {
    static selectRoom(room_id, room_name) {
        if (!room_id) {
            return;
        }

        var selection_area = jQuery('.resources_messages-form .selection-area')[0];
        if (!selection_area) {
            return;
        }

        var template = jQuery(selection_area).find('.template')[0];
        if (!template) {
            return;
        }

        var new_room = jQuery(template).clone();
        jQuery(new_room).removeClass('template');
        jQuery(new_room).removeClass('invisible');
        jQuery(new_room).find('span').text(room_name);
        jQuery(new_room).find('input[type="hidden"]').val(room_id);
        jQuery(selection_area).append(new_room);
    }
}

Resources.Messages = Messages;


class BookingPlan {
    static insertEntry(new_entry, date, begin_hour, end_hour) {
        //Get the resource-ID from the current URL:
        let results = window.location.href.match(
            /dispatch.php\/resources\/resource\/booking_plan\/([a-z0-9]{1,32})/
        );
        if (results.length === 0) {
            //No resource-ID found.
            jQuery(new_entry).remove();
            return;
        }
        let resource_id = results[1];

        //Now we re-format the time from begin_hour and end_hour.
        //In case the data-dragged attribute is set for the
        //calendar entry we just add two hours to the start time
        //to get the end time.

        let dragged = jQuery(new_entry).data('dragged');
        if (dragged) {
            end_hour = begin_hour + 2;
        }
        begin_hour += ':00';
        if (end_hour > 23) {
            end_hour = '23:59';
        } else {
            end_hour += ':00';
        }

        let result = STUDIP.Dialog.fromURL(
            STUDIP.URLHelper.getURL(
                'dispatch.php/resources/booking/add/' + resource_id,
                {
                    'begin_date': date,
                    'begin_time': begin_hour,
                    'end_date': date,
                    'end_time': end_hour
                }
            ), {size: 'auto'}
        );
    }
}

Resources.BookingPlan = BookingPlan;


export default Resources;
