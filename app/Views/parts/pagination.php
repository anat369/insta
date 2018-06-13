<nav class="pagination" role="navigation" aria-label="pagination">
    <a href="<?=$pagination->getPrevUrl()?>" class="pagination-previous" <?php if(!$pagination->getPrevUrl()):?> disabled <?php endif;?>>
        < Назад
    </a>
    <a href="<?=$pagination->getNextUrl()?>" class="pagination-next" <?php if(!$pagination->getNextUrl()):?> disabled <?php endif;?>>
        Вперед >
    </a>

    <ul class="pagination-list">
    <?php foreach ($pagination->getPages() as $page): ?>
        <?php if ($page['url']): ?>
            <li>
                <a class="pagination-link <?php echo $page['isCurrent'] ? 'is-current' : ''; ?>" href="<?php echo $page['url']; ?>"><?php echo $page['num']; ?></a>
            </li>
        <?php else: ?>
            <li class="pagination-link2"><span><?php echo $page['num']; ?></span></li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
</nav>