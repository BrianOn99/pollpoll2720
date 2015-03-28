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
                <label for="event-type" class="col-sm-2 control-label">Start Time</label>
                <div class="col-sm-10">
                    <input id="event-type" class="form-control" type="text" name="type" required />
                </div>
            </div>
            <div class="form-group">
                <label for="event-start" class="col-sm-2 control-label">Start Time</label>
                <div class="col-sm-10">
                    <input id="event-start" class="form-control" type="text" name="start" required />
                </div>
            </div>
            <div class="form-group">
                <label for="event-end" class="col-sm-2 control-label">End Time</label>
                <div class="col-sm-10">
                    <input id="event-end" class="form-control" type="text" name="end" required />
                </div>
            </div>
            <input type="submit"  name="login" value="Log in" />
        </form>
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

function loadEvents() {
    $.ajax({
        method: "POST",
        url: "ajax/events.php",
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
    $.ajax({
        type: "POST",
        url: ajax/add_event.php,
        data: $("#addEventForm").serialize(),
        success: function(data) {
                alert(data);
        }
    });
    return false;
});
</script>
