		<div class="main">
		<div id="loginFormHolder" class="loginFormHolder">
		<?php if (isset($_GET["err"])) : ?>
			<div class="error">
				Username or password are incorrect.
			</div>
		<?php endif; ?>
			<form action="TDLLoggingin.php" method="post">
				<div class="inputHolder">
					<label for="username">Username: (required)</label>
					<input type="text" name="username" />
				</div>
				<div class="inputHolder">
					<label for="password">Password: (required)</label>
					<input type="password" name="password" />
				</div>
				<input type="hidden" name="lock" value="<?php echo SID; ?>" />
				<input type="text" class="hidden" name="botBlocker" />
				<div class="inputHolder">
					<input type="submit" value="Log in" />
				</div>
			</form>
		</div>
		</div>