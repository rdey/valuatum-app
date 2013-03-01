<?php

require_once "ExpressionNode.php";
	

/**
 * This class is responsible for parsing expression trees from where clauses.
 *
 * @since 1.0
 * @author 
 */
class ExpressionTree
{
    /**
     * The expression tree root.
     *
     * @var ExpressionTree
     */
	protected $root = false;


    /**
     * Parses 'where clause' expression.
     *
     * @param expr
     * @return true/false
     */
	public function parse($expr)
	{
		// evaluate expression
		$eval = $this->evaluate($expr);
		if (is_bool($eval))
			return false;				
		
		// build tree
		$this->root = $this->buildTree($eval, $expr);
		if (is_bool($this->root))
			return false;
		
		return true;
	}
	
	
    /**
     * Evaluate expression.
     *
     * @param expr
     * @return resulted expression
     */
	protected function evaluate($expr)
	{
		$eval 		= array();
		$pos  		= 0;		
		$brackets	= 0;
		$start		= 0;
		$end		= 0;
		$lenExpr	= strlen($expr);
		
		// traverse string
		while(1) {
			if ($pos >= $lenExpr)
				break;
			
			if ($brackets < 0)
				return false;
				
			// opened bracket
			if ($expr[$pos] == "(")	{
				$brackets++;
				$start = $pos + 1;
				$pos++;
				continue;
			}
			else
			// closed bracket
			if ($expr[$pos] == ")")	{
				$brackets--;
				$end = $pos;
				
				// last end
				$no = count( $eval );
				if ($no > 0 && $eval[$no-1]["end"] < 0)	{
					$eval[$no-1]["end"] = $pos;
				}
				
				$pos++;
				continue;
			}
			
			$opLen 	= 0;
			$opType	= "";
			$opRank	= 0;
			
			// AND
			if (0 == strcasecmp(substr($expr,$pos,5), " AND ") || 0 == strcasecmp(substr($expr, $pos, 5), ")AND(") ||
				0 == strcasecmp(substr($expr,$pos,5), ")AND ") || 0 == strcasecmp(substr($expr, $pos, 5), " AND(")) {
				$opLen 	= 5;
				$opType = "AND";
				$opRank	= $brackets;		
				$start 	= $pos;			
				$end 	= $pos + 5;					
			}
			elseif (0 == strcasecmp(substr( $expr, $pos, 4 ), " OR ") || 0 == strcasecmp(substr( $expr, $pos, 4), ")OR(") ||
					0 == strcasecmp(substr( $expr, $pos, 4 ), ")OR ") || 0 == strcasecmp(substr( $expr, $pos, 4), " OR(")) {
				// OR			
				$opLen 	= 4;
				$opType = "OR";
				$opRank	= $brackets;			
				$start 	= $pos;		
				$end 	= $pos + 4;						
			}
			elseif( $expr[$pos] == '=' ) {
				// =			
				$opLen 	= 1;
				$opType = "=";
				$opRank	= $brackets + 1;
				$end 	= -1;
			}
			else {
			// move forward			
				$pos++;
				continue;
			}
			
			// last end
			$no = count( $eval );
			if ($no > 0 && $eval[$no-1]["end"] < 0) {
				$eval[$no-1]["end"] = $pos;
			}
			
			// add operator
			array_push($eval, array("len" => $opLen, "type" => $opType, "pos" => $pos, "rank" => $opRank, "start" => $start, "end" => $end));
			$pos += $opLen;
			
			if ($end != -1)
				$start = $end;
		}

		// last end
		$no = count($eval);
		if ($no > 0 && $eval[$no-1]["end"] < 0) {
			$eval[$no-1]["end"] = $lenExpr;
		}

		return $eval;
	}
	
	
    /**
     * Build expression tree.
     *
     * @param eval
     * @param expr	 
     * @return tree node
     */
	protected function buildTree($eval, $expr)
	{
		$no = count($eval);

		// no operator
		if ($no <= 0) {
			return false;
		}
			
		// only one operator				
		if ($no == 1) {
			// leaf must be =
			if ($eval[0]["type"] != "=" || $eval[0]["start"] >= $eval[0]["end"]) {
				return false;
			}
		
			$start  = $eval[0]["start"];
			$end	= $eval[0]["end"];
			$pos	= $eval[0]["pos"];
			$opLen	= $eval[0]["len"];
			
			// get leaf info
			$name	= trim( substr( $expr, $start, $pos - $start ) );
			$value	= trim( substr( $expr, $pos + $opLen, $end - $pos - $opLen ) );
		
			// add leaf
			$node = new ExpressionNode($name, $value, $eval[0]["type"]);
			return $node;
		}
		
		$minRank = -1;
		$minIdx	 = -1;
		
		// search min rank
		for ($i = 0; $i < $no; $i++) {
			if ($minRank == -1 || $eval[$i]["rank"] < $minRank) {
				$minRank = $eval[$i]["rank"];
				$minIdx	 = $i;
			}
		}
			
		if( $minIdx <= 0 || $minIdx >= $no )
			return false;
			
		// allocate parent
		$node = new ExpressionNode("", "", $eval[$minIdx]["type"]);
			
		// build left sub-tree
		$leftNode = $this->buildTree(array_slice($eval, 0, $minIdx), $expr);
		if (is_bool($leftNode)) {
			return false;		
		}
		
		$node->setLeftNode($leftNode);
		
		// build right sub-tree
		$rightNode = $this->buildTree(array_slice($eval, $minIdx + 1), $expr);
		if (is_bool($rightNode)) {
			return false;
		}
		
		$node->setRightNode($rightNode);
		
		return $node;
	}
	
	
    /**
     * Validate fields.
     *
     * @param tableList
     * @param protocol	
     * @param isSet	 	 
     * @param errorField
     * @return true/false
     */
	public function validateFields($tableList, $protocol, $isSet, &$errorField)
	{
		return $this->validateSubTreeFields($this->root, $tableList, $protocol, $isSet, $errorField);
	}
	
	
    /**
     * Validate fields in sub-tree.
     *
     * @param node	 
     * @param tableList
     * @param protocol	
     * @param isSet
     * @param errorField
     * @return true/false
     */
	public function validateSubTreeFields($node, $tableList, $protocol, $isSet, &$errorField)
	{
		if (!$node) {
			return false;
		}
	
		// node terminal
		if ($node->isTerminal()) {
			$field = $node->getName();
			
			if ($isSet)	{
				if (!$this->validateSetFieldName($tableList, $protocol, $field))
				{
					$errorField = $field;
					return false;
				}
			} else {				
				if (!$this->validateGetFieldName($tableList, $protocol, $field)) {
					$errorField = $field;
					return false;
				}
			}

			return true;
		}
		
		// validate left leaf
		if (!$this->outputSubTreeDebug($node->getLeftNode())) {
			return false;
		}
		
		// output right leaf
		if (!$this->outputSubTreeDebug($node->getRightNode())) {
			return false;
		}
		
		return true;
	}
	

