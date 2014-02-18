<?php
include('connection.php');
session_start();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<meta charset="utf-8"> 
	<meta name="author" content="Rodolphe Gagneron">
    <meta name="description" content="California SuperLotto Plus Lottery Simulator">
	<title>SuperLotto Plus Simulator</title>
	<link rel="stylesheet" type="text/css" href="lotto.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script>

		//google analytics, don't copy this code unless you want me to track you!
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-45691909-2', 'gagneron.com');
	  ga('send', 'pageview');

	</script>

</head>
<body>
	<div class='container'>
		<h1 class="tlt">California SuperLotto Plus Simulator</h1>
		<div>
			<table class='prize-structure styled'>
				<thead>
					<tr>
						<th>Prize Level</th>
						<th>Prize</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Match 0 plus Mega Ball</td>
						<td>$1</td>
					</tr>
					<tr>
						<td>Match 1 plus Mega Ball</td>
						<td>$2</td>
					</tr>
					<tr>
						<td>Match 2 plus Mega Ball</td>
						<td>$11</td>
					</tr>
					<tr>
						<td>Match 3</td>
						<td>$11</td>
					</tr>
					<tr>
						<td>Match 3 plus Mega Ball</td>
						<td>$55</td>
					</tr>
					<tr>
						<td>Match 4</td>
						<td>$100</td>
					</tr>
					<tr>
						<td>Match 4 plus Mega Ball</td>
						<td>$1,500</td>
					</tr>
					<tr>
						<td>Match 5</td>
						<td>$40,000</td>
					</tr>
					<tr>
						<td>Match 5 plus Mega Ball</td>
						<td>$7,000,000</td>
					</tr>
				</tbody>
			</table>
			<div class='overlay'>
				<div class='inner-overlay'>
					<p>These payouts are based on a rough average of the prize money distributed in a minimum $7 million jackpot.</p>
				</div>
			</div>
		</div>
		<div class='div-form'>
		
			<p>Pick 5 lucky numbers or...<button class='randomize'>randomize</button></p>

			<form method='post' action='process.php'>

				<input type='hidden' name='action' value='register'>
				<table class='numbers-table'>
					<?php for($row=0; $row<= 47; $row+= 10) { ?>
						<tr>

							<?php for($cell=1; $cell<=10; $cell++){
								if(($cell+$row)<=47){
							?>
							<td>
							<input name='lucky_numbers[]' class='regular-checkbox' id='<?= $row+$cell ?>' type='checkbox' value='<?= $row+$cell ?> '></input>
						<div class='label'><label for='<?= $row+$cell ?>'><?= $row+$cell ?></label></div>
							</td>

							<?php	} } ?>
						
						</tr>
					<?php } ?>
				</table>
					<section  id='mega-select'>
						<p>Pick a Mega Number</p>
						<select name='mega_number'>
							<?php for($number=1; $number<= 27; $number++) { ?>
							<option value='<?= $number ?>'><?= $number ?></option>
							<?php } ?>
						</select>
					</section>
					<section id='times-select'>
						<p>How many times do you want to play these numbers?</p>
						<select name='tickets'>
							<option value='1'>once</option>
							<option value='104'>104 times (twice a week for 1 year)</option>
							<option value='1040'>1,040 times (twice a week for 10 years)</option>
							<option value='5200' selected='selected'>5,200 times (twice a week for 50 years)</option>
							<option value='10000'>10,000 times (≈ 20 tickets a week for 10 years)</option>

						</select>
					</section>
		
				<input type='submit' value='submit' class='submit'>
				<div class='clear'></div>
			</form>
		</div><!-- end of .div-form -->
	<div class='clear'></div>

	<div>
		<?php 

			if(isset($_SESSION['errors']))
			{  
				echo "<p style='color:red;'>".$_SESSION['errors']."</p>";
			}
		 
		  if(isset($_SESSION['total_lotto_data'])){   

		  		if($_SESSION['dollars_spent'] != 1)
	  			{
	  				if(($_SESSION['max_prize']*$_SESSION['max_prize_freq']*2) < $_SESSION['dollars_spent'])
			  		{
		  				$only1 = 'only';

			  			switch($_SESSION['max_prize_freq'])
				  		{
				  			case 1: $times = 'once'; $only2 ='only'; break;
				  			case 2: $times = 'twice'; $only2 ='only'; break;
				  			default: $times = $_SESSION['max_prize_freq']." times"; $only2 =''; break;
				  		}	
			  		}
			  		else
			  		{
			  			$only1 = '';
			  			$only2 ='';
			  			$times = $_SESSION['max_prize_freq']." times";
			  		}

			  		echo "You just played ".$_SESSION['dollars_spent']. " times. The largest prize you ever won was ".$only1." $".$_SESSION['max_prize']." and you ".$only2." won this amount ".$times."!";
	  			}
	  			else
	  			{
	  				echo "Thanks for playing!";
	  			}
		  
		 ?>
		 <p>
		 	<?php 
		 		$query = fetch_all("SELECT SUM(prize_money), SUM(money_spent), SUM(max_prize) FROM results");
				$percentage = 100*$query[0]['SUM(prize_money)']/ $query[0]['SUM(money_spent)'];
				echo 'Out of the $'.$query[0]['SUM(money_spent)'].' spent on tickets, players have won back $'.$query[0]['SUM(prize_money)']." or ".round($percentage)."% of their money. <br>";

				if($query[0]['SUM(max_prize)'] == 0)
				{
					echo 'No one has won the jackpot yet!';
				}
				elseif ($query[0]['SUM(max_prize)'] == 1) {
					echo 'The jackpot has been won once!';
				}
				else{
					echo 'The jackpot has been won '.$query[0]['SUM(max_prize)'].' times!';
				}

				
		 	?>
		 </p>

			<table class='lotto-table styled'>
				<tr>
					<td>
						Your pick: 
						<?php 
							foreach($_SESSION['lucky_numbers'] as $one_lucky_number)
							{
								echo $one_lucky_number." ";
							}
						?>
						mega:
						<?php echo $_SESSION['mega_number']; ?>
					</td>
					<td>
						total prize money won:
						<?php echo "$".$_SESSION['total_prize']; ?>
					</td>
					<td>
						money spent:
						<?php echo "$".$_SESSION['dollars_spent']; ?>
					</td>
					<td>
						prizes won:
						<?php echo " ".$_SESSION['prizes_won']; ?>
					</td>
					
				</tr>
			<?php 

			 foreach($_SESSION['total_lotto_data'] as $one_draw_data) {


			 	if($one_draw_data['prize'] != 0)
			 	{
			 		echo "<tr class='winning-row'>";
			 	}
			 	else
			 	{
			 		echo "<tr class='row'>";
			 	}
			  ?>
				
	
					<td>
					Lotto numbers: 
					<?php foreach($one_draw_data['numbers_drawn'] as $one_number)
							{
								echo $one_number." ";
							}
							echo "Mega: ".$one_draw_data['mega_number'];
					?>
					</td>
					<td>
					Matching numbers: 
					<?php foreach($one_draw_data['matching_numbers'] as $one_number)
							{
								echo $one_number." ";
							}
						  if($_SESSION['mega_number'] == $one_draw_data['mega_number'])
						  {
						  	echo "<b>+ mega! </b>";
						  }
					?>
					</td>
					<td>
					how many balls correct:
					<?php 
						echo $one_draw_data['matching_numbers_count'];

						  if($_SESSION['mega_number'] == $one_draw_data['mega_number'])
						  {
						  	echo "<b> + mega</b>";
						  }
					 ?>
					</td>
					<td>
					prize:
					<?php 
						if($one_draw_data['prize'] == 0)
						{
							echo "$".$one_draw_data['prize'];
						}
						elseif($one_draw_data['prize'] == 7000000)
						{
							echo "<span class='grand-prize prize'>'$".$one_draw_data['prize']." HOLY $%#@ YOU WON!!!!</span>";
						}
						else
						{
							echo "<span class='small-prize prize'>$".$one_draw_data['prize']."</span>";
						}
					
					 ?>
					</td>
				</tr>
			<?php } ?>
			</table>
		<?php } 

		session_unset();

		?>
		</div>
	</div><!-- end of container -->

	
	<script type="text/javascript">

		$(document).ready(function(){


			$('.randomize').on('click', function(){

				for(var i = 1; i<=47; i++)
					{						
						$('#'+i).prop('checked', false);	
					}

				var arr = []
				while(arr.length < 5){
				  var randomnumber=Math.ceil(Math.random()*47)
				  var found=false;
				  for(var i=0;i<arr.length;i++){
				    if(arr[i]==randomnumber){found=true;break}
				  }
				  if(!found)arr[arr.length]=randomnumber;
				}
				for(var i = 0; i<5; i++)
				{
					$('#'+arr[i]).prop('checked', true);
				}		
			});

			 $('.overlay').mouseenter(function(){
		        $('.inner-overlay').fadeIn();
		        console.log('in');
		      }); 
		     
		      $('.inner-overlay').mouseleave(function(){
		        $(this).fadeOut();
		        console.log('out');
		      });
		});

	</script>

	
</body>
</html>