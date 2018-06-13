<?php $this->layout('layout') ?>

<div class="wrapper">
    <section class="hero is-medium is-primary is-bold">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Самые минималистичные и элегантные обои для вашего рабочего стола!
                </h1>
                <h2 class="subtitle">
                    Настроение и вдохновение в одном кадре.
                </h2>
            </div>
        </div>
    </section>
    <section class="section main-content">
        <div class="container">
            <div class="columns  is-multiline">
                <?php foreach($images as $image) {?>
                    <div class="column is-one-quarter">
                        <div class="card">
                            <div class="card-image">
                                <figure class="image is-4by3">
                                    <a href="/images/<?= $image['id'];?>">
                                        <img src="<?= getImage($image['image'])?>">
                                    </a>
                                </figure>
                            </div>
                            <div class="card-content">
                                <div class="media">
                                    <div class="media-left">
                                        <p class="title is-5"><a href="/category/<?= $image['category_id'];?>"><?= getCategory($image['category_id'])['title']?></a></p>
                                    </div>
                                    <div class="media-right">
                                        <p  class="is-size-7">Размер: <?= $image['sizes'];?></p>
                                        <time datetime="2016-1-1" class="is-size-7">Добавлено: <?= uploadedDate($image['date']);?></time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
