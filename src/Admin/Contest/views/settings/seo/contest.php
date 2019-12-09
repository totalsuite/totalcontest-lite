<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Title', 'totalcontest' ); ?>
        </label>
        <input type="text" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.seo.contest.title">
    </div>
</div>
<div class="totalcontest-settings-item">
    <div class="totalcontest-settings-field">
        <label class="totalcontest-settings-field-label">
			<?php _e( 'Description', 'totalcontest' ); ?>
        </label>
        <textarea rows="3" class="totalcontest-settings-field-input widefat" ng-model="editor.settings.seo.contest.description" maxlength="150"></textarea>
        <p class="totalcontest-feature-tip">{{150 - editor.settings.seo.contest.description.length}} <?php _e( 'Characters left.', 'totalcontest' ); ?></p>
    </div>
</div>
<div class="totalcontest-settings-item">
    <p><strong><?php _e( 'Template variables', 'totalcontest' ); ?></strong></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{title}} for contest title.', 'totalcontest' ); ?></p>
    <p class="totalcontest-feature-tip" ng-non-bindable><?php _e( '{{sitename}} for website title.', 'totalcontest' ); ?></p>
</div>