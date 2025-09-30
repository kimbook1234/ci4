<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
		<?php if ($pager->hasPrevious()) : ?>
			<a href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>" style="margin:0 5px; text-decoration:none; color:#555;">&laquo;</a>
		<?php endif ?>

		<?php foreach ($pager->links() as $link) : ?>
			<?php if($link['active']):?>
                <span style="margin:0 5px; text-decoration:none; color:#000; font-weight:bold;"><?= $link['title'] ?></span>
            <?php else:?>
				<a href="<?= $link['uri'] ?>" style="margin:0 5px; text-decoration:none; color:#555;"><?= $link['title'] ?></a>
            <?php endif; ?>
		<?php endforeach ?>

		<?php if ($pager->hasNext()) : ?>
			<a href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>" style="margin:0 5px; text-decoration:none; color:#555;">&raquo;</a>
		<?php endif ?>
