<?php
?>

<h1><?=$article->title ?></h1>

<div class="row">
    <?php if ($article->image_link)  { ?>
        <div class="col-lg-4"><img class="thumbnail" style="width: 100%;" src='<?=$article->image_link?>' /></div>
        <div class="col-lg-8"><?= $article->content ?></div>
    <?php } else { ?>
    <div class="col-lg-12"><?= $article->content ?></div>
    <?php } ?>

</div>
