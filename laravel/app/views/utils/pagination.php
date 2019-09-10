<?php
	$presenter = new Illuminate\Pagination\BootstrapPresenter($paginator);
?>

<?php if ($paginator->getLastPage() > 1): ?>
    <div class="row pagination-row">
        <div class="col-md-4">Showing <?php echo $paginator->getFrom(); ?>-<?php echo $paginator->getTo(); ?> of <?php echo $paginator->getTotal(); ?></div>
        <div class="col-md-8 text-right">
            <ul class="pagination  pagination-sm">
                <?php echo $presenter->render(); ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
