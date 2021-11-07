$(function() {

    valideB();
});

function valideB() {

    $("#formB").validate({
        rules: {
            title: {
                remote: {
                    url: "board/titleIsAvailableB",
                    type: "post",
                    data: {
                        title: function() {
                            return $("#title").val();
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
    $("input:text:first").focus();

}