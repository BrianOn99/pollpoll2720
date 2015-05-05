function renderResult(targetElmId, eventId, voterId, key) {
    var ajaxdata =  {event_id: eventId}
    if (voterId) {
        ajaxdata["voter_id"] = voterId;
        ajaxdata["key"] = key;
    }

    $.ajax({
        method: "POST",
        url: "../ajax/vote_result.php",
        dataType: "json",
        data: ajaxdata
    })
    .done(function(voterList) {
        alert(JSON.stringify(voterList));
        var dataGot = [];
        for (var i in voterList) {
            choice = voterList[i];
            dataGot.push({y         : parseInt(choice.vote_count),
                          legendText: choice.description});
        }
        var chart = new CanvasJS.Chart(targetElmId, {
            title:{
                text: "Result"
            },
            animationEnabled: true,
            legend: {
                verticalAlign: "bottom",
                horizontalAlign: "center"
            },
            data: [
                {
                    indexLabelFontSize: 20,
                    indexLabelFontFamily: "Garamond",
                    indexLabelFontColor: "darkgrey",
                    indexLabelLineColor: "darkgrey",
                    indexLabelPlacement: "outside",
                    type: "doughnut",
                    showInLegend: true,
                    dataPoints: dataGot
                }
            ]
        });
        chart.render();
    })
    .fail(function( jqXHR, textStatus ) {
        alert(textStatus);
        console.log( "Request failed: " + textStatus );
    });
}
