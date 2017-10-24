<!DOCTYPE html>
<html>
	<head>
		<title>[ZCT][super-instrument.ru] Data Loader</title>
	</head>
	<body>
		<?php if($errors)echo "<nav>$errors</nav><hr>";?>
		<section>
			<form action="<?php echo $this->_base;?>/" method="post" enctype="multipart/form-data">
				<input type="hidden" name="MAX_FILE_SIZE" value="100000000">
				<table>
					<tr>
						<td>Токен:</td>
						<td><input type="password" name="key" value=""></td>
					</tr>
					<tr>
						<td>Файл (.xls/.xlsx/.ods/.zip с ними)</td>
						<td><input type="file" name="f"></td>
					</tr>
					<tr>
						<td colspan="2">Максимальный размер, разрешенный сервером: <?php if(false != ($mfs = @ini_get('upload_max_filesize')))echo $mfs;else echo '?';?><hr><input type="submit" value="Залить"></td>
					</tr>
				</table>
			</form>
		</section>
	</body>
</html>