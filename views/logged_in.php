<h1>Manager</h1>
<p>Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.</p>
<a href="index.php?logout">Logout</a>

<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#list" aria-controls="list" role="tab" data-toggle="tab">Show</a></li>
    <li role="presentation"><a href="#add" aria-controls="add" role="tab" data-toggle="tab">Add</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="list">
      <table class="table table-striped" id="etable">
        <thead>
          <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Start</th>
            <th>End</th>
          </tr>
        </thead>
        <tbody>
        <tr>
          <td>E</td>
          <td>desc</td>
          <td>1-1-1</td>
          <td>6-6-6</td>
        </tr>
        </tbody>
      </table>
    </div>

    <div role="tabpanel" class="tab-pane" id="add">
        <form class="form-horizontal" id="addEventForm">
            <div class="form-group">
                <label for="event-title" class="col-sm-2 control-label">Title</label>
                <div class="col-sm-10">
                    <input id="event-title" class="form-control" type="text" name="title" required />
                </div>
            </div>
            <div class="form-group">
                <label for="event-desc" class="col-sm-2 control-label">Description</label>
                <div class="col-sm-10">
                    <input id="event-desc" class="form-control" type="text" name="desc" required />
                </div>
            </div>
            <div class="form-group">
                <label for="event-type" class="col-sm-2 control-label">Type</label>
                <div class="col-sm-10">
                    <input id="event-type" class="form-control" type="text" name="type" required />
                </div>
            </div>

            <div class="form-group">
                <label for="event-start" class="col-sm-2 control-label">Start Time</label>
                <div class='input-group date col-sm-10'>
                    <input id="event-start" type='text' class="form-control" name="start" required />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="event-end" class="col-sm-2 control-label">End Time</label>
                <div class='input-group date col-sm-10'>
                    <input id="event-end" type='text' class="form-control" name="end" required />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
            <input type="submit"  name="submit" value="Submit" />

            <script type="text/javascript">
                $(function () {
                    $('.date').datetimepicker();
                });
            </script>
        </form>

        <form class="form-horizontal">
            <table class="table" id="option-table">
                <caption>Choices</caption>
                <thead><tr><th>Description</th> <th>Image</th></tr></thead>
                <tbody>
                <tr>
                  <td> <input class="form-control" type="text" name="choise-desc" required /> </td>
                  <td>Image</td>
                </tr>
                </tbody>
            </table>
        </form>
        <button type="button" class="btn btn-primary" id="more-option">add more option</button>
    </div>

  </div>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
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

$("#more-option").click(function() {
    var defaultrow = $(
            '<tr>' +
              '<td> <input class="form-control" type="text" name="choise-desc" required /> </td>' +
              '<td>Image</td>' +
            '</tr>');
    $("#option-table > tbody").append(defaultrow);
});

function loadEvents() {
    $.ajax({
        method: "POST",
        url: "../ajax/events.php",
        dataType: "json"
    })
    .done(function(eventList) {
        console.log(eventList);
        var tbody = $("#etable");
        eventList.forEach(function(e) {
            var newrow = "<tr><td>{0}</td><td>{1}</td><td>{2}</td><td>{3}</td></tr>".format(
                e.title, e.description, e.start_time, e.end_time);
            tbody.append(newrow);
        });
    })
    .fail(function( jqXHR, textStatus ) {
        console.log( "Request failed: " + textStatus );
    });
}
loadEvents();

$("#addEventForm").submit(function() {
    alert("submit");
    var epoch = function(datestr) {
            var pattern = /([0-9]{2})\/([0-9]{2})\/([0-9]{4}) ([0-9]){1,2}:([0-9]{2}) (AM|PM)/;
            var t = pattern.exec(datestr);
            var hour = parseInt(t[4]);
            if (t[6] == "PM") {
                    hour += 12;
            }
            return new Date(parseInt(t[3]), parseInt(t[1]), parseInt(t[2]),
                            hour, parseInt(t[5])).getTime() / 1000;
    }

    var formdata = {};
    var options = [];
    $("#addEventForm").serializeArray().map(function(x){formdata[x.name] = x.value;});
    formdata["start"] = epoch(formdata["start"]);
    formdata["end"] = epoch(formdata["end"]);
    var i = $("#option-table input");
    $("#option-table input").each(function(i) {
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
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.css" />
