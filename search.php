<?php	
	/*Get SQL Data*/
	function get_data($query, $params){
		
		if(isset($query)){
			//Build Query
			$sql = build_query($query, $params);
			
			//Connect to database and get results	
			$db = Database::getInstance();
			$mysqli = $db->getConnection();
			$result = $mysqli->query($sql);
			
			//get results
			if ($result->num_rows > 0) {			
				$data = $result->fetch_all( MYSQLI_ASSOC );
			}	
			else{
				$data = null;
			}	
		}
		
		return $data;
	}
	
	/*SQL Ingection Sanitisation*/
	function sanitize(){
		return trim(preg_replace('/[^-a-zA-Z0-9_]/', ' ', $_GET['q']));
	}
	
	/*build query*/
	function build_query($query, $params){	
		
		$condition 	= build_condition($query, $params['column']);
		
		/*$sql = 'SELECT *, jaro_winkler_similarity(`'. $params['sim_field'].'`, "'.$query.'") AS score
				FROM (SELECT '.$params['fields'].' FROM `'. $params['table'] .'` WHERE '.$condition.') AS likeMatches
				ORDER BY score DESC
				LIMIT '. $params['limit'];*/
		
		$sql = 'SELECT '.$params['fields'].' FROM `'. $params['table'] .'` WHERE '.$condition.'
				LIMIT '. $params['limit'];
		
		return $sql;
	}
	
	/*Set the Conditions*/
	function build_condition($query, $column){
		
		$like 	= explode(" ", $query);		
		$i 		= 0;
		
		foreach($column as $col){			
			if($i>0)
				$condition .= ' OR '. build_comparison($like, $column, $i);
			else
				$condition = build_comparison($like, $column, $i);
			$i++;
		}
		
		return $condition;
	}
	
	/*Set the Conditions Comparison Parameters*/
	function build_comparison($like, $column, $i){		
		
		$j		= 0;
		$cond 	= '';
			
		foreach ($like as $comp){			
			if($j>0)
				$cond .= ' OR ' . $column[$i] . ' LIKE "%'. $comp .'%"';
			else
				$cond = $column[$i] . ' LIKE "%'. $comp .'%"';
			$j++;
		}
		
		return $cond;
	}
?>
