$(function() {

    validate();
});
$.validator.addMethod("regex", function (value, element, pattern) {
    if (pattern instanceof Array) {
        for (p of pattern) {
            if (!p.test(value))
                return false;
        }
        return true;
    } else {
        return pattern.test(value);
    }

});
function validate() {
    console.log("hhhh");
    $("#form").validate({
        rules: {
            mail: {
                remote: {
                    url: "user/emailIsAvalaible",
                    type: "post",
                    data: {
                        mail: function() {
                            return $("#mail").val();
                        }

                    }

                },
                required: true,

                regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,

            },
            fullname: {
                required: true,
                minlength: 3,
            },
            password: {
                required: true,
                minlength: 3,
                //regex:,

            },
            confirmpassword: {
                required : true,
                equalTo : "#password"
            }

        },
        messages: {
            mail: {
                remote: "le mail est déja utilisé",
                required: "cet email est requis",
                regex: "le mail n'est pas valide"
            },
            FullName: {
                required: " le nom et prenom sont requis",
                minlength: "le nom et prénom doivent être d'au moins trois lettres"
            },
            password: {
                required: "le mot de passe est requis",
                minlength: "le password doit contenir au moins huit lettres",
                regex: "le password doit contenir au moins une lettre majuscule ,un chiffre et un caractère"
            },
            confirmpassword: {
                required: "le mot de passe est requis",
                //minlenght: ""
            }
        }
    });
    $("input:text:first").focus();

}