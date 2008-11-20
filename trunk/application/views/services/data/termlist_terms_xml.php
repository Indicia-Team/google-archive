<?xml version='1.0' encoding='utf-8'?>
<terms>
<?php foreach($terms as $term) : ?>
	<term id="<?php echo $term->id; ?>">
		<value><?php echo $term->term; ?></value>
		<language><?php echo $term->language; ?></language>
	</term>
<?php endforeach; ?>
</terms>