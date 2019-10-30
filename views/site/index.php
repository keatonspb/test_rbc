<?php

/* @var $this yii\web\View */

use yii\widgets\LinkPager;

$this->title = 'RBC news';
?>
<div class="site-index">

    <?php foreach ($articles as $article) { ?>
        <div>
            <h3><?= $article->title ?></h3>
            <p>
                <? $content = strip_tags(preg_replace('/\s+/', ' ', $article->content)); ?>
                <?= (strlen($content) <= 200) ? $content : mb_substr($content, 0, 200)."..."; ?>

            </p>
            <a href="/article/?id=<?= $article->id ?>">подробнее</a>
        </div>

    <?php } ?>
    <?= LinkPager::widget([
        'pagination' => $pages,
    ]); ?>
</div>
