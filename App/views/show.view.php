<?php require 'partials/head.php'?>
		    <p class="card-text">On this day 
		    <text class="text-danger"><?= htmlspecialchars($_GET['value'], ENT_QUOTES, 'utf-8');?>
		    <?= htmlspecialchars($_GET['from'], ENT_QUOTES, 'utf-8');?></text>
			amounts to
			<text class="text-primary"><?= $converted . ' ' . htmlspecialchars($_GET['to'], ENT_QUOTES, 'utf-8');?></text>
			</p>	    
		  </div>
  		<div class="card-footer text-muted">
   		 <a href="/" class="btn btn-success">Back</a>
  		</div>
<?php require 'partials/footer.php'?>