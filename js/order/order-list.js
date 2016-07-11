$(function() {
    //日期选择
    $('#id-date-range-picker').daterangepicker({
        format: 'YYYY-MM-DD',
        separator: ' 至 '
    }).prev().on(ace.click_event, function() {
        $(this).next().focus();
    });

    $(".chosen-select").chosen();

    //全选
    $('table th input:checkbox').on('click', function() {
        var that = this;
        $(this).closest('table').find('tr > td:first-child input:checkbox, .choose-rec').each(function() {
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });
    });
    //选中单条
    $('td:first-child input:checkbox').on('click', function() {
        var that = this;
        $(this).closest('tr').find('.choose-rec').each(function() {
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });

    });

    //表单提交
    $("#order-list").Validform({
        tiptype: 4,
        btnSubmit: ".btn-purple",
        showAllError: true,
//        ajaxPost: true,
        beforeSubmit: function(curform) {
            var date = $.trim($('#id-date-range-picker').val());
            if (date.length > 0) {
                var start = $.trim($('input[name="daterangepicker_start"]').val());
                var end = $.trim($('input[name="daterangepicker_end"]').val());
                $('#date_start').val(start);
                $('#date_end').val(end);
            }
        }
    });

    //override dialog's title function to allow for HTML titles
    $.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
        _title: function(title) {
            var $title = this.options.title || '&nbsp;'
            if (("title_html" in this.options) && this.options.title_html == true)
                title.html($title);
            else
                title.text($title);
        }
    }));

}) 