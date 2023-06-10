<?php 

date_default_timezone_set("Asia/Manila");

$server = "localhost";
$user = "root";
$pass = "";
$database = "php_calendar";

$db = mysqli_connect($server, $user, $pass, $database);

if (!$db) {
    die("<script>alert('Connection Failed.')</script>");
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.jqueryui.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    <title>Disable Date - PHP Calendar</title>
</head>
<body >
    <br>
    <!-- main content -->
    <div class="container">
        <div class="row">
           <div class="col-md-2">
               <a href="index.php" class="btn btn-default btn-sm">Back to Calendar</a>
           </div>
           <div class="col-md-1">
            <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#myModal">Add Disabled Date</button>

            <!-- Modal -->
            <div id="myModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <form method="POST">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Alter Calendar</h5>
                    </div>
                    <div class="modal-body">
                        <h6>Select a date your want to disable:</h6>
                        <input type="date" name="date_selected" min="<?php echo date('Y-m-d')?>" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="submit_date" class="btn btn-default" value="Add Record">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <?php 

        // adding disabled date
    if(isset($_POST['submit_date'])){
        $date = $_POST['date_selected'];
        $date_sel = $db->prepare("SELECT * FROM disabled_date WHERE disabled_date=?");
        $date_sel->bind_param('s', $date);
        $date_sel->execute();
        $date_sel_res = $date_sel->get_result();
        if(!$date_sel_res->num_rows > 0){
            $date_sel_in = $db->prepare("INSERT INTO disabled_date (disabled_date) VALUES (?)");
            $date_sel_in->bind_param('s', $date);
            $date_sel_in->execute();
            if($date_sel_in){
               echo "<script>
               alert('The information has been recorded successfully!');
               </script>";
           }
       }else{
           echo "<script>
           alert('This information has been on the record already!');
           </script>";
       }
   }
   ?>
</div>
</div>

<hr>
<div class="row">
   <div class="col-md-5">
    <?php
    // viewing disabled dates
    $msg = $db->prepare("SELECT * FROM disabled_date ");

    $msg->execute();
    $result = $msg->get_result();
    if ($result->num_rows  > 0) {?>
        <div class="row thumbnail">
            <div class="col-md-12 cur_class">
                <div class="table-responsive">    
                    <h6>Payment Records</h6>
                    <hr>
                    <table class="table table-responsive" id="disabled_dates">
                        <thead class="thead">
                            <tr >
                                <th>Selected Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="msg_table">
                            <?php
                            while($row = $result->fetch_assoc()){ ?>
                                <tr>
                                    <td><?php echo date('F d, Y', strtotime($row['disabled_date']));?></td>
                                    <td>
                                        <form method="POST" id="removeDate">
                                            <input type="hidden" name="date_selected" value="<?php echo $row['id']?>">
                                            <input type="submit" class="btn btn-danger btn-sm" value="Delete">
                                        </form>
                                    </td>
                                </tr>
                                <?php 
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }else {
        echo "No disabled dates to show.";
    }
    ?>
</div>
</div>
</div>

<?php 
// delete disabled date
if(isset($_POST['date_selected'])){
    $date_selected = $_POST['date_selected'];
    $remove = $db->prepare("DELETE FROM disabled_date WHERE id=?");
    $remove->bind_param('s', $date_selected);
    $remove->execute();
    if($remove){
        echo "<script>
        alert('The information has been removed successfully!');
        </script>";
    }
}

?>


<script>
    jQuery(document).ready(function($) {
        $('#disabled_dates').DataTable( {});
    });
</script>

</body>
</html>