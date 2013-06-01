<?php
$con=mysqli_connect("localhost","root","","fireapp");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

if(isset($_GET["pre"])) {
  $state = $_GET["state"];
  $zip = $_GET["zip"];
}

if(isset($_POST["submitted"])) {

  $state = $_POST["state"];
  $zip = $_POST["zipcode"];

}

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Fire Visualization App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
      .navbar-inner {
        background: url('img/home_bg.jpg') top center repeat !important; color: #F0F0F0 !important;
      }
      .navbar-inner a { color: #FFF !important; }
      .navbar-inverse .nav .active > a, .navbar-inverse .nav .active > a:hover, .navbar-inverse .nav .active > a:focus, .navbar-inner .nav a:hover {
        background: url('img/header_hover.png') top center repeat !important;
      }
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <!-- <link rel="apple-touch-icon-precomposed" sizes="144x144" href="ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="ico/favicon.png"> -->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">FireApp</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="index.php">Browse</a></li>
              <li><a href="tips.php">Fire Prevention Tips</a></li>
              <li><a href="report.php">Report a Fire</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <form class="form-horizontal" action="index.php" method="POST">

      <div class="row-fluid">
        <div class="span5">
          <div class="control-group">
            <label class="control-label" for="inputEmail">Select State</label>
            <div class="controls">
              <select name="state" id="state" onchange="this.form.submit()">
                <option value="" selected="selected">All</option> 
                <?php
                  $query = "SELECT DISTINCT state FROM fires_table ORDER BY state ASC";

                   $result = mysqli_query($con,$query);

                  while($row = mysqli_fetch_array($result))
                    { ?>
                    <option value="<?php echo $row['state']; ?>"
                    <?php if($state == $row['state']) { echo " SELECTED"; } ?>
                    ><?php echo $row['state']; ?></option>
                    <?php } 
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="span3">
          <?php if(isset($state)) { ?>
          <div class="control-group">
            <label class="control-label" for="inputPassword">Select Zip Code(s)</label>
            <div class="controls">
              <select id="zipcode" name="zipcode[]" multiple>
                <option value="" selected="selected">All</option>
                <?php
                  $query = "SELECT DISTINCT zip FROM fires_table WHERE state = '" . $state . "' ORDER BY zip ASC";

                   $result = mysqli_query($con,$query);

                  while($row = mysqli_fetch_array($result))
                    { ?>
                    <option value="<?php echo $row['zip']; ?>"
                    <?php if($zip == $row['zip']) { } ?>
                    ><?php echo $row['zip']; ?></option>
                    <?php } 
                ?>
              </select>
            </div>
          </div>
          <?php } ?>
        </div>
        <div class="span3" id="submit_cell">
          <div class="control-group">
            <div class="controls">
              <input type="hidden" name="submitted" value="true" />
              <?php if(isset($_POST["submitted"]) || isset($_GET["pre"])) { ?><button type="submit" class="btn">Search</button><?php } ?>
            </div>
          </div>
        </div>
      </div>

      </form>

      <?php

      if(isset($zip) && $zip !== "") {

      ?>
      <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#summary" data-toggle="tab">Summary</a></li>
        <li><a href="#graphs" data-toggle="tab">Heatmap</a></li>
        <li><a href="#data" data-toggle="tab">Data</a></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="summary">
          ...
        </div>
        <div class="tab-pane" id="data">
          <table class="table table-striped">
            <thead>
                <tr>
                  <th>Zip Code</th>
                  <th>Total Fires</th>
                  <th>% Major</th>
                  <th>Intentional</th>
                  <th>Smoking</th>
                  <th>Heating</th>
                  <th>Cooking</th>
                </tr>
              </thead>
              <tbody>

      <?php

      if(isset($_GET["pre"])) {

        $query = "SELECT * FROM fires_table WHERE state LIKE '%" . $state . "%' AND zip = (" . $zip . ")";

      } else {
        $zip_arr = implode(',', $zip);
        $query = "SELECT * FROM fires_table WHERE state LIKE '%" . $state . "%' AND zip IN (" . $zip_arr . ")";

      }

      //var_dump($_POST);

      //echo "<br><br>" . $query;

       $result = mysqli_query($con,$query);

      while($row = mysqli_fetch_array($result))
        {
        echo "<tr>";
        echo "<td><a href='index.php?pre=true&state=" . $state . "&zip=" . $row['zip'] . "'>" . $row['zip'] . "</a></td>";
        echo "<td>" . $row['fires'] . "</td>";
        echo "<td>" . round($row['major_pct'],2) . "%</td>";
        echo "<td>" . round($row['cause_int_pct'],2) . "%</td>";
        echo "<td>" . round($row['cause_smoking_pct'],2) . "%</td>";
        echo "<td>" . round($row['cause_heating_pct'],2) . "%</td>";
        echo "<td>" . round($row['cause_cooking_pct'],2) . "%</td>";
        echo "</tr>";
        } 

      }

    ?>  </tbody>
      </table>

    </div>
        <div class="tab-pane" id="graphs">
          <?php if(isset($zip)) { ?>
          <iframe src="heatmap/demo/maps_heatmap_layer/gmaps.html" frameborder="0" width="100%" scrolling="no" height="400"></iframe>
          <?php } ?>
        </div>
    </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>

  </body>
</html>
<?php
  mysqli_close($con);
?>