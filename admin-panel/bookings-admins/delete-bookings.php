<?php require "../../config/config.php"; ?>
<?php require "../../libs/App.php"; ?>
<?php require "../layouts/header.php"; ?>
<?php 


    if(!isset($_SERVER['HTTP_REFERER'])){
        // redirect them to your desired location
        echo "<script>window.location.href='".ADMINURL."'</script>";
        exit;
    }


?>
<?php 

    $app = new App;
    $app->validateSessionAdminInside();
    if(isset($_GET['id'])) {
        $id = $_GET['id'];

        $query = "DELETE FROM bookings WHERE id='$id'";
    
        $path = "show-bookings.php";
        $app->delete($query, $path);


    } 