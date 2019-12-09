<?php
include $this->getPath( 'views/shared/header.php' );
?>
    <div class="totalcontest-page-content">
		<?php echo empty( $content ) ? __( 'Thank you!', 'totalcontest' ) : $content; ?>
        <a href="<?php echo esc_attr( $submission->getPermalink() ); ?>">&larr;&nbsp;<?php _e( 'Back', 'totalcontest' ); ?></a>
    </div>
<?php
include $this->getPath( 'views/shared/footer.php' );