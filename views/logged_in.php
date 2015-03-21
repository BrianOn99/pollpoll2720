<h1>Manager</h1>
<p>Hey, <?php echo $_SESSION['user_name']; ?>. You are logged in.</p>

<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="list" role="tab" data-toggle="tab">Show</a></li>
    <li role="presentation"><a href="#profile" aria-controls="add" role="tab" data-toggle="tab">Add</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="list">...</div>
    <div role="tabpanel" class="tab-pane" id="add">...</div>
  </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
$('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
})
</script>

<a href="index.php?logout">Logout</a>
