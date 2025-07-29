<?php if ($pagination['total_pages'] > 1): ?>
<nav class="flex items-center justify-between" aria-label="Pagination">
    <div class="hidden sm:block">
        <p class="text-sm text-gray-700">
            نمایش
            <span class="font-medium"><?php echo (($pagination['current_page'] - 1) * $pagination['per_page']) + 1; ?></span>
            تا
            <span class="font-medium"><?php echo min($pagination['current_page'] * $pagination['per_page'], $pagination['total_items']); ?></span>
            از
            <span class="font-medium"><?php echo $pagination['total_items']; ?></span>
            نتیجه
        </p>
    </div>
    <div class="flex-1 flex justify-between sm:justify-end">
        <?php
            // Preserve existing query string parameters
            $queryParams = $_GET;
        ?>
        <?php if ($pagination['current_page'] > 1): ?>
            <?php $queryParams['page'] = $pagination['current_page'] - 1; ?>
            <a href="?<?php echo http_build_query($queryParams); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                قبلی
            </a>
        <?php endif; ?>

        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <?php $queryParams['page'] = $pagination['current_page'] + 1; ?>
            <a href="?<?php echo http_build_query($queryParams); ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                بعدی
            </a>
        <?php endif; ?>
    </div>
</nav>
<?php endif; ?>
