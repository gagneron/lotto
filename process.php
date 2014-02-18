<?php
session_start();
include_once('connection.php');

	if(isset($_POST['action']) && $_POST['action'] == 'register')
	{
		if(isset($_POST['lucky_numbers']) && isset($_POST['mega_number']) && isset($_POST['tickets'])) //if both lucky numbers and mega numbers are selected, proceed
		{
			if(count($_POST['lucky_numbers']) == 5)
			{

				$game = 'SuperLotto Plus';
				$tickets = $_POST['tickets'];

				if($game == 'SuperLotto Plus')
				{
					$number_list = range(1,47);
					$mega_list = range(1,27);
					$picks_quantity = 5;
					$prize_array = array();
				}

				for($i=0; $i<$tickets; $i++)
				{
					$returned_prize_array[] = play_lotto($number_list, $mega_list, $picks_quantity, $prize_array);
				}

				$_SESSION['max_prize'] = max($returned_prize_array);
				$counted = array_count_values($returned_prize_array);
				$_SESSION['max_prize_freq'] = $counted[$_SESSION['max_prize']];

				$max_prize = 0;
				if ($_SESSION['max_prize'] == 7000000)
				{
					$max_prize = $_SESSION['max_prize_freq'];
				}
				//insert final results into the database
				mysql_query("INSERT INTO  results (money_spent, prize_money, max_prize, created_at, updated_at) VALUES ( '{$_SESSION['dollars_spent']}', '{$_SESSION['total_prize']}', '{$max_prize}', NOW(), NOW())");
				
			}
			else
			{
				$_SESSION['errors'] = "You did not select 5 numbers, try again!";
			}
		}
		else
		{
			$_SESSION['errors'] = "Make sure you check off 5 numbers!";
		}	
	}


	function play_lotto($number_list, $mega_list, $picks_quantity, $prize_array)
	{
		
		//pick 5 INDEXES from an array of $number_list [1..47]. 
		$lotto_indexes = array_rand($number_list, $picks_quantity);

		foreach($lotto_indexes as $lotto_index)
		{
			//form an array of the lotto numbers
			$numbers_drawn_array[] = $number_list[$lotto_index];

		}

		//pick an index from an array of $mega_list [1..27]
		$mega_index = array_rand($mega_list, 1);
		//plug that index into the $mega_list to pick a mega number
		$mega_number = $mega_list[$mega_index];

		//set up an array of matching numbers
		$matching_numbers = array();

		//$_POST will be an array of the user's number picks
		foreach($_POST['lucky_numbers'] as $user_number)
		{		
			foreach($numbers_drawn_array as $lotto_number)
			{
				if($user_number == $lotto_number)
				{
					$matching_numbers[] = $user_number;
				}
			}
		} //need to transfer this to a hashtable 

		//determine prize money depending on count of matching numbers
		if($mega_number == $_POST['mega_number'])
		{
			switch(count($matching_numbers))
			{
				case 0: 
					$prize = 1; 
					break;
				case 1: 
					$prize = 2; 
					break;
				case 2: 
					$prize = 11; 
					break;
				case 3: 
					$prize = 55; 
					break;
				case 4: 
					$prize = 1500; 
					break;
				case 5: 
					$prize = 7000000; 
					break;
				default: 
					$prize = 0; 
					break;
			}	
		}
		else
		{
			switch(count($matching_numbers))
			{
				case 3: 
					$prize = 11; 
					break;
				case 4: 
					$prize = 100; 
					break;
				case 5: 
					$prize = 40000; 
					break;
				default: 
					$prize = 0; 
					break;
			}
		}


		$lotto_data_array['numbers_drawn'] = $numbers_drawn_array;
		$lotto_data_array['mega_number'] = $mega_number;
		$lotto_data_array['matching_numbers'] = $matching_numbers;
		$lotto_data_array['matching_numbers_count'] = count($matching_numbers);
		$lotto_data_array['prize'] = $prize;


		$_SESSION['total_lotto_data'][] = $lotto_data_array;
		$_SESSION['lucky_numbers'] = $_POST['lucky_numbers'];
		$_SESSION['mega_number'] = $_POST['mega_number'];

		
		//add the total prize money each time
		$_SESSION['total_prize'] += $prize;
		$_SESSION['dollars_spent'] += 1;

		if($prize != 0)
		{
			$_SESSION['prizes_won'] += 1;
		}
		
		return $prize;
	}

header('Location: index.php');
exit;

// Prize structure ==========================================================
// Match 5 plus Mega Ball	 $7,000,000	Rollover0
// Match 5	 $21,934	 2
// Match 4 plus Mega Ball	 $1,566	 14
// Match 4	 $112.00	 324
// Match 3 plus Mega Ball	 $58.00	 558
// Match 3	 $11.00	 13,976
// Match 2 plus Mega Ball	 $11.00	 7,898
// Match 1 plus Mega Ball	 $1.00	 42,775
// Match 0 plus Mega Ball	 $1.00	 71,898
	
	

?>