<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Moderation', 'totalcontest' ); ?>
        </label>
        <p>
            <label> <input type="radio" name="" ng-model="editor.settings.contest.submissions.requiresApproval" ng-value="false">
				<?php _e( 'All submissions are publicly visible.', 'totalcontest' ); ?>
                <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'All submissions will be approved automatically.', 'totalcontest' ); ?>">?</span>
            </label>
        </p>
        <p>
            <label> <input type="radio" name="" disabled>
				<?php _e( 'Only approved submissions are publicy visible.', 'totalcontest' ); ?>
                <?php TotalContest( 'upgrade-to-pro' ); ?>
                <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( 'All submissions will need an approval before going public.', 'totalcontest' ); ?>">?</span>
            </label>
        </p>
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Submission title', 'totalcontest' ); ?>
        </label>
        <input type="text" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.contest.submissions.title">
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Submission subtitle', 'totalcontest' ); ?>
        </label>
        <input type="text" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.contest.submissions.subtitle">
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Submission preview', 'totalcontest' ); ?>
        </label>
        <label>
            <input type="radio" name="" ng-model="editor.settings.contest.submissions.preview.source" value="">
			<?php _e( 'No preview', 'totalcontest' ); ?>
            &nbsp;
        </label>
        <label ng-repeat="field in editor.settings.contest.form.fields" ng-if="['image','video','audio','textarea'].indexOf(field.type) !== -1">
            <input type="radio" name="" ng-model="editor.settings.contest.submissions.preview.source" ng-value="field.name">
            {{field.label || '<?php _e( 'Untitled', 'totalcontest' ); ?>'}} ({{field.name}})
            &nbsp;
        </label>
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Submission preview fallback (No preview available)', 'totalcontest' ); ?>
        </label>
        <input type="text" class="totalcontest-settings-field-input widefat" placeholder="<?php esc_attr_e( 'URL', 'totalcontest' ); ?>" ng-model="editor.settings.contest.submissions.preview.default">
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Submission content', 'totalcontest' ); ?>
            <span class="totalcontest-feature-details" tooltip="<?php esc_attr_e( "This content will be shown in submission's page body.", 'totalcontest' ); ?>">?</span>
        </label>
        <progressive-textarea class="totalcontest-settings-field-input widefat" ng-model="editor.settings.contest.submissions.content"></progressive-textarea>
        <p class="totalcontest-settings-autocomplete">
            <strong><?php _e( 'Insert a form field', 'totalcontest' ); ?></strong>
            <a ng-click="editor.settings.contest.submissions.content = editor.settings.contest.submissions.content + (['image','audio','video'].indexOf(field.type) === -1 ? '\{\{fields.' + field.name + '\}\}' : '\{\{contents.' + field.name + '.content\}\}')"
               ng-repeat="field in editor.settings.contest.form.fields" ng-if="field.name">{{field.name}}</a>
        </p>
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Submissions per page', 'totalcontest' ); ?>
        </label>
        <input type="number" min="1" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.contest.submissions.perPage">
    </div>
</div>
<div class="totalcontest-settings-item">
    <p><strong><?php _e( 'Template variables', 'totalcontest' ); ?></strong></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{id}} for submission ID.', 'totalcontest' ); ?></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{fields.FIELD_NAME}} for form fields.', 'totalcontest' ); ?></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{user.PROPERTY_NAME}} for a property value in current user.', 'totalcontest' ); ?></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{date}} for submission date.', 'totalcontest' ); ?></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{views}} for submission views.', 'totalcontest' ); ?></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{votes}} for submission votes.', 'totalcontest' ); ?></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{rate}} for submission rate.', 'totalcontest' ); ?></p>
</div>
