<div class="wrap">
 
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    
    <form method="post" name="my-rdm-quotes_options" action="options.php">
    
		<?php settings_fields($this->plugin_name); ?>
 
        <!-- Optional title for quotes list -->
        <fieldset>
            <legend class="screen-reader-text"><span>Include title in quotes list.</span></legend>
            <label for="<?php echo $this->plugin_name; ?>-quo-title">
                <input type="checkbox" id="<?php echo $this->plugin_name; ?>-quo-title" name="<?php echo $this->plugin_name; ?>[quo-title]" value="1"/>
                <span><?php esc_attr_e('Include title in quotes list?', $this->plugin_name); ?></span>
            </label>
        </fieldset>
 
        <?php submit_button('Save all changes', 'primary','submit', TRUE); ?>
 
    </form>
 
</div>