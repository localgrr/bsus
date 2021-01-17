<?php 

add_filter('acf/pre_save_post' , 'my_pre_save_post', 10, 1 );

function my_pre_save_post( $post_id ) {

    $new_email = $_POST["new_email"];

    add_row("field_5ff64152ae29f", array("field_5ff6415fae2a0" => $new_email), $post_id);

    return;

}

acf_form_head(); 


if(isset($_POST["waiting_list_form_submitted"])) if($_POST["waiting_list_form_submitted"] == true) {

  echo '
  <script>
  jQuery(document).ready(function() {

      jQuery("#waiting-list-modal").modal({show: true})

  });
  </script>';

}


?>
<div class="modal fade" id="waiting-list-modal">
  <div class="modal-dialog">
    <div class="modal-content">

<?php
    $product_id = $_POST["product_id"];
    $title = $_POST["title"];
?>

      <!-- Modal Header -->
      <div class="modal-header">
      <h4>Join the waiting list for <?=$title; ?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
       </div>

      <!-- Modal body -->
      <div class="modal-body">


    <form name="waiting-list-form" action="" onsubmit="return validateWaitingListForm()" method="post">

        <?php acf_form([
        'post_id' => $product_id,
        'html_after_fields' => '<label for="new_email">Email:</label><br><input type="email" id="new_email" name="new_email" required />',
        'updated_message' => false,
        'form' => false
        ]); ?>

        <input type="hidden" name="waiting_list_product_id" id="waiting_list_product_id"/>

        <input type="submit" value="Submit" class="acf-button btn btn-primary">

    </form>

    <script>

    function validateWaitingListForm() {

        var new_email = jQuery("#new_email").val();

        var $fields = jQuery(".acf-table input[type='email']");

        var clean = true;

        $fields.each(function() {

            if(new_email.trim() == jQuery(this).val().trim()) {

                jQuery("#waiting-list-modal .modal-body .alert").remove();

                jQuery("#waiting-list-modal .modal-body").prepend('<div class="alert alert-danger" role="alert">Email already on list</div>');

                clean = false;

                return false;

            }

        });

        return clean;
    }


    </script>

      </div>
    </div>
  </div>
</div>