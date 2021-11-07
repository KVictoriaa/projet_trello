$(function() {

    validateColumn();
});

function validateColumn() {
    $("#formC").validate({
        rules: {
            title: {
                remote: {
                    url: "column/titleIsAavaible",
                    type: "post",
                    data: {
                        title: function() {
                            
                            return $("#title").val();
                        },
                        IDBoard: function() {
                            return $("#IDBoard").val();
                        }

                    }

                },
                required: true,
                minlength: 3,
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