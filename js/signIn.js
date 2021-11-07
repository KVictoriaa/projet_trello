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
    $("#form").validate({
        rules: {
            mail: {
                remote: {
                    url: "user/confirmEmail",
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
            password: {
                remote: {
                    url: "user/passwordconfirm",
                    type: "post",
                    data: {
                        mail: function() {
                            return $("#mail").val();
                        },
                        password: function() {
                            return $("#password").val();
                        }
                    }
                },
                required: true,
                //regex:/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/,
            }
        },
        messages: {
            mail: {
                remote: "this mail is not exit",
                required: "mail required",
                //regex:"This mail is not valid",
            },
            password: {
                remote: "this password is not exit",
                required: "password required"
                //regex:"This password is not valid",
            },
        }
    });
    $("input:text:first").focus();

}