$(function() {
    $(".column").hide();
    $("#confirmDeletecolumn").show();
    $(".columnB").show();
})

function deleteColumn(id) {
    $("#confirmDeleteColumn").text("Voulez-vous vraiment supprimer cette column?");
    $("#confirmDeleteColumn").dialog({ 
        
        buttons: {
            Delete: function() {
                $.post("column/deleteColumnJS", { idColumn: id }, function() {
                        $("div").remove("#" + id);
                        
                    });$(this).dialog("close");
            },
            Cancel: function() {
                $(this).dialog("close")
            }
        }

    })

}