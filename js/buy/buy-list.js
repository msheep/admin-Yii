$(function() {
    $(".chosen-select").chosen();
    /*
     * 日期选择
     */
    $('#id-date-range-picker').daterangepicker({
        format: 'YYYY-MM-DD',
        separator: ' 至 '
    }).prev().on(ace.click_event, function() {
        $(this).next().focus();
    });

    /*
     * 全选
     */
    $('table th input:checkbox').on('click', function() {
        var that = this;
        $(this).closest('table').find('tr > td:first-child input:checkbox, .choose-rec').each(function() {
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });
    });

    /*
     * 选中单条
     */
    $('td:first-child input:checkbox').on('click', function() {
        var that = this;
        var orderId = $(this).attr('order-id');
        $('.choose-rec[rec-id="'+orderId+'"]').each(function() {
            this.checked = that.checked;
            $(this).closest('tr').toggleClass('selected');
        });

    });

    /*
     * 表单提交
     */
    $("#order-list").Validform({
        tiptype: 4,
        btnSubmit: ".btn-purple",
        showAllError: true,
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

    /*
     * 导出excel
     */
    $('.excel').on('click', function(e) {
        var id = $(this).attr('id');
        e.preventDefault();
        var order = [];
        $('input[name="rec-choose-box"]:checked').each(function() {
            order.push($.trim($(this).attr('buy-id')));
        })
        var search = window.location.search;
        if (!search) {
            var url = '/buy/buyList?type=' + id;
        } else {
            var url = '/buy/buyList' + search + '&type=' + id;
        }
        if (order.length > 0) {
            var orderStr = order.join(",");
            window.location.href = url + '&excel=' + orderStr;
        } else {
            var totalNumber = $('#total-number').html();
            if (!totalNumber) {
                totalNumber = 0;
            }
            var content = '你尚未选择某个订单，将导出当前<strong>' + totalNumber + '</strong>个订单详情！';
            $('#dialog-message .alert-info').html(content);
            // Dialogs
            $("#dialog-message").removeClass('hide').dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i>你确定吗？</h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='icon-trash bigger-110'></i>&nbsp; Yes",
                        "class": "btn btn-danger btn-xs",
                        click: function() {
                            window.location.href = url + '&excel=all';
                            $(this).dialog("close");
                        }
                    }
                    ,
                    {
                        html: "<i class='icon-remove bigger-110'></i>&nbsp; No",
                        "class": "btn btn-xs",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }
                ]
            });
        }
    })

})


function deleteBuy(id) {
    $.ajax({
        url: "/buy/deleteBuyOrder",
        type: "post",
        data: 'id=' + id,
        dataType: 'json',
        success: function(data) {
            if (data.status === true) {
                window.location.reload();
            } else {
                alert(data.message);
                return false;
            }
        }
    });
}

function changeInput(obj, id, column) {
    var content = $.trim($(obj).html());
    if (content.indexOf("input") == -1 && content.indexOf("textarea") == -1) {
        if ($.trim(column) != 'note' && $.trim(column) != 'oversea_url') {
            var html = '<input type="text" style="width: 80%;margin: 0;" name="' + id + '_' + column + '" value="' + content + '" onblur="saveChangeInput(this,' + "'" + id + "'" + ',' + "'" + column + "'" + ')">';
            $(obj).html(html);
            $(obj).find('input').focus();
        } else {
            if(content == '/'){
                content = '';
            }
            var html = '<textarea style="width: 80%;margin: 0;height:90%; line-height:22px;" name="' + id + '_' + column + '" onblur="saveChangeInput(this,' + "'" + id + "'" + ',' + "'" + column + "'" + ')">' + content + '</textarea>';
            $(obj).html(html);
            $(obj).find('textarea').focus();
        }
    }
}

function saveChangeInput(obj, id, column) {
    var cont = $.trim($(obj).val());
    if ($.trim(column) == 'note' || $.trim(column) == 'oversea_url') {
        if(cont.length == 0){
            cont = '/';
        }
    }
    $.ajax({
        url: "/buy/updateBuyOrder",
        type: "post",
        data: 'id=' + id + '&column=' + column + '&cont=' + cont,
        dataType: 'json',
        success: function(data) {
            if (data.status === true) {
                $('#' + id + '_' + column).html(cont);
            } else {
                alert(data.message);
                return false;
            }
        }
    });
}

