<?php require 'partials/head.php'?>
<p class="card-text">
	<form action="/converted" method="GET" >
		<div class="form-group d-flex justify-content-center">
		<input type="number" name="value" id="value" class="form-control" style="width:80px!important;" >
		<select name="from" id="from" class="custom-select" style="width:80px">
			<!-- Foreach loop to loop through all the currencies -->
			<?php foreach ($currencies as $currency): ?>			
				<option value=<?= $currency;?>> <?= $currency;?> </option>
			<?php endforeach; ?> 
		</select>
		<select name="to" id="to" class="custom-select" style="width:80px">			
			<?php foreach ($currencies as $currency): ?>
				<option value=<?= $currency;?>> <?= $currency;?> </option>
			<?php endforeach; ?> 
		</select>
	<button value="submit" class="btn btn-success ml-1">Convert</button>
	</div>
	</form>
</p>
<?php require 'partials/footer.php'?>