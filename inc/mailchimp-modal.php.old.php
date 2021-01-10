<div class="modal fade" id="mailchimp-modal">
  <div class="modal-dialog">
    <div class="modal-content"> 

      <!-- Modal Header -->
      <div class="modal-header">
      <h4>Keep Updated</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">


      <form action="<?php echo site_url() ?>/wp-admin/admin-ajax.php" id="mailchimp">

        <div class="row">
          <div id="mailchimp-modal-messages"></div>
        </div>
        <div class="form-group row">
        
          <label for="email-address">Email address</label>
          <input type="email" class="form-control" id="email-address" name="EMAIL" placeholder="You email address" required="required">
        </div>

        <div class="form-group row">

<?php
      $args = array(

        'posts_per_page'   => -1,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'class',
        'post_status'      => 'publish'

      );

      $posts_array = get_posts( $args ); 
      foreach ($posts_array as $i => $p) { 
        if(! get_field("always_on",  $p->ID)) {
        ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="<?php echo $p->post_title; ?>" name="interest-array">
                <label class="form-check-label" for="specific-course">
                Email me when <strong><?php echo $p->post_title; ?></strong> is available
                </label>
            </div>
<?php
      }}
?>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" name="course-notify" id="any-course">
                <label class="form-check-label text-success" for="any-course">
                <strong>Just join the newsletter</strong>
                </label>
            </div>
        </div>
        <div class="form-group row">
          <input type="submit" class="btn btn-primary"/>
        </div>
        <input type="hidden" name="action" value="mailchimpsubscribe" />
        <input type="hidden" name="INTEREST" value="" />

      </form>

      </div>
    </div>
  </div>
</div>