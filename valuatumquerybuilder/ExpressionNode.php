<?php


/**
 * This class holds data structure for expression tree nodes.
 *
 * @since 1.0
 * @author 
 */
class ExpressionNode
{
    /**
     * Node name.
     *
     * @var string
     */
	protected $name = "";
	
    /**
     * Node value.
     *
     * @var string
     */	
	protected $value = "";
	
    /**
     * Node operator.
     *
     * @var string
     */		
	protected $operator = "";
	
    /**
     * Node left sub-tree.
     *
     * @var ExpressionNode
     */		
	protected $leftNode = false;
	
    /**
     * Node right sub-tree.
     *
     * @var ExpressionNode
     */		
	protected $rightNode = false;
	
	
    /**
     * Constructor.
     *
     * @param name
     * @param value	 
     * @param operator	 	 
     * @return true/false
     */
	function __construct($name, $value, $operator)
	{
		$this->name 	= $name;
		$this->value	= $value;
		$this->operator	= $operator;		
	}


    /**
     * Get name.
     *
     * @param none
     * @return string
     */
	public function getName()
	{
		return $this->name;
	}
	
	
    /**
     * Get stripped name (from table alias).
     *
     * @param none
     * @return string
     */
	public function getStrippedName()
	{
		$field = $this->name;
		
		// remove table alias
		$pos = strpos($field, ".");
		if (!is_bool($pos)) {
			$field = substr($field, $pos + 1);
		}
				
		return $field;
	}		
	

    /**
     * Get value.
     *
     * @param none
     * @return string
     */
	public function getValue()
	{
		return $this->value;
	}	
	
	
    /**
     * Get operator.
     *
     * @param none
     * @return string
     */
	public function getOperator()
	{
		return $this->operator;
	}
	
	
    /**
     * Get left node.
     *
     * @param none
     * @return ExpressionNode
     */
	public function getLeftNode()
	{
		return $this->leftNode;
	}
	
	
    /**
     * Get right node.
     *
     * @param none
     * @return ExpressionNode
     */
	public function getRightNode()
	{
		return $this->rightNode;
	}
	
	
    /**
     * Whether terminal node.
     *
     * @param none
     * @return true/false
     */
	public function isTerminal()
	{
		return !$this->leftNode && !$this->rightNode;
	}
	
	
    /**
     * Set name.
     *
     * @param name
     * @return none
     */
	public function setName( $name )
	{
		$this->name = $name;
	}
	
	
    /**
     * Set value.
     *
     * @param value
     * @return none
     */
	public function setValue( $value )
	{
		$this->value = $value;
	}
	
	
    /**
     * Set operator.
     *
     * @param operator
     * @return none
     */
	public function setOperator( $operator )
	{
		$this->operator = $operator;
	}
	
	
    /**
     * Set left node.
     *
     * @param child
     * @return none
     */
	public function setLeftNode( $child )
	{		
		$this->leftNode = $child;
	}
	
	
    /**
     * Set right node.
     *
     * @param child
     * @return none
     */
	public function setRightNode( $child )
	{		
		$this->rightNode = $child;
	}
	
};


?>