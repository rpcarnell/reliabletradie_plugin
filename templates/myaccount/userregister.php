<div class="col-2">

		<h2><?php _e( 'Register', 'reliabletradie' ); ?></h2>
		<form method="post" class="register">

			 

				<p class="form-row form-row-first">
					<label for="reg_username"><?php _e( 'Username', 'reliabletradie' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" />
				</p>

				<p class="form-row form-row-last">

		 

				<p class="form-row form-row-wide">
 

				<label for="reg_email"><?php _e( 'Email', 'reliabletradie' ); ?> <span class="required">*</span></label>
				<input type="email" class="input-text" name="email" id="reg_email" value="<?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
			</p>

			<div class="clear"></div>

			<p class="form-row form-row-first">
				<label for="reg_password"><?php _e( 'Password', 'reliabletradie' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password" id="reg_password" value="<?php if (isset($_POST['password'])) echo esc_attr($_POST['password']); ?>" />
			</p>
			<p class="form-row form-row-last">
				<label for="reg_password2"><?php _e( 'Re-enter password', 'reliabletradie' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if (isset($_POST['password2'])) echo esc_attr($_POST['password2']); ?>" />
			</p>
			<div class="clear"></div>

			<!-- Spam Trap -->
			<div style="left:-999em; position:absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php   if (isset($_POST['tradie']) && $_POST['tradie'] ==1) 
                        {
                            do_action( 'register_form', true);
                            echo "<input type='hidden' name='usertype' value='tradie' />";
                        }
                        else
                        { echo "<input type='hidden' name='usertype' value='regular' />"; }
                        ?>

			<p class="form-row">
				<?php 
                                $reliableTradie->nonce_field( 'register' );
                                
                                //wp_nonce_field('reliabletradie-register', '_n', 'register', true);//$reliabletradie->nonce_field('register', 'register') ?>
				<input type="submit" class="button" name="reliable_register" value="<?php _e( 'Register', 'reliabletradie' ); ?>" />
			</p>

		</form>
<div id='previewSubrb'></div>
<div id='SubrbPrw'></div>
	</div>