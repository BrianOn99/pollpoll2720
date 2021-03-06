<h1>Manager</h1>
<p>Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.</p>
<a href="index.php?logout">Logout</a>

<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#list" aria-controls="list" role="tab" data-toggle="tab">Show</a></li>
    <li role="presentation"><a href="#add" aria-controls="add" role="tab" data-toggle="tab">Add</a></li>
    <li role="presentation" class="hidden" id="event-manage-tab"><a href="#manage" aria-controls="manage" role="tab" data-toggle="tab">Manage Event</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <!--
    List event interface
    -->
    <div role="tabpanel" class="tab-pane active" id="list">

      <table class="table table-striped" id="etable">
        <thead>
          <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Start</th>
            <th>End</th>
            <th>Details</th>
            <th>Activates</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>

    <!--
    Add event Interface
    -->
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
            <select id="event-type" class="form-control" name="type" required />
              <option>1</option>
              <option>2</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="event-start" class="col-sm-2 control-label">Start Time</label>
          <div class='input-group date col-sm-10'>
            <input id="event-start" class="form-control" type="text" name="start" required />
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
          </div>
        </div>
        <div class="form-group">
          <label for="event-end" class="col-sm-2 control-label">End Time</label>
          <div class='input-group date col-sm-10'>
            <input id="event-end" class="form-control" type="text" name="end" required />
            <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
          </div>
        </div>

        <div class="table" id="option-table">
          <h2>Choices</h2>
          <div id="choice-list">
            <div class="row"><span class="col-sm-6">Description</span><span class="col-sm-6">Image</span></div>
          </div>
        </div>
        <div>
        <button type="button" class="btn btn-primary" id="more-option">add more option</button>
        </div>

        <input type="submit"  name="submit" value="Submit" />
      </form>

        <script type="text/javascript">
            $(function () {
                $('.date').datetimepicker();
            });
        </script>

    </div>

    <div role="tabpanel" class="tab-pane" id="manage">

      <!--
      event management interface
      Okay, the nested div is quite horrifying
      I copy them from http://www.tutorialspoint.com/bootstrap/bootstrap_collapse_plugin.htm
      -->

      <h3>Choices</h3>
      <div id="choices-label">
      </div>

      <div class="panel-group" id="accordion">

        <!-- voter editor -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" 
                href="#collapseOne">
                Voters
              </a>
            </h4>
          </div>
          <div id="collapseOne" class="panel-collapse collapse in">
            <div class="panel-body">
              <button type="button" class="btn btn-default" id="help-voter-edit">
                <span class="glyphicon glyphicon-question-sign" aria-hidden="true"> </span> Help
              </button>
              <textarea class="form-control" id="voter-text" style="height: 20em">
              </textarea>
              <button type="button" class="btn btn-primary" id="voter-submit">commit</button>
              <button type="button" class="btn btn-primary" id="voter-export">export csv</button>
            </div>
          </div>
        </div>

        <!-- vote summary -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" 
                href="#collapseTwo">
                Vote Summary
              </a>
            </h4>
          </div>
          <div id="collapseTwo" class="panel-collapse collapse">
            <div class="panel-body">
                <button id="get-result">Get result</button>
                <div id="chartContainer" style="height: 300px; width: 100%;"> </div>
            </div>
          </div>
        </div>

        <!-- vote detail -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#accordion" 
                href="#collapseThree">
                Vote Detail
              </a>
            </h4>
          </div>
          <div id="collapseThree" class="panel-collapse collapse">
            <div class="panel-body">
              <button id="get-result-detail">Get result</button>
              <table class="table table-striped" id="result-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th id="voter-desc"></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
