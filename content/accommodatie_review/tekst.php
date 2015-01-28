<?php
$get_translated_comment = ($vars["taal"] !== $value['tekst_language']);
if ($translation_available) : ?>
	<div class="comment_wrap">
		<div class="beoordeling_enquete_toelichting">
			<div class="beoordeling_enquete_toelichting_wrapper">
		

				<div class="review-comment">
					<?php echo wt_he_decode($value[$comment_column]); ?>
				</div>
				<?php if ($get_translated_comment): ?>
					<div class="original-review-comment" style="display: none;">
						<?php echo wt_he_decode($value["websitetekst_gewijzigd"]); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ($get_translated_comment): ?>
			<div class="subtext">
				<i>
					<span data-text-toggle="<?php echo html("original_review_shown", "beoordelingen") ?>">
						<?php echo html("review_is_translated", "beoordelingen") ?>
					</span>
				</i> |
				<a class="toggle-review-translation" data-text-toggle="<?php echo html("show_translated", "beoordelingen") ?>">
					<?php echo html("show_original", "beoordelingen") ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>