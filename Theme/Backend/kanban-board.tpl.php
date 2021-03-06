<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Kanban
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

/** @var \Modules\Kanban\Models\KanbanBoard $board */
$board = $this->getData('board');

/** @var \Modules\Kanban\Models\KanbanColumn[] $columns */
$columns = $board->getColumns();
?>
<!--
@todo Orange-Management/Modules#197
    Columns width should be in % but with min-width and on smaller screens full width
    The amount of columns depends on the user settings
-->
<div class="row">
    <?php $i = 0; foreach ($columns as $column) : $i++; $cards = $column->getCards(); ?>
    <div id="kanban-column-<?= $i; ?>" class="col-xs-12 col-sm-3 box kanban-column" draggable="true">
        <header><?= $this->printHtml($column->name); ?></header>
        <?php $j = 0; foreach ($cards as $card) : $j++;
            $url = \phpOMS\Uri\UriFactory::build('{/prefix}kanban/card?{?}&id=' . $card->getId());
        ?>
            <section id="kanban-card-<?= $this->printHtml($i . '-' . $j); ?>" class="portlet" draggable="true">
                <div class="portlet-head">
                    <a href="<?= $url; ?>"><?= $this->printHtml($card->name); ?></a>
                    <div><span class="tag"><?= $card->getCommentCount(); ?></span></div>
                </div>
                <div class="portlet-body">
                    <article><?= $card->description; ?></article>
                </div>
                <div class="portlet-foot">
                    <div class="overflowfix">
                        <?php $tags = $card->getTags(); foreach ($tags as $tag) : ?>
                            <span class="tag" style="background: <?= $this->printHtml($tag->color); ?>"><?= $tag->icon !== null ? '<i class="' . $this->printHtml($tag->icon ?? '') . '"></i>' : ''; ?><?= $this->printHtml($tag->getL11n()); ?></span>
                        <?php endforeach; ?>
                        <a href="<?= $url; ?>" class="button floatRight"><?= $this->getHtml('More', '0', '0'); ?></a>
                    </div>
                </div>
            </section>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>
