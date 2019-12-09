<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label>
            <input type="checkbox" name="" ng-model="editor.settings.contest.limitations.period.enabled" ng-checked="editor.settings.contest.limitations.period.enabled">
			<?php _e( 'Time period', 'totalcontest' ); ?>
        </label>
    </div>
</div>
<div class="totalcontest-settings-item-advanced" ng-class="{active: editor.settings.contest.limitations.period.enabled}">
    <div class="totalcontest-settings-item totalcontest-settings-item-inline">
        <div class="totalcontest-settings-field">
            <label class="totalcontest-settings-field-label">
				<?php _e( 'Start date', 'totalcontest' ); ?>
                <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'Entries submission will be closed before reaching this date.', 'totalcontest' ); ?>">?</span>
            </label>
            <input type="text" datetime-picker min="0" step="1" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.contest.limitations.period.start">
        </div>

        <div class="totalcontest-settings-field">
            <label class="totalcontest-settings-field-label">
				<?php _e( 'End date', 'totalcontest' ); ?>
                <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'Entries submission will be closed after reaching this date.', 'totalcontest' ); ?>">?</span>
            </label>
            <input type="text" datetime-picker min="0" step="1" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.contest.limitations.period.end">
        </div>
    </div>
</div>
<!-- Membership -->
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label>
            <input type="checkbox" name="" disabled>
			<?php _e( 'Membership', 'totalcontest' ); ?>
            <?php TotalContest( 'upgrade-to-pro' ); ?>
        </label>
    </div>
</div>


<!-- Quota -->
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label>
            <input type="checkbox" name="" disabled>
			<?php _e( 'Quota', 'totalcontest' ); ?>
            <?php TotalContest( 'upgrade-to-pro' ); ?>
        </label>
    </div>
</div>
