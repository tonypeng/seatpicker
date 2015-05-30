function seatpicker(sp, s, e) {

    $(sp).find(".sp-table").each(function() {
        $(this).find("td").each(function() {
            if (e) {
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
}