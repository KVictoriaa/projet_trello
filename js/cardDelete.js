$(function() {
    $(".card").hide();
    $("#confirmDeletecard").show();
    $(".cardB").show();
})

function deleteCard(id) {
    $("#confirmDeleteCard").text("Voulez-vous vraiment supprimer cette card?");
    $("#confirmDeleteCard").dialog({
        buttons: {
            Delete: function() {
                $.post("card/deleteJs", { idCard: id }, function() {
                    console.log(id);
                    $("ul").remove("#" + id);
                });
                $(this).dialog("close");
            },
            Cancel: function() {
                $(this).dialog("close");
            }
        }

    })

}