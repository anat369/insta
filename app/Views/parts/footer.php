<footer class="section hero is-light">
    <div class="container">
        <div class="content has-text-centered">
            <div class="tabs">
                <ul>
                    <li><a href="/">Главная</a></li>
                    <?php foreach(getAllCategories() as $category):?>
                        <li><a href="/category/<?= $category['id'];?>"><?= $category['title'];?></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
            <p>
                <strong><a href="http://myorient.ru/" target="_blank">Myorient.ru</a></strong> - блог о всякой всячине.
            </p>
            <p class="is-size-7">
                Все права защищены. <?= date('Y') . 'г.'?>
            </p>
        </div>
    </div>
</footer>
