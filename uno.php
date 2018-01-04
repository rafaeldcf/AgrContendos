<div id="acme-extended-description" class="meta-options">
	<label for="acme-extended-description-field">
		Extended Description
	</label>
	<textarea name="acme-extended-description-field" id="acme-extended-description-field">
		<?php get_post_meta( $post->ID, 'acme_extended_description_field', true ); ?>
	</textarea>
</div>