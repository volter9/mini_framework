<article>
	<h2><?php echo $title ?></h2>
	
	<p>Привет! Вот пользователи этого сайта:</p>
	
	<ul>
		<?php if ($users): ?>
			<?php foreach ($users as $username => $user): ?>
			<li>
				<a href="<?php echo url('#show_user', $username) ?>">
					<?php echo $user['name'] ?>, возраст: <?php echo $user['age'] ?>
				</a>
			</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</article>
<!-- templates/default/html/users.php -->
