<?php

function db_thread($queries, $debug=0){
	if(count($queries)){
		$all_links = [];
		
		//Execute queries in parallel
		foreach($queries as $k=>$v){
			//Open a new connection for every query
			$link = mysqli_connect(CONFIG_DB_HOST, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);

			//Assign query to connection with async
			$link->query($v, MYSQLI_ASYNC);

			//Prepare mapping to identify original query order later
			$thread_db[$link->thread_id] = $k;

			$all_links[] = $link;
		}
		
		//Show debug information
		if($debug){
			print_r($thread_db);
			print_r($all_links);
		}

		$all_links_count = count($all_links);
		$processed = 0;
		do{
			$links = $errors = $reject = array();

			foreach($all_links as $link){
				$links[] = $errors[] = $reject[] = $link;
			}

			if(!mysqli_poll($links, $errors, $reject, 1)){
				continue;
			}
			
			//Fetch available results
			foreach($links as $link) {
				if($result = $link->reap_async_query()){
					while($row = $result -> fetch_assoc()){
						$rows[] = $row;
					}
					
					//Set results in original query order
					$thread_id = $thread_db[$link->thread_id];					
					$results[$thread_id] = $rows;
					
					//Free memory immediately
					unset($rows);
					
					if(is_object($result)){
						mysqli_free_result($result);
					}
				}else{
					die(sprintf("MySQLi Error: %s", mysqli_error($link)));
				}

				++$processed;
			}
		}while($processed < $all_links_count);

		return $results;
	}else{		
		return FALSE;
	}
}

?>
