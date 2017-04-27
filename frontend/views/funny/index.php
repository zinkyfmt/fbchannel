<?php
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <?php for($i = 0; $i < count($graph_array); $i++ ) { ?>
        <h2><?php echo $graph_array[$i]['description'] ?></h2>
        <div style="margin: 10px" class="block-video"><?php echo $graph_array[$i]['embed_html'];?></div>
    <?php } ?>
</div>
