<?php

	//array for suits
	$suits = ['C', 'H', 'S', 'D'];
	//array for cards
	$cards = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
	$counter=1;
    $menu=0;
	// build a deck (array) of cards
	function buildDeck($suits, $cards,) {
		$newDeck = array();
	  	foreach($suits as $suit){
	  		foreach($cards as $card){
	  			$newDeck[] = "$card $suit";
	  		}
	  		shuffle($newDeck);
	  		shuffle($newDeck);
	  		shuffle($newDeck);
	  	}
	  	return $newDeck;
	}
	function buildDecks($suits, $cards) {
		$newDeck1 = array();
		global $counter;
		for($i=$counter;$i>=0;$i--){
	  	foreach($suits as $suit){
	  		foreach($cards as $card){
	  			$newDeck1[] = "$card $suit";
	  		}
	  	}
		}
		shuffle($newDeck1);
		  return $newDeck1;
	}
	// Remove the suit from the card so that it may be evaluated
	function removeSuit($card) {
        $card = explode(' ', $card);
        array_pop($card);
        $card = implode(' ', $card);
      return $card;
    }
    // determine the value of an individual card (string)
	function getCardValue($card) {
        $card = removeSuit($card);
        if ($card == 'A'){
            $value = 11;
        } elseif ($card == 'J' || $card == 'Q' || $card == 'K') {
            $value = 10;
        } else {
            $value = $card;
        }
        return $value;
    }
    // get total value for a hand of cards
	function getHandTotal($hand) {
        $handTotal=0;
	  	foreach($hand as $eachCard){
	  		if(getCardValue($eachCard) == 11 && $handTotal >= 11){
	  			$handTotal += 1;
	  		} else {
	  			$handTotal += getCardValue($eachCard);
	  		}
	  	}
		return $handTotal;
	}
    // draw a card from the deck into a hand
	function drawCard(&$hand, &$deck) {
		$hand[] = $deck[0];
		array_shift($deck);
	}
    // print out a hand of cards
	function Hand($hand, $name, $hidden = false) {
        if($hidden){
			//display only one card for dealer 
            echo "$name: [$hand[0]] Total: ???\n";
        } else {
			//print out each card in players hand
            $cardsInHand = '';
            foreach($hand as $eachCard){
              
                $cardsInHand .= " [$eachCard]"; 
            }

          echo "$name:".$cardsInHand." Total: ".getHandTotal($hand)."\n";
      }
    }

    while($menu!=4){
        echo"21 Menu\n1\tStart Game\n2\tLeader board\n3\tReset Leader board\n4\tExit\n";
        $menu = readline('Enter (1,2,3,4): ');
        if($menu==1){
			echo "Please enter the amout of decks to be used (1-6)\n";
			$input = readline('Enter number between 1-6: ');
			while ($input>6||$input==0) {
			  	echo "Please enter the amout of decks to be used (1-6)\n";
			  	$input = readline('Enter number between 1-6: ');
			}
			$counter=$input-1;
			// build the deck of cards
			$deck = buildDecks($suits, $cards);
			//print_r($deck);
			$bank=20;
			$bet;
			while ($bank != 0) {
				$dealer = [];
				$player = [];	
				// dealer and player each draw two cards
				drawCard($player, $deck);
				drawCard($player, $deck);
				drawCard($dealer, $deck);
				drawCard($dealer, $deck);
				//player enters bet
				echo"You have: ".$bank." tokens\n";
				$bet = readline('Enter bet: ');
				while($bet>$bank||is_string($bank)==true){
					echo"You have: ".$bank." tokens (Please enter a valid amount)\n";
					$bet = readline('Enter bet: ');
				}	
				// echo both hands
				Hand($dealer, 'Dealer', true);
				Hand($player, 'Player');
			
				// allow player to hit or stay if hand is less than 21
				while (getHandTotal($player) < 21) {
					echo "(h)it or (s)tay?\n";
					$choice = readline();
					if ($choice == 'h'){
						drawCard($player, $deck);
						Hand($player, 'Player');
					  } elseif ($choice == 's'){break;}
				}
				//show dealers hand
				Hand($dealer, 'Dealer'); 
			
				// if more than 21 = bust 
				if(getHandTotal($player) > 21){
					echo "You Busted\n";
					$bank=$bank - $bet;
					$bet=0;
					echo"You have: ".$bank." tokens\n";
				//if 21= win
				} else if(getHandTotal($player) == 21){
					$bet*2;
					$bank=$bank + $bet;
					echo "21 You Won! ^_^\n";
				//dealer deals more cards if player hasnt lost or 21
				} else {
					while(getHandTotal($dealer) < 17){
						drawCard($dealer, $deck);
						Hand($dealer, 'Dealer');
					}
				}
				//if dealer more 21=win
				if(getHandTotal($dealer) > 21){
					echo "You win!\n";
					$bet*2;
					$bank=$bank + $bet;
					echo"You have: ".$bank." tokens\n";
				// if both equal hands draw"
				} elseif (getHandTotal($dealer) == getHandTotal($player)){
					echo "You have both Drew\n";
				// if dealer more than player = lost
				 } elseif (getHandTotal($dealer) > getHandTotal($player)){
					 echo "Dealer Won\n";
					 $bank=$bank - $bet;
					 echo"You have: ".$bank." tokens\n";
				//if player more than dealer = win
				 } elseif (getHandTotal($player)<=21){
					 echo "You Won\n";
					 $bet*2;
					 $bank=$bank + $bet;
					 echo"You have: ".$bank." tokens\n";
				 }
				 //check player has enought tokens to play
				 if($bank==0)
				 {
				   echo "You have no more tokens game has ended\n";
				 }
				 else {
				 //ask player to start next round
				 echo "Do you want to play again\n";
				 $again = readline('Enter (y)yes/(n)no: ');
				 while ($again!="y"&&$again!="n") {
					echo "Please enter a valid input\n";
					$again = readline('Enter (Y)yes/(N)no: ');
				  }
				// if no, ask user to input name for leaderboard
				  if($again=="n"){
					$username = readline('Enter Name: ');
					$myfile = fopen("leader.txt", "a") or die("Unable to open file!");
					$txt = $bank."\t".$username."\n";
					fwrite($myfile, $txt);
					fclose($myfile);
					$bank=0;
					$bet=0;
				  }
				  $bet=0;
				}
				}
        	}
        else if($menu==2){
			//call text file
        	$data = file('leader.txt');
			//sort score from high to low
	        natsort($data);
	        $data=array_reverse($data);
	        echo implode(PHP_EOL,$data); 
        }
        else if($menu==3){
			//reset leaderboard
			$deleteFileText = fopen ("leader.txt", "w+");
			fclose($deleteFileText);
        }

    }   


?>