<div style="text-align:center;">
	<img src="<?php echo sx('plugin_url').'images/Logo_MoreSharesForYou.png'; ?>" />
	<p><strong>Subscribe to our newsletter and get 25 points!</strong></p>
	<label for="sx_email">Your Email:</label>
	<input type="text" name="sx_email" /><br/>
	<a href="" id="sx_accept">ADD POINTS</a>
	<?php if ($subscribed != 'no'): ?>
		<a href="" id="sx_decline">Skip</a>
	<?php endif; ?>
</div>
