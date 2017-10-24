<?php if ($reviews) { ?>
	<?php foreach ($reviews as $review) { ?>
		<table class="review_list table table-striped table-bordered">
			<tr>
				<td style="width:30%;"><i class="fa fa-user" aria-hidden="true"></i><span><?php echo $review['author']; ?></span></td>
				<td style="width:30%;">
					<div class="rating">
						<?php for ($i = 1; $i <= 5; $i++) { ?>
							<?php if ($review['rating'] < $i) { ?>
								<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
							<?php } else { ?>
								<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i></span>
							<?php } ?>
						<?php } ?>
					</div>
				</td>
				<td class="text-right"><?php echo $review['date_added']; ?></td>
			</tr>
			<tr>
				<td colspan="3">
					<?php if($show_plus_minus_review && $review['plus']) { ?>
						<div class="plus">
							<div><i class="fa fa-plus" aria-hidden="true"></i><?php echo $text_plus; ?></div>
							<?php echo $review['plus']; ?>
						</div>
					<?php } ?>
					<?php if($show_plus_minus_review && $review['minus']) { ?>
						<div class="minus">
							<div><i class="fa fa-minus" aria-hidden="true"></i><?php echo $text_minus; ?></div>
							<?php echo $review['minus']; ?>
						</div>
					<?php } ?>
					<div class="comment">
						<?php if($show_plus_minus_review && $review['plus'] || $review['minus']) { ?><div><i class="fa fa-comment" aria-hidden="true"></i><?php echo $text_comment; ?></div><?php } ?>
						<?php echo $review['text']; ?>
					</div>
					<?php if (isset($review['admin_reply']) && $review['admin_reply'] != '') { ?> 
						<div class="admin_reply">
							<div><i class="fa fa-reply" aria-hidden="true"></i><span><?php echo $text_admin_reply; ?></span></div>
							<?php echo $review['admin_reply']; ?>
						</div>
					<?php } ?>
				</td>
			</tr>
		</table>
	<?php } ?>
	<div class="text-right"><?php echo $pagination; ?></div>
<?php } else { ?>
	<p><?php echo $text_no_reviews; ?></p>
<?php } ?>
