$(document).ready(function(){
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const page = urlParams.get('page')

    //Variables de filtrage
    var filter = {
        category: [],
        minPrice: $(".minPrice").val(),
        maxPrice: $(".maxPrice").val(),
        sortBy: $(".form-select").val(),
        search : urlParams.get('q'),
        state: []
    }

    productListAction.getProductList(page, filter);
    $(".form-select").change(function(){
        filter.sortBy = $(this).val();
        productListAction.getProductList(page, filter);
    });
    $(".minPrice").keyup(function(){
        filter.minPrice = $(this).val();
        productListAction.getProductList(page, filter);
    });
    $(".maxPrice").keyup(function(){
        filter.maxPrice = $(this).val();
        productListAction.getProductList(page, filter);
    });
    $('.categorie-checkbox').change(function(){
        if($(this).is(':checked')){
            filter.category.push($(this).val());
        }else{
            filter.category.splice(filter.category.indexOf($(this).val()), 1);
        }
        productListAction.getProductList(page, filter);
    });
    $('.product-state').change(function(){
        if($(this).is(':checked')){
            filter.state.push($(this).val());
        }else{
            filter.state.splice(filter.state.indexOf($(this).val()), 1);
        }
        productListAction.getProductList(page, filter);
    });
    $('.reset-product-filter').click(function(){
        filter.category = [];
        filter.minPrice = "";
        filter.maxPrice = "";
        filter.sortBy = "";
        filter.state = [];

        $('.categorie-checkbox').prop('checked', false);
        $('.product-state').prop('checked', false);
        $('.minPrice').val("");
        $('.maxPrice').val("");
        productListAction.getProductList(page, filter);
    });

});


var productListAction = {

    getProductList : function(page, filter){
        $('.spinner-border').show();
        $('.product-list').html("");
        $.ajax({
            url:        '/produits/list',
            type:       'POST',
            dataType:   'html',
            async:      true,
            data : {page: page, filter: filter},

            success: function(data, status) {
                $('.spinner-border').hide();
                $('.product-list').show();
                $('.product-list').html(data);

            },
            error : function(xhr, textStatus, errorThrown) {
                alert('Ajax request failed.');
            }
        });
    }
}

function addToFavorites(id){
    $.ajax({
        url:        '/produits/favorites/add/'+id,
        type:       'POST',
        dataType:   'json',
        async:      true,
        data : {id: id},

        success: function(data, status) {
            notification(data.type, data.message);
        },
        error : function(xhr, textStatus, errorThrown) {
            alert('Ajax request failed.');
        }
    });
}