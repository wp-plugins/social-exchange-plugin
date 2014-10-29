<div style="text-align:center;">
	<img src="<?php echo sx('plugin_url').'images/Logo_MoreSharesForYou.png'; ?>" />
	<p style="font-size: 24px;font-weight: bold;"><strong>SEND ME 50 POINTS!</strong></p>
	<label for="sx_email">My best email:</label>
	<input type="text" name="sx_email" /><br/>
	<a href="" id="sx_accept">Let's do it</a> 
	<?php if ($subscribed != 'no'): ?>
		<a href="" id="sx_decline">Skip</a>
	<?php endif; ?>
</div>