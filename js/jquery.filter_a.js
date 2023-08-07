/**
 * @param item_selector JQuery Selector for items that must be filtered by
 * @param control_selector JQuery Selector for control buttons
 * */
function filter_a(item_selector,control_selector) {
$(control_selector).click(function (slf) {
    var selectedvalue = slf.currentTarget.getAttribute('data-filter');
    $(control_selector).removeClass('active');
    $(control_selector+"[data-filter='"+selectedvalue+"']").addClass('active');
    if (selectedvalue === 'all') {
        $(item_selector).show();
        return;
    }
    $(item_selector).hide();
    $(item_selector+"[data-category='"+selectedvalue+"']").show();
    //console.log(item_selector+"[data-category='"+selectedvalue+"']");
});
}
/**
 * */
function image_modal() {
    $('.img-thumbnail').click(function (slf) {
        var sel_img = slf.currentTarget.getAttribute('img-url');
        var sel_title = slf.currentTarget.getAttribute('img-title');
        var sel_price = slf.currentTarget.getAttribute('img-price');
        $("#modal_head").html(sel_title);
        $("#modal_img").attr('src',sel_img);
        $("#modal_foot").html(sel_price);
        $("#mdl_btn").click();
    });
}