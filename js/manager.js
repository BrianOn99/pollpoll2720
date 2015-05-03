/* copied form stackoverflow by fearphage for readable tring formatting */
if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined'
            ? args[number]
            : match
            ;
        });
    };
}

$("document").ready(function() {
    $("#more-option").click(function() {
        var defaultrow = $(
                '<tr>' +
                '<td class="col-md-6">' +
                '<input class="form-control" type="text" name="choice-desc" required />' +
                '</td>' +
                '<td class="col-md-6"><form><input type="file" /></form></td>' +
                '</tr>');
        $("#option-table > tbody").append(defaultrow);
    });

    voterEditor = {
        elm : $("#voter-text"),
        eventId : null,

        setText: function(text) {
            this.elm.val(text);
        },

        loadVoters: function() {
            if (!this.eventId) {
                alert("Loading voters: EventId not set!");
                return false;
            }

            textEditor = this;  /* closure variable */

            $.ajax({
                method: "POST",
                url: "../ajax/get_voters.php",
                dataType: "json",
                data: {event_id: this.eventId}
            })
            .done(function(voterList) {
                displayText = "";
                alert(JSON.stringify(voterList));
                for (var voter of voterList) {
                    displayText += "{0}, {1}\n".format(voter.name, voter.email);
                }
                textEditor.setText(displayText);
            })
            .fail(function( jqXHR, textStatus ) {
                alert(textStatus);
                console.log( "Request failed: " + textStatus );
            });
        },

        exportVoters: function() {
            var blob = new Blob([this.elm.val()], {type: "text/csv;charset=utf-8"});
            saveAs(blob, "voter.csv");
        },

        importVoters: function(file) {
            textEditor = this;  /* closure variable */
            var reader = new FileReader();
            alert(file.size);
            reader.onload = function(e) {
                var content = e.target.result;
                textEditor.setText(content);
            }
            reader.readAsText(file);
        }
    }

    $("#get-result").click(function() {
        if (!voterEditor.eventId) {
            alert("Loading voters: EventId not set!");
            return false;
        }

        $.ajax({
            method: "POST",
            url: "../ajax/vote_result.php",
            dataType: "json",
            data: {event_id: voterEditor.eventId}
        })
        .done(function(voterList) {
            alert(JSON.stringify(voterList));
            var dataGot = [];
            for (var i in voterList) {
                choice = voterList[i];
                dataGot.push({y         : parseInt(choice.vote_count),
                              legendText: choice.description});
            }
            var chart = new CanvasJS.Chart("chartContainer", {
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
    });

    $("#get-result-detail").click(function() {
        if (!voterEditor.eventId) {
            alert("Loading voters: EventId not set!");
            return false;
        }

        $.ajax({
            method: "POST",
            url: "../ajax/vote_result_detail.php",
            dataType: "json",
            data: {event_id: voterEditor.eventId}
        })
        .done(function(ret) {
            alert(JSON.stringify(ret));

            var tbody = $("#result-table > tbody");
            tbody.html("");
            var type1Handler = function(voter) {
                ret.voters.forEach(function(voter) {
                    var hasVoted = voter.voted ? "yes" : "no";
                    var newrow = "<tr><td>{0}</td><td>{1}</td></tr>"
                                 .format(voter.name, hasVoted);
                    tbody.append(newrow);
                });
            };
            var type2Handler = function(voter) {
                ret.voters.forEach(function(voter) {
                    var newrow = "<tr><td>{0}</td><td>{1}</td></tr>"
                                 .format(voter.name, voter.voted_choice_label);
                    tbody.append(newrow);
                });
            };
            handlerMap = {1: type1Handler, 2: type2Handler};
            handlerMap[ret.event_type](ret.voter);
        })
        .fail(function( jqXHR, textStatus ) {
            alert(textStatus);
            console.log( "Request failed: " + textStatus.responseText );
        });
    });

    function loadEvents() {
        $.ajax({
            method: "POST",
            url: "../ajax/events.php",
            dataType: "json"
        })
        .done(function(eventList) {
            console.log(JSON.stringify(eventList));
            var tbody = $("#etable");
            eventList.forEach(function(e) {
                var newrow = ('<tr><td>{0}</td><td>{1}</td><td>{2}</td><td>{3}</td>' +
                    '<td><button type="button" class="btn btn-default btn-sm voter-edit" data-eventid="{4}">' +
                    '<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>' +
                    '</button></td></tr>').format(
                        e.title, e.description, e.start_time, e.end_time, e.event_id);
                tbody.append(newrow);

                /*
                 * when the edit button clicked, load voters and switch to voter
                 * tab.
                 * Previously it put this at the bottom of this file, and had a
                 * weird bug: sometimes the clisk event is registerd, sometimes
                 * not.  Guess what? I forget ajax is asynchronous. The button
                 * still not exist sometimes.  Now I know why concurency program is
                 * hard to develop  :-(
                 */
            });

            $(".voter-edit").click(function() {
                voterEditor.eventId = $(this).attr("data-eventid");
                voterEditor.loadVoters();
                $('.nav a[href="#manage"]').trigger("click");
            });
        })
        .fail(function( jqXHR, textStatus ) {
            alert(textStatus);
            console.log( "Request failed: " + textStatus );
        });
    }
    loadEvents();

    $("#addEventForm").submit(function() {
        alert("submit");
        /*
         * TODO:
         * send individual files by ajax first, server give me back a handle id
         * put this id in a hidden field, to associate it with the option
         * finally send all data
         *
         * this has low priority, because it is additionalfunctionality. And, it is
         * very complicated, at least need 5 human hours
         */
        var epoch = function(datestr) {
                var pattern = /([0-9]{2})\/([0-9]{2})\/([0-9]{4}) ([0-9]{1,2}):([0-9]{2}) (AM|PM)/;
                var t = pattern.exec(datestr);
                var hour = parseInt(t[4]);
                if (t[6] == "PM") {
                        hour += 12;
                } else if (t[6] == "AM" && hour == 12) {
                        hour = 0;
                }
                return new Date(parseInt(t[3]), parseInt(t[1])-1, parseInt(t[2]),
                                hour, parseInt(t[5])).getTime() / 1000;
        }

        var formdata = {};
        var options = [];
        $("#addEventForm").serializeArray().map(function(x){formdata[x.name] = x.value;});
        formdata["start"] = epoch(formdata["start"]);
        formdata["end"] = epoch(formdata["end"]);
        var i = $("#option-table input");
        $('#option-table input[name="choice-desc"]').each(function(i) {
            var opt = {};
            opt.desc = $(this).serializeArray()[0].value;
            opt.img = "not set";
            options.push(opt);
        });
        formdata["options"] = options;
        alert(JSON.stringify(formdata));

        $.ajax({
            type: "POST",
            url: "../ajax/add_event.php",
            data: JSON.stringify(formdata),
            contentType: 'application/json; charset=utf-8',
            success: function(data) {
                    console.log(data);
            },
            error: function ( jqXHR, textStatus ) {
                    console.log( "Request failed: " + textStatus );
            }
        });
        return false;
    });

    $("#voter-submit").click(function() {
        voters_info = $("#voter-text").val();
        vdata = {};
        vdata.event_id = voterEditor.eventId;
        vdata.voters = voters_info.split("\n").map(function(row) {
            r = row.split(/, +/);
            return { name: r[0], email: r[1] };
        });
        console.log(JSON.stringify(vdata));
        $.ajax({
            type: "POST",
            url: "../ajax/set_voter.php",
            data: JSON.stringify(vdata),
            contentType: 'application/json; charset=utf-8',
            success: function(data) {
                    console.log(data);
                    alert("submitted");
            },
            error: function ( jqXHR, textStatus ) {
                    console.log( "Request failed: " + textStatus );
            }
        });
    });

    $("#voter-export").click(function() {
        voterEditor.exportVoters();
    });

    var dropbox;

    dropbox = document.getElementById("voter-text");
    dropbox.addEventListener("dragenter", dragenter, false);
    dropbox.addEventListener("dragover", dragover, false);

    function dragenter(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    function dragover(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    $("#voter-text").on("drop", function(e) {
        var f = e.originalEvent.dataTransfer.files[0];
        alert(f.name + " dropped");
        if (f){
            voterEditor.importVoters(f);
        }
        return false;
    });

    $("#help-voter-edit").click(function() {
        var helpmsg = "Please select event in the \"show\" tab\n" +
                      "input as name, email pair,  each pair seperated by newline.\n" +
                      "Drag and drop into the teatarea to import voters from file\n"
        alert(helpmsg);
    });
});
