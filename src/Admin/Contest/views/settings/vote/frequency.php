<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <p><?php _e( 'Block based on', 'totalcontest' ); ?> <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'The methods used to control rate of voting.', 'totalcontest' ); ?>">?</span></p>
        <div class="totalcontest-settings-field">
            <label> <input type="checkbox" name="" ng-model="editor.settings.vote.frequency.cookies.enabled">
				<?php _e( 'Cookies', 'totalcontest' ); ?>
            </label>
        </div>
        <div class="totalcontest-settings-field">
            <label> <input type="checkbox" name="" disabled>
				<?php _e( 'IP', 'totalcontest' ); ?>
                <?php TotalContest( 'upgrade-to-pro' ); ?>
            </label>
        </div>
        <div class="totalcontest-settings-field">
            <label> <input type="checkbox" name="" disabled>
				<?php _e( 'User', 'totalcontest' ); ?>
                <?php TotalContest( 'upgrade-to-pro' ); ?>
            </label>
        </div>
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Votes per user', 'totalcontest' ); ?>
            <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'Number of votes per each user.', 'totalcontest' ); ?>">?</span>
        </label>
        <input type="number" min="0" step="1" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.vote.frequency.count" ng-disabled="!(editor.settings.vote.frequency.cookies || editor.settings.vote.frequency.ip || editor.settings.vote.frequency.user)">
    </div>

    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Votes per submission', 'totalcontest' ); ?>
            <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'Number of allowed votes for the same submission.', 'totalcontest' ); ?>">?</span>
        </label>
        <input type="number" min="0" step="1" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.vote.frequency.perItem" ng-disabled="!(editor.settings.vote.frequency.cookies || editor.settings.vote.frequency.ip || editor.settings.vote.frequency.user)">
    </div>

    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Vote timeout', 'totalcontest' ); ?>
            <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'The period of time that a user must wait until he can vote again.', 'totalcontest' ); ?>">?</span>
        </label>
        <input type="number" min="0" step="1" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.vote.frequency.timeout" ng-disabled="!(editor.settings.vote.frequency.cookies || editor.settings.vote.frequency.ip || editor.settings.vote.frequency.user)">
        <p class="totalcontest-feature-tip">After this period, users will be able to vote again. To lock the vote permanently, use 0 as a value.</p>
    </div>
</div>
