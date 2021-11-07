$(function() {
    $("ul", ".cardMoveColumn").draggable({
        revert: "invalid",
        containment:'document',
        helper:"clone" ,
        cursor:"move"
    });
    $("ul , .listColumn").droppable({
        cursor:"move", 
        accept: ".cardMoveColumn > ul  " ,
        revert:true,
        greedy: true, // lorsque on lache l'objet 
        tolerance: "pointer",
        //containment, 
        drop: function(event,ui) {
             
            var startCard = $(ui.draggable).attr("id");
            var endCard = $(this).attr("id");
            var startColumn = $(ui.draggable).attr("idColumn");
            var endColumn = $(this).attr("idColumn");
            console.log(endColumn);
            if(startColumn == endColumn) {
                $.post("card/moveCardDragUpDown", { startCard,endCard}, function() {
                    document.location.reload();
                 })
            }
            else {
                $.post("card/moveCardDrag", { startCard,startColumn,endColumn }, function() {
                    document.location.reload();
            })
            }
            
        }
    });
    $(".rightCard").hide();
    $(".leftCard").hide();
    $(".upCard").hide();
    $(".downCard").hide();
})