    /**
     * Validate get field name.
     *
     * @param tableList
     * @param protocol	
     * @param field
     * @return true/false
     */	
	protected function validateGetFieldName($tableList, $protocol, $field)
	{
		if ($field == "*") {
			return true;
		}
		
		// traverse tables
		$no = count($tableList);
		for ($i = 0; $i < $no; $i++) {
			$testField = $field;
			
			// all table
			if ($tableList[$i]["alias"] == $testField) {
				return true;
			}
			
			// remove alias
			if ($tableList[$i]["alias"] != "") {
				$len = strlen($tableList[$i]["alias"]);
				if (0 == strcmp(substr($testField, 0, $len), $tableList[$i]["alias"] ) && $testField[$len] == '.') {
					$testField = substr( $testField, $len + 1 );
				}
			}
			
			if ($protocol->isTableField($tableList[$i]["name"], $testField)) {
				return true;
			}
		}
		
		return false;
	}
	

    /**
     * Validate set field name.
     *
     * @param tableList
     * @param protocol	
     * @param field
     * @return true/false
     */
	protected function validateSetFieldName($tableList, $protocol, $field)
	{
		// traverse tables
		$no = count($tableList);
		for( $i = 0; $i < $no; $i++ ) {
			$testField = $field;
			
			// remove alias
			if ($tableList[$i]["alias"] != "") {
				$len = strlen($tableList[$i]["alias"]);
				if (0 == strcmp(substr($testField, 0, $len), $tableList[$i]["alias"] ) && $testField[$len] == '.') {
					$testField = substr($testField, $len + 1);
				}
			}
			
			if ($protocol->isTableField($tableList[$i]["name"], $testField)) {
				return true;
			}
		}
		
		return false;
	}
	

