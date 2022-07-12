function notification (type, message){
    if (type == 'success'){
        iziToast.success({
            "message" : message,
            position : "topRight",
            messageSize: "20",
        })
    }else if (type == "error"){
        iziToast.error({
            "message" : message,
            position : "topRight",
            messageSize: "20",
        })
    }else if (type == "warning"){
        iziToast.warning({
            "message" : message,
            position : "topRight",
            messageSize: "20",
        })
    }
}