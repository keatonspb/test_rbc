<?php

/* @var $this yii\web\View */

$this->title = 'RBC news';
?>
<div class="site-index">

    <?php foreach ($articles as $article) { ?>
        <div>
            <h3><?= $article->title ?></h3>
            <p>
                <?= mb_substr(strip_tags($article->content), 0, 200) . (count_chars($article->content) > 200 ? "..." : "") ?>
            </p>
            <a href="/article/?id=<?= $article->id ?>">подробнее</a>
        </div>
    <?php } ?>
</div>
