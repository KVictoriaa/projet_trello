$(function() {
    $(".list", ".columnMoveBoard").draggable({
        revert: "invalid",
        containment:'document',
        helper:"clone" ,
        cursor:"move"
    });
    $(".list").droppable({
        cursor:"move", 
        accept: ".columnMoveBoard > .list",
        revert:true,
        greedy: true, // lorsque on lache l'objet 
        tolerance: "pointer",
        //containment, 
        drop: function(event,ui) {
             
            var startColumn = $(ui.draggable).attr("id");
            var endColumn = $(this).attr("id");
            $.post("column/moveDrag", { startColumn,endColumn }, function(){
               document.location.reload();
            })
        }
    });
    $(".rightColumn").hide();
    $(".leftColumn").hide();

})

