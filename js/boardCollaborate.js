function getCardCollaborate(id) {
    $.get("board/getParticipipation/" +id, function(result) {
        var res = JSON.parse(result)
        $("#nbParticipation").text("nombre de participation:" + "   "+ res);
        $("#collaborate").dialog({
            buttons:{
                Close: function() {
                    $(this).dialog("close")
                }
            }
        })

    })

}