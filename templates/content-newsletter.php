<?php
/**
 * Default output for a newsletter
 */

global $acb_newsletter, $ACBNEWS;
?>

<div id="dynamic-newsletter-wrapper">
	<table class="body" data-made-with-foundation="">
		<tr>
			<td class="float-center" align="center" valign="top">
				<center data-parsed="">
					<table class="spacer float-center">
						<tbody>
							<tr>
								<td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
							</tr>
						</tbody>
					</table>
					<table align="center" class="container float-center">
						<tbody>
							<tr>
								<td>
									<table class="spacer">
										<tbody>
											<tr>
												<td height="16px" style="font-size:16px;line-height:16px;">&#xA0;</td>
											</tr>
										</tbody>
									</table>
									<?php if(has_post_thumbnail($acb_newsletter->id)): ?>
										<table class="row">
											<tbody>
												<tr>
													<th class="small-12 large-12 columns first last">
														<table>
															<tr>
																<th>
																	<center data-parsed=""> <?php echo '<a href="' . get_permalink( $acb_newsletter->id ) . '" title="' . esc_attr( $acb_newsletter->get_the_title() ) . '">';
																		echo get_the_post_thumbnail($acb_newsletter->id, 'newsletter-hero', array('align'=>'center', 'class'=>'float-center'));
																	echo '</a>'; ?></center>
																</th>
																<th class="expander"></th>
															</tr>
														</table>
													</th>
												</tr>
											</tbody>
										</table>
									<?php endif; ?>
									<table class="row">
										<tbody>
											<tr>
												<th class="small-12 large-12 columns first last">
													<table>
														<tr>
															<th>
																<h2><?php $acb_newsletter->the_title(); ?></h2>
																<p><?php $acb_newsletter->the_short_description(); ?></p>
															</th>
														</tr>
													</table>
												</th>
											</tr>
										</tbody>
									</table>
									<table class="row">
										<tbody>
											<tr>
												<?php $i = 1; foreach($acb_newsletter->items as $item): ?>

												<th class="small-12 large-6 columns <?php echo $i % 2 == 0 ? 'last' : 'first'; ?>">
													<table>
														<tr>
															<th>
																<?php if (current_user_can('edit_post', $item->ID) && !$acb_newsletter->is_inlining()) edit_post_link( 'Edit', null, '<br />', $item->ID ); ?>
																<?php if(has_post_thumbnail($item->ID)): ?>
																	<div class="item-hero"><?php echo get_the_post_thumbnail( $item->ID, 'newsletter-hero' ); ?></div>
																<?php endif; ?>
																<h4><?php echo $item->post_title; ?></h4><?php echo wpautop(do_shortcode($item->post_content)); ?>
															</th>
														</tr>
													</table>
												</th>

												<?php $i++; endforeach; ?>

											</tr>
										</tbody>
									</table>
									<table class="row">
										<tbody>
											<tr>
												<th class="small-12 large-12 columns first last">
													<table>
														<tr>
															<th>
																<p class="text-center"><?php bloginfo('name'); ?> | <?php _e('&copy;', 'acb_nwsltr'); ?> <?php echo date('Y'); ?></p>
															</th>
															<th class="expander"></th>
														</tr>
													</table>
												</th>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</center>
			</td>
		</tr>
	</table>
</div>