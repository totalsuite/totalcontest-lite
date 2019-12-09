<div class="totalcontest-menu" totalcontest-mobile-scrollable>
	<?php
	$menuItems = $contest->getMenuItems();
	foreach ( $menuItems as $menuItem ): ?>
        <a class="totalcontest-menu-item <?php echo $menuItem['active'] ? 'totalcontest-menu-item-active' : ''; ?>" href="<?php echo esc_attr( $menuItem['url'] ); ?>" totalcontest-ajax-url="<?php echo esc_attr( $menuItem['ajax'] ); ?>"><?php echo esc_html( $menuItem['label'] ); ?></a>
	<?php endforeach; ?>
</div>