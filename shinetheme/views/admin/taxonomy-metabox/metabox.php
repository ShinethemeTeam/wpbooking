<?php
if(empty($metabox['fields'])) return;
switch($layout_type) {
	case "edit_page":
		?>
		<table class="wpbooking-taxonomy-metabox form-table" id="wb-metabox-<?php echo esc_attr($metabox['id']) ?>">
			<tbody class="">
				<?php foreach ($metabox['fields'] as $metabox_field) {
					$metabox_field = wp_parse_args($metabox_field, array(
						'label' => FALSE,
						'desc'  => FALSE,
						'type'  => FALSE,
						'id'    => FALSE
					));
					if (empty($metabox_field['type'])) continue;

					?>
					<tr class="form-field wb-tax-field wb-field-type-<?php echo esc_attr($metabox_field['type']) ?> wb-field-<?php echo esc_attr($metabox_field['id']) ?>">
						<th scope="row">
							<?php if ($metabox_field['label']) {
								printf('<label for="wb-field-%s" class="wb-tax-field-title">%s</label>', $metabox_field['id'], $metabox_field['label']);
							} else echo "&nbsp;"; ?>
						</th>
						<td class="wb-tax-field-content">
							<?php echo wpbooking_admin_load_view('taxonomy-metabox/field-' . $metabox_field['type'], array('field' => $metabox_field, 'taxonomy' => $taxonomy)) ?>
						</td>
					</tr>
					<?php

				} ?>
			</tbody>
		</table>
		<?php
		break;
	case "add_page":
	default:
		?>
		<div class="wpbooking-taxonomy-metabox" id="wb-metabox-<?php echo esc_attr($metabox['id']) ?>">
			<?php foreach ($metabox['fields'] as $metabox_field) {
				$metabox_field = wp_parse_args($metabox_field, array(
					'label' => FALSE,
					'desc'  => FALSE,
					'type'  => FALSE,
					'id'    => FALSE
				));
				if (empty($metabox_field['type'])) continue;

				?>
				<div
					class="form-field wb-tax-field wb-field-type-<?php echo esc_attr($metabox_field['type']) ?> wb-field-<?php echo esc_attr($metabox_field['id']) ?>">
					<?php if ($metabox_field['label']) {
						printf('<label for="wb-field-%s" class="wb-tax-field-title">%s</label>', $metabox_field['id'], $metabox_field['label']);
					} ?>
					<div class="wb-tax-field-content">
						<?php echo wpbooking_admin_load_view('taxonomy-metabox/field-' . $metabox_field['type'], array('field' => $metabox_field, 'taxonomy' => $taxonomy)) ?>
					</div>
				</div>
				<?php

			} ?>
		</div>
		<?php
		break;
}