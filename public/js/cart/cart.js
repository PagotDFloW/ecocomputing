function addToCart(id){
    $.ajax({
        url:        '/produits/cart/add/'+id,
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


