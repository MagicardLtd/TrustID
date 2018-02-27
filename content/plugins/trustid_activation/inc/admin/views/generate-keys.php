<?php

  // Only allow users with set priveliges to generate keys
  if (current_user_can('edit_users')) {

    // Generate a custom nonce value.
		$trust_add_meta_nonce = wp_create_nonce( 'trust_add_user_meta_form_nonce' );

?>

    <?php // Build the Form ?>
    <h2>Generate TrustID v3 Keys</h2>
    <p>This form will generate a quantity of registration keys and store them in the key database.</p>

    <div class="generateKeysForm">
      <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="generateKeysForm" >
        <?php //	Default fields required by WordPress ?>
        <input type="hidden" name="action" value="trust_form_response">
  			<input type="hidden" name="trust_add_user_meta_nonce" value="<?php echo $trust_add_meta_nonce ?>" />
        <div class="fieldRow">
          <label for="<?php echo $this->plugin_name; ?>-qty"> <?php _e('Quantity of keys to generate', $this->plugin_name); ?> </label><br>
  				<input required id="<?php echo $this->plugin_name; ?>-qty" type="text" name="trust-qty" onkeyup="addInput('trust-qty',this.value);" /><br>
          <p class="hint"><small>How many keys to generate</small></p>
        </div>
        <div class="fieldRow">
          <label for="<?php echo $this->plugin_name; ?>-edition"><?php _e('Software edition', $this->plugin_name); ?></label>
          <select id="<?php echo $this->plugin_name; ?>-edition" name="edition" onchange="newEdition(this.value);">
            <option value="" selected  > </option>
            <option value="0" >Classic </option>
            <option value="1" >Premium</option>
            <option value="2" >Pro</option>
            <option value="3" >Pro Smart</option>
          </select>
          <p class="hint"><small>Which edition of the software</small></p>
        </div>
      </form>
    </div>

<?php
  }	else {
    echo '<p>You are not authorized to generate software keys.", $this->plugin_name)</p>';
  }
?>