    /**
     * Validate field type.
     *
     * @param tableList
     * @param protocol	
     * @param field
     * @param structName
     * @return true/false
     */	
	protected function getFieldType($tableList, $protocol, $field, &$structName, &$structCount)
	{
		// traverse tables
		$no = count($tableList);
		for ($i = 0; $i < $no; $i++) {
			$testField = $field;
			
			// remove alias
			if ($tableList[$i]["alias"] != "") {
				$len = strlen($tableList[$i]["alias"]);
				if (0 == strcmp(substr($testField, 0, $len), $tableList[$i]["alias"] ) && $testField[$len] == '.') {
					$testField = substr($testField, $len + 1);
				}
			}
			
			if ($protocol->getTableFieldType($tableList[$i]["name"], $testField, $structName, $structCount)) {
				return true;
			}
		}
		
		return false;
	}
	
	
    /**
     * Output tree.
     *
     * @param tableList	 
     * @param protocol
     * @param paramList
     * @return result
     */
	public function output($tableList, $protocol, &$paramList)
	{
		if (!$this->root) {
			return true;
		}
	
		return $this->outputSubTree($this->root, $tableList, $protocol, $paramList);
	}
	
	
    /**
     * Output sub-tree.
     *
     * @param node	 
     * @param tableList
     * @param protocol
     * @param paramList
     * @return result
     */
	public function outputSubTree($node, $tableList, $protocol, &$paramList)
	{
		if (!$node) {
			return true;
		}
	
		// node terminal
		if ($node->isTerminal()) {
			$field = $node->getStrippedName();
			$value = $node->getValue();
			
			// get field type
			if (!$this->getFieldType($tableList, $protocol, $node->getName(), $structName, $structCount)) {
				return false;
			}
			
			// unnamed struct
			if ($structName == "" ) {
				// search struct
				$no = count($paramList);
				for ($i = 0; $i < $no; $i++) {
					if (is_array($paramList[$i])) {
						if (isset($paramList[$i][$field])) {
							if (is_array($paramList[$i][$field])) {
								array_push($paramList[$i][$field], $value);
							}else {
								$paramList[$i][$field]=array($paramList[$i][$field], $value);
							}
						}else {
							$paramList[$i][$field]=$value;							
						}							
						return true;
					}
				}
			
				// add param
				array_push($paramList, array($field => strval($value)));
				
			// multiple names struct
			}elseif (is_string($structName) && $structCount == "multiple") {
				// search struct
				$foundIdx = -1;
				$no = count($paramList);
				for ($i = 0; $i < $no; $i++) {					
					if (isset($paramList[$i][$structName])) {
						$noFields = count($paramList[$i][$structName]);
						for ($j = 0; $j < $noFields; $j++) {
							if (!isset($paramList[$i][$structName][$j][$field]) ) {
								$paramList[$i][$structName][$j][$field]=strval($value);
								return true;														
							}
						}
						
						array_push($paramList[$i][$structName], array($field => strval($value)));
						return true;
					}
				}

				// add param
				if ($no > 0) {
					$paramList[0][$structName] = array(array($field => strval($value)));
				}else {
					array_push($paramList, array( $structName => array(array($field => strval($value)))));
				}
				
			// single named struct
			}elseif (is_string($structName)) {
				// search struct
				$foundIdx = -1;
				$no = count($paramList);
				for ($i = 0; $i < $no; $i++) {					
					if (isset($paramList[$i][$structName])) {
						if (isset($paramList[$i][$structName][$field])) {
							if (is_array($paramList[$i][$structName][$field])) {
								array_push($paramList[$i][$structName][$field], $value);
							}else {
								$paramList[$i][$structName][$field]=array($paramList[$i][$structName][$field], $value);
							}
						}else {
							$paramList[$i][$structName][$field]=strval($value);
						}
						return true;
					}
				}

				// add param
				if ($no > 0) {
					$paramList[0][$structName] = array($field => strval($value));
				}else {
					array_push($paramList, array( $structName => array($field => strval($value))));
				}
			}

			return true;
		}
		
		// output left leaf
		$this->outputSubTree($node->getLeftNode(), $tableList, $protocol, $paramList);
	
		// output right leaf
		$this->outputSubTree($node->getRightNode(), $tableList, $protocol, $paramList);
		
		return true;
	}
	
	
    /**
     * Whether tree has group fields.
     *
     * @param node 
     * @param groupFields
     * @return true/false
     */
	public function isGroupFields($node, $groupFields)
	{
		if (!$node) {
			return false;
		}
	
		// node terminal
		if ($node->isTerminal()) {
			$field = $node->getStrippedName();
			if (!in_array($field, $groupFields)) {
				return false;
			}
				
			return true;
		}
		
		// left tree
		if (!$this->isGroupFields($node->getLeftNode(), $groupFields)) {
			return false;
		}
		
		// right tree
		if (!$this->isGroupFields($node->getRightNode(), $groupFields)) {
			return false;
		}

		return true;
	}
	
	
    /**
     * Output tree for debug.
     *
     * @param none
     * @return true/false
     */
	public function outputDebug()
	{
		if (!$this->root) {
			return true;
		}
	
		return $this->outputSubTreeDebug($this->root);
	}
	
	
    /**
     * Output sub-tree for debug.
     *
     * @param node
     * @return true/false
     */
	public function outputSubTreeDebug($node)
	{
		if (!$node) {
			return "";
		}
	
		// node terminal
		if ($node->isTerminal()) {
			$result = " - endleaf " . $node->getName() . "=" . $node->getValue() . "<br>";
			return $result;
		}
		
		$result = "parent_open<br>";
		
		// output left leaf
		$result .= $this->outputSubTreeDebug($node->getLeftNode());
		
		// output parent
		$result .= $node->getOperator() . "<br>";			
		
		// output right leaf
		$result .= $this->outputSubTreeDebug($node->getRightNode());
		
		$result .= "parent_close<br>";
		
		return $result;
	}
	
};


?>