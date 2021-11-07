let boardId;
var elem = [];
var colors = [];
var d = new Date('2021-05-07');
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        initialDate: '2021-05-07',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: {
            url: "card/duedate",
            method: "post",
            extraParams: function() {
                var i = 0;
                console.log(d.getMonth())
                $(':checked').each(function() {
                    //elem[i] = $(this).val();
                    var id =$(this).val();
                    var color = $(this).attr("color");
                    elem[i] = id;
                    colors[i] = color;
                    
                    i++;
                });

                return {
                    board: elem,
                    color: colors,
                    size: elem.length,
                    dates : d.getMonth()
                }
                
            },
            
            
        },
        eventClick: function(data) {
            var id = data.event.id;
            $.get("card/getCard/" + id, function(result) {
                var res = JSON.parse(result);
                
               console.log(res);
               $("#cardTitle").text("title: " + " "  +res.title );
               $("#cardCreatedAt").text(" createdAt: " +" "+ res.createdAt );
               $("#cardDuedate").text("due date: " + " "+ res.dueDate);
                $("#cardPopUp").dialog({
                    buttons: {
                        Close: function() {
                            $(this).dialog("close")
                        }
                    }

                })
            });
            var duedate = data.event.start;
            var title = data.event.title;
            console.log(title,duedate);
            console.log(id);
            
        },
        
        //eventColor: randColor()
    });
    //console.log(boardId);
    calendar.render();

    $("#onclick").on("input", function() {
        console.log("events")
        calendar.refetchEvents(calendar);
    });
    //getRandomColor()
});

function boardCheck(id) {
    //calendar.refetchEvents();
    //console.log($("#val input:checked").lenght);
    //boardId = id;


    //$.get("card/duedate", { id: id }, "html")
    //$("#onclick").val();

}
function getRandomColor() {

        var letters = '0123456789ABCDEF';
        var color = '#';
       for (var i = 0; i < 6; i++) {
         color += letters[Math.floor(Math.random() * 16)];
        }
       return color;
    
}
function randColor() { //function name
    var color = '#'; // hexadecimal starting symbol
    var letters = ['000000','FF0000','00FF00','0000FF','FFFF00','00FFFF','FF00FF','C0C0C0']; //Set your colors here
    color += letters[Math.floor(Math.random() * letters.length)];
    document.getElementById().style.color = color; // Setting the random color on your div element.
    
}
