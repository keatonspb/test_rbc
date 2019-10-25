<?php
?>

<h1><?=$article->title ?></h1>
<?= $article->image_link ? "<img src='{$article->image_link}' />" : "" ?>
<div>
    <?= $article->content ?>
</div>
