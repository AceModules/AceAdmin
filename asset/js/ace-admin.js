jQuery(function($) {
    $.fn.modalForm = function() {
        $(this).on("submit", function(e) {
            e.preventDefault();

            var form = $(this);
            var modal = form.parents(".modal");

            $.ajax({
                type: form.attr("method"),
                url: form.attr("action"),
                data: form.serialize(),

                success: function(data) {
                    if (!data) {
                        return location.reload();
                    }

                    modal.html(data);
                    $("form", modal).modalForm();
                }
            });
        });

        $(".selectpicker", this).selectpicker().filter("[data-ajax-url]").ajaxSelect();

        $(".summernote", this).summernote({
            minHeight: 300,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['link', ['linkDialogShow', 'unlink']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['misc', ['undo', 'redo']],
                ['code', ['codeview']]
            ]
        });
    };

    $('#modalForm').appendTo("body");

    $(document).on("show.bs.modal", function(e) {
        var link = $(e.relatedTarget);
        $(e.target).load(link.attr("href"), function() {
            $("input:enabled:visible", this).first().focus();
            $("form", this).modalForm();
        });
    });

    $(document).on("hidden.bs.modal", function(e) {
        $(e.target).removeData("bs.modal").empty();
    });

    $(".main-holder").removeClass("main-holder");

    $('input[type="date"]').change(function() {
        if ($(this).val().length < 1) {
            $(this).addClass('datepicker');
        } else {
            $(this).removeClass('datepicker');
        }
    });

    $('[data-placeholder]').attr('placeholder', function() { return $(this).attr('data-placeholder'); });

    $(".admin-header button[type=submit]").removeAttr("name");
    $.applyDataMask();
});