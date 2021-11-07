$(function() {
    $("#boardD").hide();
    $("#confirmDeleteBoard").show();
    $(".boardB").show();
})

function deleteBoard(id) {

    $(".suppressionBoard").text("Voulez-vous vraiment supprimez ce tableau?");
    $("#confirmDeleteBoard").dialog({
        buttons: {
            Delete: function() {
                $.post("board/deleteJs", { id: id },function(){
                    history.back();
                }
                    
                    
                )
            },
            Cancel: function() {
                $(this).dialog("close")
            }
        }

    })

} //$('#confirmDialog').dialog({
//                 resizable: false,
//                 height: 300,
//                 width: 500,
//                 modal: true,
//                 autoOpen: true,

//                 buttons: {
//                     Return: function () {
//                         var idRent = $("#rentalid").html();
//                         $.post("rental/returnDate", {rentId: idRent}, refresh, "html");
//                         $(this).dialog("close");
//                     },

//                     Delete: function () {
//                         var idRent = $("#rentalid").html();
//                         $.post("rental/deleteRental", {rentdel: idRent}, refresh, "html");
//                         $(this).dialog("close");
//                     },
//                     Close: function () {
//                         $(this).dialog("close");
//                     }
//                 }
//             });