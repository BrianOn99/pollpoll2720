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

$("document").ready(function() {
    /* "show" tab */
    var activate = function(eventId) {
        $.ajax({
            type: "POST",
        url: "../ajax/activate.php",
        dataType: "text",
        data: {event_id: eventId},
        })
        .done(function(response) {
            alert(response);
            console.log(response);
            loadEvents();
        })
        .fail(function( jqXHR, textStatus ) {
            alert(textStatus);
            console.log( "Request failed: " + textStatus );
        });
    };

    function loadEvents() {
        $.ajax({
            method: "POST",
            url: "../ajax/events.php",
            dataType: "json"
        })
        .done(function(eventList) {
            console.log(JSON.stringify(eventList));
            var tbody = $("#etable tbody");
            tbody.html("");
            eventList.forEach(function(e) {
                var newrow = ('<tr><td>{0}</td><td>{1}</td><td>{2}</td><td>{3}</td>' +
                    '<td><button type="button" class="btn btn-default btn-sm voter-edit" data-eventid="{4}">' +
                    '<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>' +
                    '</button></td>').format(
                        e.title, e.description, e.start_time, e.end_time, e.event_id);
                if (!parseInt(e.activated)) {
                    newrow += '<td><button class="activate">Go</button></td>';
                } else {
                    newrow += "<td>Active</td>";
                }
                newrow += "</tr>";
                newrowElm = $(newrow);
                newrowElm.find(".activate").click(function() {
                    activate(e.event_id);
                });

                /*
                 * when the edit button clicked, load voters and switch to voter
                 * tab.
                 * Previously it put this at the bottom of this file, and had a
                 * weird bug: sometimes the clisk event is registerd, sometimes
                 * not.  Guess what? I forget ajax is asynchronous. The button
                 * still not exist sometimes.  Now I know why concurency program is
                 * hard to develop  :-(
                 */
                newrowElm.find(".voter-edit").click(function() {
                    edittingEventId = e.event_id;
                    voterEditor.loadVoters();
                    showChoices();
                    $("#event-manage-tab").removeClass('hidden');
                    $('.nav a[href="#manage"]').trigger("click");
                });

                tbody.append(newrowElm);
            });
        })
        .fail(function( jqXHR, textStatus ) {
            alert(textStatus);
            console.log( "Request failed: " + textStatus );
        });
    }

    function enableEventSort() {
        new Tablesort(document.getElementById('etable'));
    }

    /*
     * "add" tab
     */

    /*
    $("#addEventForm").submit(function() {
        alert("submit");
        var formdata = {};
        $("#addEventForm").serializeArray().map(function(x){formdata[x.name] = x.value;});
        formdata["start"] = epoch(formdata["start"]);
        formdata["end"] = epoch(formdata["end"]);
        alert(JSON.stringify(formdata));

        $.ajax({
            type: "POST",
            url: "../ajax/add_event.php",
            data: JSON.stringify(formdata),
            contentType: 'application/json; charset=utf-8',
            dataType: "text",
            success: function(data) {
                    console.log(data);
                    submitChoices(parseInt(data));
                    loadEvents();
            },
            error: function ( jqXHR, textStatus ) {
                    console.log( "Request failed: " + textStatus );
            }
        });
        return false;
    });
    */

    var collectMetaData = function() {
        alert("collecting question");
        var formdata = {};
        $("#addEventForm").serializeArray().forEach(function(x){
            if (x.name != "choice-desc") {
                formdata[x.name] = x.value;
            }
        });
        formdata["start"] = epoch(formdata["start"]);
        formdata["end"] = epoch(formdata["end"]);
        
        alert(JSON.stringify(formdata));
        return formdata;
    };

    $("#addEventForm").submit(function() {
        alert("submit");
        var fd = new FormData();
        fd.append("metadata", JSON.stringify(collectMetaData()));
        var choicesInfo = {};
        var choiceLabel = "A";
        var nextChar = function(c) {
            return String.fromCharCode(c.charCodeAt(0) + 1); 
        }

        $(".choice-row").each(function() {
            var file = $(this).find('input[type="file"]')[0].files[0]
            var desc = $(this).find('input[type="text"]').val();
            fd.append(choiceLabel, file);
            choicesInfo[choiceLabel] = desc;
            choiceLabel = nextChar(choiceLabel);
        });

        console.log(JSON.stringify(choicesInfo));
        fd.append("choices_info", JSON.stringify(choicesInfo));

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../ajax/add_event.php', true);
        xhr.onload = function() {
            if (this.status == 200) {
                console.log('Server got:'+ this.response);
            } else {
                alert("Error submit:" + this.status);
            }
        };
        xhr.send(fd); 

        return false;
    });

    /*
     * "manage" tab
     */
    var edittingEventId = null;

    $("#more-option").click(function() {
        var defaultrow = $(
                '<div class="row choice-row">' +
                '<span class="col-sm-6">' +
                '<input class="form-control" type="text" name="choice-desc" required />' +
                '</span>' +
                '<span class="col-sm-6"><input type="file" /></span>' +
                '</div>'
                );
        $("#choice-list").append(defaultrow);
    });

    var voterEditor = {
        elm : $("#voter-text"),

        setText: function(text) {
            this.elm.val(text);
        },

        loadVoters: function() {
            if (!edittingEventId) {
                alert("Loading voters: EventId not set!");
                return false;
            }

            textEditor = this;  /* closure variable */

            $.ajax({
                method: "POST",
                url: "../ajax/get_voters.php",
                dataType: "json",
                data: {event_id: edittingEventId}
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

    var showChoices = function() {
        if (!edittingEventId) {
            alert("getting result: EventId not set!");
            return false;
        }
        $.ajax({
            type: "POST",
            url: "../ajax/get_choices.php",
            dataType: "json",
            data: {event_id: edittingEventId},
        })
        .done(function(response) {
            var output = "";
            response.forEach(function(choice) {
                output += "<p>{0} : {1}</p>".format(choice.label, choice.description);
            });
            $("#choices-label").html(output);
        })
        .fail(function( jqXHR, textStatus ) {
            alert(textStatus);
            console.log( "Request failed: " + textStatus );
        });
    };

    $("#voter-submit").click(function() {
        var pattern = /^([a-z ]+),[ ]*([a-z_1-9]+@[a-z1-9.]+)$/i;

        voters_info = $("#voter-text").val();
        vdata = {};
        vdata.event_id = edittingEventId;
        vdata.voters = voters_info.split("\n").map(function(row) {
            var res = pattern.exec(row);
            if (!res) {
                alert("incorrect voter info");
                throw "incorrect voter info";
            }
            return { name: res[1], email: res[2] };
        });
        alert("Correct Voter format");
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
            error: function ( jqXHR, textStatus, err) {
                    alert("Request failed: " + err);
            }
        });
    });

    $("#voter-export").click(function() {
        voterEditor.exportVoters();
    });

    $("#get-result").click(function() {
        if (!edittingEventId) {
            alert("getting result: EventId not set!");
            return false;
        }

        renderResult("chartContainer", edittingEventId);
    });

    $("#get-result-detail").click(function() {
        if (!edittingEventId) {
            alert("getting result detail: EventId not set!");
            return false;
        }

        $.ajax({
            method: "POST",
            url: "../ajax/vote_result_detail.php",
            dataType: "json",
            data: {event_id: edittingEventId}
        })
        .done(function(ret) {
            alert(JSON.stringify(ret));

            var tbody = $("#result-table > tbody");
            var theadVoterDesc = $("#voter-desc");
            tbody.html("");
            var type1Handler = function(voter) {
                theadVoterDesc.text("Voted");
                ret.voters.forEach(function(voter) {
                    var hasVoted = voter.voted ? "yes" : "no";
                    var newrow = "<tr><td>{0}</td><td>{1}</td></tr>"
                                 .format(voter.name, hasVoted);
                    tbody.append(newrow);
                });
            };
            var type2Handler = function(voter) {
                theadVoterDesc.text("Vote Casted");
                ret.voters.forEach(function(voter) {
                    if (voter.voted_choice_label == null) {
                        voter.voted_choice_label = "not voted";
                    }
                    var newrow = "<tr><td>{0}</td><td>{1}</td></tr>"
                                 .format(voter.name, voter.voted_choice_label);
                    tbody.append(newrow);
                });
            };
            handlerMap = {1: type1Handler, 2: type2Handler};
            handlerMap[ret.event_type](ret.voter);
        })
        .fail(function(jqXHR, textStatus, err) {
            alert(textStatus + " " + err);
        });
    });

    $("#voter-export").click(function() {
        voterEditor.exportVoters();
    });

    /*
     * initialization
     */

    enableEventSort();
    loadEvents();

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

    /*
     * End Initializtion
     */
});
