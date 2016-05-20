<?php
require_once '../admin/vars.php';

$mongodb = $vars['mongodb']['wrapper']->getDatabase();
$accommodations = $mongodb->accommodations->find();
$types = $mongodb->types->find();
$path = $vars['unixdir'] . 'pic/cms';
?>

<h1>Niet gevonden accommodatie afbeeldingen</h1>
<table>
	<tr>
		<th>ID</th>
		<th>Afbeelding</th>
		<th></th>
	</tr>
	<?php foreach ($accommodations as $file) : ?>
	<?php if (file_exists($path . '/' . $file['directory'] . '/' . $file['filename'])) : ?>
		<?php continue; ?>
	<?php endif; ?>
	<tr>
		<td><?php echo $file['file_id']; ?></td>
		<td><?php echo $file['directory'] . '/' . $file['filename']; ?></td>
		<td><a href="<?php echo $vars['basehref']; ?>cms_accommodaties.php?edit=1&bc=5&wzt=1&archief=0&1k0=<?php echo $file['file_id']; ?>">Wijzigen</a></td>
	</tr>
	<?php endforeach; ?>
</table>

<h1>Niet gevonden type afbeeldingen</h1>
<table>
	<tr>
		<th>ID</th>
		<th>Afbeelding</th>
		<th></th>
	</tr>
	<?php foreach ($types as $file) : ?>
	<?php if (file_exists($path . '/' . $file['directory'] . '/' . $file['filename'])) : ?>
		<?php continue; ?>
	<?php endif; ?>
	<tr>
		<td><?php echo $file['file_id']; ?></td>
		<td><?php echo $file['directory'] . '/' . $file['filename']; ?></td>
		<td><a href="<?php echo $vars['basehref']; ?>cms_types.php?edit=2&bc=12&wzt=1&archief=0&2k0=<?php echo $file['file_id']; ?>">Wijzigen</a></td>
	</tr>
	<?php endforeach; ?>
</table>