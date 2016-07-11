jQuery(function($) {
    //拖拽效果
    $('.dd').nestable();
    $('.dd-handle a').on('mousedown', function(e) {
        e.stopPropagation();
    });
    $('[data-rel="tooltip"]').tooltip();

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
});

function add_order_number(obj) {
    var orderId = $.trim($(obj).attr('order-id'));
    var orderSn = $.trim($(obj).parent().siblings('.order-sn').html());
    var totalNum = this.findMaxDataId();
    totalNum++;
    $(".chosen-select").chosen();
    $('#form_field_select_chosen').css('width','auto');
    $('#dialog-message').find('input[name="supplier_id"]').val(orderId);
    $("#dialog-message").removeClass('hide').dialog({
        width: 350,
        resizable: false,
        modal: true,
        title: "<div class='widget-header'><h4 class='smaller'><i class='icon-warning-sign red'></i><b>" + orderSn + "</b>下新增采购单</h4></div>",
        title_html: true,
        buttons: [
            {
                html: "<i class='icon-ok bigger-110'></i>&nbsp; 提交",
                "class": "btn btn-danger btn-xs",
                click: function() {
                    var orderNumber = $.trim($('input[name="order-number"]').val());
                    var orderPrice = $.trim($('input[name="order-price"]').val());
                    var supplierId = $.trim($('select[name="supplier-id"]').val());
                    var dia = this;
                    $.ajax({
                        url: "/buy/addBuyOrder",
                        type: "post",
                        data: 'order_number=' + orderNumber + '&order_price=' + orderPrice + '&supplier_id=' + supplierId,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === true) {
                                if ($('.dd-list li[order-id-father="' + orderId + '"] .dd-list').size() > 0) {
                                    var template = YayaTemplate(document.getElementById("OrderTemplate2").innerHTML);
                                    $('.dd-list li[order-id-father="' + orderId + '"] .dd-list:first').prepend(template.render({orderNumber: data.message, orderSN: orderNumber, orderPrice: orderPrice, totalNum: totalNum}));
                                } else {
                                    var template = YayaTemplate(document.getElementById("OrderTemplate").innerHTML);
                                    $('.dd-list li[order-id-father="' + orderId + '"] .btn-info:first').after(template.render({orderNumber: data.message, orderSN: orderNumber, orderPrice: orderPrice, totalNum: totalNum}));
                                }
                                $('.dd-handle a').on('mousedown', function(e) {
                                    e.stopPropagation();
                                });
                                $('#dialog-message input').val('');
                                $(dia).dialog("close");
                            } else {
                                alert(data.message);
                            }
                        }
                    });
                }
            }
            ,
            {
                html: "<i class='icon-remove bigger-110'></i>&nbsp; 取消",
                "class": "btn btn-xs",
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });
}

function findMaxDataId() {
    var max = 0;
    $('.dd-item').each(function() {
        var id = parseInt($(this).attr('data-id'));
        if (id > max) {
            max = id;
        }
    })
    return max;
}

function saveBuyOrder(dataId, orderNum, orderSN) {
    var list = $('li.item-red[data-id="' + dataId + '"]').find('.dd-item');
    if (list.size() > 0) {
        var data = [];
        var totalPrice = 0;
        list.each(function(i) {
            var overseaPrice = $.trim($(this).find('.oversea-price').val());
            if (overseaPrice.length == 0) {
                alert('请填写采购商品价格！');
                return false;
            }
            var shippingFee = $.trim($(this).find('.shipping-fee').val());
            if (shippingFee.length == 0) {
                alert('请填写采购邮费！');
                return false;
            }
            var consumptionTax = $.trim($(this).find('.consumption-tax').val());
            if (consumptionTax.length == 0) {
                alert('请填写消费税！');
                return false;
            }
            var useCoupon = $.trim($(this).find('.use-coupon').val());
            if (useCoupon.length == 0) {
                alert('请填写使用优惠券！');
                return false;
            }
            var buyNumber = $.trim($(this).find('.buy-number').val());
            var overseaUrl = $.trim($(this).find('.oversea-url').val());
            var buyNote = $.trim($(this).find('.buy-note').val());
            var orderId = $.trim($(this).find('.order-id').val());
            var goodsId = $.trim($(this).find('.goods-id').val());
            var eachData = {order_id: orderId, goods_id: goodsId, oversea_price: overseaPrice, shipping_fee: shippingFee, consumption_tax: consumptionTax, use_coupon: useCoupon, buy_number: buyNumber, oversea_url: overseaUrl, note: buyNote};
            data.push(eachData);
            totalPrice += parseFloat(overseaPrice) + parseFloat(shippingFee) + parseFloat(consumptionTax) - parseFloat(useCoupon);
        })
        var buyPrice = $.trim($('#buy_id_' + orderNum).html());
        if (parseFloat(buyPrice).toFixed(2) == parseFloat(totalPrice).toFixed(2)) {
            $.ajax({
                url: "/buy/addBuyProducts",
                type: "post",
                data: {data: JSON.stringify(data), orderNum: orderNum},
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        alert('采购单' + orderSN + '添加成功！');
                        $('li[buy-order-id="' + orderNum + '"]').remove();
                    } else {
                        alert(data.message);
                    }
                }
            });
        } else {
            alert('商品总价与购买价不同！');
        }
    } else {
        alert('您尚未在此采购单下选择采购商品！');
    }
}

//HTML转义
function htmlEncode(value) {
    return $('<div />').text(value).html();
}
//HTML反转义
function htmlDecode(value) {
    return $('<div />').html(value).text();
}

$(function(){
    var vglnk = { api_url: '//api.viglink.com/api',
    key: '06e06da81f97719184386ff3660278c6' };

    (function(d, t) {
    var s = d.createElement(t); s.type = 'text/javascript'; s.async = true;
    s.src = ('https:' == document.location.protocol ? vglnk.api_url :
    '//cdn.viglink.com/api') + '/vglnk.js';
    var r = d.getElementsByTagName(t)[0]; r.parentNode.insertBefore(s, r);
    }(document, 'script'));
})
   