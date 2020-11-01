<div class="col-1">

 

		<h2><?php _e( 'Login', 'reliabletradie' ); ?></h2> 
		<form method="post" class="login">
			<p class="form-row form-row-first">
				<label for="username"><?php _e( 'Username or email', 'reliabletradie' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="username" />
			</p>
			<p class="form-row form-row-last">
				<label for="password"><?php _e( 'Password', 'reliabletradie' ); ?> <span class="required">*</span></label>
				<input class="input-text" type="password" name="password" id="password" />
			</p>
			<div class="clear"></div>

			<p class="form-row">
				<?php $reliableTradie->nonce_field('login', 'login') ?>
				<input type="submit" class="button" name="reliable_login" value="<?php _e( 'Login', 'reliabletradie' ); ?>" />
                                <br /><br /><a class="lost_password" href="<?php

				$lost_password_page_id = reliabletradie_get_page_id( 'lost_password' );

				if ( $lost_password_page_id )
					echo esc_url( get_permalink( $lost_password_page_id ) );
				else
					echo esc_url( wp_lostpassword_url( home_url() ) );

				?>"><?php _e( 'Lost Password?', 'reliabletradie' ); ?></a>
			</p>
                        <p>
                            
                                <?php
                                echo "<p><a href='".esc_url('index.php?register=1&page_id='.$my_account_id)."'>Register</a></p>";
                                ?>
                        </p>
		</form>

         

	</div>