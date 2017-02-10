<div class="sk-elnat-operation-messages">
	<hr>
	<?php foreach ( $messages as $message ) : ?>
		<div
			class="sk-elnat-operation-message<?php echo ! empty( $message['icon'] ) ? ' ' . $message['icon'] : null; ?>">
			<div class="sk-elnat-operation-message-desc">
				<h3><?php material_icon( $message['icon'], array( 'size' => '1.5em' ) ); ?><?php echo ! empty( $message['desc'] ) ? $message['desc'] : null; ?></h3>
			</div>
			<div
				class="sk-elnat-operation-message-start"><?php echo ! empty( $message['start'] ) ? $message['start'] : null; ?></div>
			<div
				class="sk-elnat-operation-message-end"><?php echo ! empty( $message['end'] ) ? $message['end'] : null; ?></div>
		</div>
		<hr>
	<?php endforeach; ?>

</div>

