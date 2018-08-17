function openShareMask() {
    $('#share-mask').show();
    setTimeout("closeShareMask()", 5000);
}

function closeShareMask() {
    $('#share-mask').hide();
}

function openSignMask() {
    $('#sign-mask-bg').show();
    $('#sign-mask-div').show();
}

function closeSignMask() {
    $('#sign-mask-bg').hide();
    $('#sign-mask-div').hide();
}

function openPaymentMask() {
    $('#payment-mask-bg').show();
    $('#payment-mask-div').show();
}

function openPaymentMask2() {
    $('#payment-mask-bg').show();
    $('#payment-mask-div2').show();
}

function closePaymentMask() {
    $('#payment-mask-bg').hide();
    $('#payment-mask-div').hide();
    $('#payment-mask-div2').hide();
}

function openProfileMask() {
    $('#profile-mask-bg').show();
    $('#profile-mask-div').show();
}

function closeProfileMask() {
    $('#profile-mask-bg').hide();
    $('#profile-mask-div').hide();
}

function openProtocolMask() {
    $('#protocol-mask-bg').show();
    $('#protocol-mask-div').show();
}

function closeProtocolMask() {
    $('#protocol-mask-bg').hide();
    $('#protocol-mask-div').hide();
}

function openExchangeMask() {
    $('#exchange-mask-bg').show();
    $('#exchange-mask-div').show();
}

function closeExchangeMask() {
    $('#exchange-mask-bg').hide();
    $('#exchange-mask-div').hide();
}

function cdnUrl(url) {
    return /^(?:[a-z]+:)?\/\//i.test(url) ? url : myConfig.upload.cdn_url + url;
}

function checkUpScrolling() {
    if ($(window).scrollTop() + $(window).height() + 1 >= $(document).height()) {
        getListData();
    }
}

$(function () {
    //select默认选中项颜色
    var un_selected = '#999';
    var selected = '#333';
    $('select').css('color', un_selected);
    $('option').css('color', selected);
    $('select').change(function () {
        var sel_item = $(this).val();
        if (sel_item == $(this).find('option:first').val()) {
            $(this).css('color', un_selected);
        } else {
            $(this).css('color', selected);
        }
    });
});
