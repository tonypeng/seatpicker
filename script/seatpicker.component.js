function seatpicker(sp, s, sb, e) {

    $(sp).find(".sp-table").each(function() {
        $(this).find("td").each(function() {
            if (e && $(this).hasClass('editable')) {
                $(this).click(function() {
                    var taken = $(this).data('taken');
                    var gender = $(this).data('gender');
                    var id = $(this).data('id');

                    if(taken) {
                        alert('This seat is already taken.');
                        return;
                    }

                    $.post('ajax/post_seat', { seat: id }, function(data) {
                        if(data.error) {
                            alert(data.error);
                        } else {
                            location.reload();
                        }
                    });
                });
            }
            $(this).qtip();
        });
    });

    $(sb).click(function() {
        var studentName = $(s).val().toLowerCase();

        if (studentName.length <= 0) return;

        $(sp).find(".sp-table").each(function() {
            $(this).find("td").each(function() {
                var title = $(this).attr('title');

                if (title && title.toLowerCase().indexOf(studentName) >= 0) {
                    $(this).addClass('highlight');
                } else {
                    $(this).removeClass('highlight');
                }
            });
        });
    });
}