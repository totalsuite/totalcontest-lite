<?php include $this->getPath( 'views/shared/header.php' ); ?>
    <div class="totalcontest-page-content <?php echo empty( $customPage['id'] ) ?: "totalcontest-page-content-{$customPage['id']}"; ?>">
		<?php echo empty( $customPage['content'] ) ? '' : $customPage['content']; ?>
    </div>
<?php include $this->getPath( 'views/shared/footer.php' );