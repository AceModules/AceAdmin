$(function() {
    $('.admin-header button[type=submit]').removeAttr('name');

    $.fn.modalForm = function() {
        $(this).prettyForm().on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var modal = form.parents('.modal');

            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),

                success: function(data) {
                    if (!data) {
                        return location.reload();
                    }

                    modal.html(data);
                    $('form', modal).modalForm();
                }
            });
        });

        return this;
    };

    $('#modalForm').on('show.bs.modal', function(e) {
        var link = $(e.relatedTarget);
        $(e.target).load(link.attr('href'), function() {
            setTimeout(function() { $(this).focus(); $('input:enabled:visible', this).first().focus(); }.bind(this), 300);
            $('form', this).modalForm();
        });
    });

    $('#modalForm').on('hidden.bs.modal', function(e) {
        $(e.target).removeData('bs.modal').empty();
    });
});