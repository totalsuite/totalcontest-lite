<div class="totalcontest-pro-badge-container">
    <div class="totalcontest-settings-item">
        <div class="totalcontest-settings-field">
            <label class="totalcontest-settings-field-label">
				<?php _e( 'Recipient email', 'totalcontest' ); ?>
            </label>
            <input type="text" class="totalcontest-settings-field-input widefat" disabled>
        </div>
    </div>
    <div class="totalcontest-settings-item">
        <p>
			<?php _e( 'Send notification when', 'totalcontest' ); ?>
        </p>
        <div class="totalcontest-settings-field">
            <label>
                <input type="checkbox" name="" disabled>
				<?php _e( 'New submission', 'totalcontest' ); ?>
            </label>
        </div>
    </div>
    <?php TotalContest( 'upgrade-to-pro' ); ?>
</div>
