$(function() {

    
});
function doValidate(columnId) {
    validateCard(columnId);
}

function validateCard(columnId) {
    $("#formCard"+ columnId ).validate({
        rules: {
            title: {
                remote: {
                    url: "card/titleIsAavaible",
                    type: "post",
                    data: {
                        title: function() {
                            return $("#title" + columnId).val();
                        },
                        IDColumn: columnId,

                    }
                },
                required: true,
                minlength: 3
            }
        },
        messages: {
            title: {
                remote: "le titre est déja utilisé",
                required: "le titre est requis",
                minlength: "le titre  doit posséder au moins trois lettres"
            }
        }
    });
    

}