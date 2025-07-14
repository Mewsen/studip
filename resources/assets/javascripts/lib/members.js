const Members = {
    addPersonToSelection: function(userId, name) {
        const target = $('#persons-to-add');
        let newEl = $('<li>').html(
                $('<span>')
                    .html(name)
                    .text()
            );
        let input = $('<input type="hidden" name="users[]">').val(userId);
        let remove = $('<button>')
            .addClass('btn-icon btn-icon--trash btn-icon--inline')
            .attr('type', 'button')
            .on('click', function () {
                $(this).parent().remove();
            });

        remove.on('click', function() {
            $(this)
                .parent()
                .remove();
        });

        newEl.append(input, remove).appendTo(target);

        return false;
    }
};

export default Members;
