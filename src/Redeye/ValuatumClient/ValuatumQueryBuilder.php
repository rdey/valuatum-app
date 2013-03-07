<?php

namespace Redeye\ValuatumClient;

use Redeye\ValuatumClient\ExpressionTree;
use Redeye\ValuatumClient\ValuatumSoapProtocol;
use \SoapClient;
use \SoapHeader;

/**
 * This class is responsible for building and executing SOAP queries over the Valuatum Web Service.
 *
 * @since 1.0
 * @author
 */
class ValuatumQueryBuilder
{
    /**
     * The error list used by Valuatum Query Builder.
     *
     * @var array
     */
    protected $valuatumQBErrorList = array(
        array(1, "Unknown virtual table"),
        array(2, "Invalid select field"),
        array(3, "Invalid where clause"),
        array(4, "Invalid set field"),
        array(5, "No query table selected"),
        array(6, "Invalid field in where clause"),
        array(7, "Select where clause cannot be empty"),
        array(8, "Update table cannot be empty"),
        array(9, "Update field cannot be empty"),
        array(10, "Select table cannot be empty"),
        array(11, "Failed to send request to Valuatum SOAP service"),
        array(12, "Failed to build request XML")
    );

    /**
     * The select table list.
     *
     * @var array
     */
    protected $selectTableList = array();

    /**
     * The update table list.
     *
     * @var array
     */
    protected $updateTableList = array();

    /**
     * The select field list.
     *
     * @var array
     */
    protected $selectFieldList = array();

    /**
     * The set field list.
     *
     * @var array
     */
    protected $setFieldList = array();

    /**
     * The where field list.
     *
     * @var array
     */
    protected $whereFieldList = array();

    /**
     * The last error code.
     *
     * @var integer
     */
    protected $lastErrorCode = 0;

    /**
     * The last error message.
     *
     * @var string
     */
    protected $lastErrorMsg = "";

    /**
     * The last SOAP error message.
     *
     * @var string
     */
    protected $lastSoapError = "";

    /**
     * The last SOAP request.
     *
     * @var string
     */
    protected $lastSoapRequest = "";

    /**
     * The protocol class.
     *
     * @var ValuatumSoapProtocol
     */
    protected $protocol = false;

    /**
     * The SOAP username.
     *
     * @var string
     */
    protected $soapUsername = "";

    /**
     * The SOAP password.
     *
     * @var string
     */
    protected $soapPassword = false;


    /**
     * Constructor.
     *
     * @param string $soapUsername
     * @param string $soapPassword
     * @internal param $soapUsername
     * @internal param $soapPassword
     * @return \Valuatum\ValuatumQueryBuilder
     */
    public function __construct($soapUsername, $soapPassword)
    {
        $this->soapUsername = $soapUsername;
        $this->soapPassword = $soapPassword;

        // init protocol
        $this->protocol = new ValuatumSoapProtocol();
    }


    /**
     * Select statement.
     *
     * @param string $select
     * @return this object
     */
    public function select($select)
    {
        // clear error
        $this->clearError();

        // parse field list
        $this->selectFieldList = $this->parseFieldList($select);

        // empty field list
        if (!$this->selectFieldList || count($this->selectFieldList) <= 0) {
            $this->lastErrorCode = $this->valuatumQBErrorList[6][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[6][1];

            return $this;
        }

        return $this;
    }


    /**
     * Update statement.
     *
     * @param string $table
     * @param string $alias
     * @return this object
     */
    public function update($table, $alias = "")
    {
        // clear error
        $this->clearError();

        // empty table name
        if ($table == "") {
            $this->lastErrorCode = $this->valuatumQBErrorList[7][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[7][1];

            return $this;
        }

        // validate table name
        if (!$this->validateTableName($table)) {
            $this->lastErrorCode = $this->valuatumQBErrorList[0][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[0][1] . ": " . $table;

            return $this;
        }

        // add table
        array_push($this->updateTableList, array("name" => $table, "alias" => $alias));

        return $this;
    }


    /**
     * Set field statement.
     *
     * @param string $key
     * @param string $value
     * @return this object
     */
    public function set($key, $value = "")
    {
        if ($this->lastErrorCode != 0) {
            return $this;
        }

        // empty field name
        if ($key == "") {
            $this->lastErrorCode = $this->valuatumQBErrorList[8][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[8][1];

            return $this;
        }

        // validate field name
        if (!$this->validateSetFieldName($key)) {
            $this->lastErrorCode = $this->valuatumQBErrorList[3][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[3][1] . ": " . $key;

            return $this;
        }

        // add field
        array_push($this->setFieldList, array("name" => $key, "value" => $value));

        return $this;
    }


    /**
     * Select From table statement.
     *
     * @param string $table
     * @param string $alias
     * @return this object
     */
    public function from($table, $alias = "")
    {
        if ($this->lastErrorCode != 0) {
            return $this;
        }

        // empty field name
        if ($table == "") {
            $this->lastErrorCode = $this->valuatumQBErrorList[9][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[9][1];

            return $this;
        }

        // validate table name
        if (!$this->validateTableName($table)) {
            $this->lastErrorCode = $this->valuatumQBErrorList[0][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[0][1] . ": " . $table;

            return $this;
        }

        // add table
        array_push($this->selectTableList, array("name" => $table, "alias" => $alias));

        $field = "";

        // validate field name
        if (!$this->validateGetFieldList($this->selectFieldList, $field)) {
            $this->lastErrorCode = $this->valuatumQBErrorList[1][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[1][1] . ": " . $field;

            return $this;
        }

        return $this;
    }


    /**
     * Where statement.
     *
     * @param string $where
     * @return this object
     */
    public function where($where)
    {
        if ($this->lastErrorCode != 0) {
            return $this;
        }

        // empty clause
        if ($where == "") {
            return $this;
        }

        // clear where list
        $this->clearExprList($this->whereFieldList);
        unset($this->whereFieldList);

        // init where list
        $this->whereFieldList = array();

        // parse expression
        $expr = new ExpressionTree();
        if (!$expr->parse($where)) {
            unset($expr);
            $this->lastErrorCode = $this->valuatumQBErrorList[2][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[2][1] . ": " . $where;

            return $this;
        }

        $field = "";

        // validate field tree
        if (!$this->validateWhereFieldTree($expr, $field)) {
            unset($expr);
            $this->lastErrorCode = $this->valuatumQBErrorList[5][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[5][1] . ": \"" . $field . "\" in where clause \"" . $where . "\"";

            return $this;
        }

        // add expression
        array_push($this->whereFieldList, array("bind" => "", "expr" => $expr));

        return $this;
    }


    /**
     * AND Where statement.
     *
     * @param string $where
     * @return this object
     */
    public function andWhere($where)
    {
        if ($this->lastErrorCode != 0) {
            return $this;
        }

        // empty clause
        if ($where == "") {
            return $this;
        }

        // parse expression
        $expr = new ExpressionTree();
        if (!$expr->parse($where)) {
            unset($expr);
            $this->lastErrorCode = $this->valuatumQBErrorList[2][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[2][1] . ": " . $where;

            return $this;
        }

        $field = "";

        // validate field tree
        if (!$this->validateWhereFieldTree($expr, $field)) {
            unset($expr);
            $this->lastErrorCode = $this->valuatumQBErrorList[5][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[5][1] . ": \"" . $field . "\" in where clause \"" . $where . "\"";

            return $this;
        }

        // add expression
        array_push($this->whereFieldList, array("bind" => "AND", "expr" => $expr));

        return $this;
    }


    /**
     * OR Where statement.
     *
     * @param string $where
     * @return this object
     */
    public function orWhere($where)
    {
        if ($this->lastErrorCode != 0) {
            return $this;
        }

        // empty clause
        if ($where == "") {
            return $this;
        }

        // parse expression
        $expr = new ExpressionTree();
        if (!$expr->parse($where)) {
            unset($expr);
            $this->lastErrorCode = $this->valuatumQBErrorList[2][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[2][1] . ": " . $where;

            return $this;
        }

        $field = "";

        // validate field tree
        if (!$this->validateWhereFieldTree($expr, $field)) {
            unset($expr);
            $this->lastErrorCode = $this->valuatumQBErrorList[5][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[5][1] . ": \"" . $field . "\" in where clause \"" . $where . "\"";

            return $this;
        }

        // add expression
        array_push($this->whereFieldList, array("bind" => "OR", "expr" => $expr));

        return $this;
    }


    /**
     * Run statement.
     *
     * @param none
     * @return response
     */
    public function run()
    {
        if ($this->lastErrorCode != 0) {
            return false;
        }

        if (count($this->selectTableList) <= 0 && count($this->updateTableList) <= 0) {
            $this->clearQuery();
            $this->lastErrorCode = $this->valuatumQBErrorList[4][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[4][1];

            return false;
        }

        // add set fields
        $setFields = "";
        foreach ($this->setFieldList as $field) {
            $where = $field["name"] . "=" . $field["value"];
            // parse expression
            $expr = new ExpressionTree();
            if (!$expr->parse($where)) {
                unset($expr);
                $this->clearQuery();
                $this->lastErrorCode = $this->valuatumQBErrorList[2][0];
                $this->lastErrorMsg = $this->valuatumQBErrorList[2][1] . ": " . $where;

                return false;
            }

            // add expression
            array_unshift($this->whereFieldList, array("bind" => "AND", "expr" => $expr));
        }

        // generate request
        $request = $this->generateRequest();
        if (false == $request) {
            $this->clearQuery();
            $this->lastErrorCode = $this->valuatumQBErrorList[11][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[11][1];

            return false;
        }

        // send request
        $response = $this->sendSoapRequest($request);
        if (false == $response) {
            $this->clearQuery();
            $this->lastErrorCode = $this->valuatumQBErrorList[10][0];
            $this->lastErrorMsg = $this->valuatumQBErrorList[10][1];

            return false;
        }

        // clear query
        $this->clearQuery();

        return $response;
    }


    /**
     * Parse field list.
     *
     * @param table
     * @return true/false
     */
    protected function parseFieldList($clause)
    {
        $list = explode(",", $clause);

        $fieldList = array();

        // traverse fields
        $no = count($list);
        for ($i = 0; $i < $no; $i++) {
            $field = trim($list[$i]);
            if ($field != "") {
                array_push($fieldList, $field);
            }
        }

        return $fieldList;
    }


    /**
     * Validate table name.
     *
     * @param table
     * @return true/false
     */
    protected function validateTableName($table)
    {
        return $this->protocol->isValidTable($table);
    }


    /**
     * Validate get field name.
     *
     * @param field
     * @return true/false
     */
    protected function validateGetFieldName($field)
    {
        if ($field == "*") {
            return true;
        }

        // table update
        if (count($this->updateTableList) > 0) {
            return $this->validateSetFieldName($field);
        }

        // traverse tables
        $no = count($this->selectTableList);
        for ($i = 0; $i < $no; $i++) {
            $testField = $field;

            // all table
            if ($this->selectTableList[$i]["alias"] == $testField) {
                return true;
            }
        }

        return false;
    }


    /**
     * Validate get field list.
     *
     * @param fieldList
     * @param errorField
     * @return true/false
     */
    protected function validateGetFieldList($fieldList, &$errorField)
    {
        // traverse fields
        $no = count($fieldList);
        for ($i = 0; $i < $no; $i++) {
            if (!$this->validateGetFieldName($fieldList[$i])) {
                $errorField = $fieldList[$i];

                return false;
            }
        }

        return true;
    }


    /**
     * Validate set field name.
     *
     * @param field
     * @return true/false
     */
    protected function validateSetFieldName($field)
    {
        // traverse tables
        $no = count($this->updateTableList);
        for ($i = 0; $i < $no; $i++) {
            $testField = $field;

            // remove alias
            if ($this->updateTableList[$i]["alias"] != "") {
                $len = strlen($this->updateTableList[$i]["alias"]);
                if (strlen($testField) > $len && 0 == strcmp(
                    substr($testField, 0, $len),
                    $this->updateTableList[$i]["alias"]
                ) && $testField[$len] == '.'
                ) {
                    $testField = substr($testField, $len + 1);
                }
            }

            // check field
            if ($this->protocol->isTableField($this->updateTableList[$i]["name"], $testField)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Validate field tree.
     *
     * @param tree
     * @param errorField
     * @return true/false
     */
    protected function validateWhereFieldTree($tree, &$errorField)
    {
        // set
        if (count($this->updateTableList) > 0) {
            if (!$tree->validateFields($this->updateTableList, $this->protocol, 1, $errorField))
                return false;
        } else {
            // get
            if (!$tree->validateFields($this->selectTableList, $this->protocol, 0, $errorField))
                return false;
        }

        return true;
    }


    /**
     * Generate request.
     *
     * @param none
     * @return request
     */
    protected function generateRequest()
    {
        // set
        if (count($this->updateTableList) > 0) {
            $tableList = $this->updateTableList;
        } else {
            // get
            $tableList = $this->selectTableList;
        }

        // get request parameters
        if (!$this->protocol->getTableParameters($tableList[0]["name"], $soapUrl, $soapAction, $requestPart)) {
            return false;
        }

        // init param list
        $paramList = array();

        // where clause
        $no = count($this->whereFieldList);
        for ($i = 0; $i < $no; $i++) {
            $this->whereFieldList[$i]["expr"]->output($tableList, $this->protocol, $paramList);
        }

        // add request part
        if ($requestPart != "") {
            $paramList = array(array($requestPart => $paramList[0]));
        }

        // set request
        $request = array();
        $request["soapUrl"] = $soapUrl;
        $request["soapAction"] = $soapAction;
        $request["paramList"] = $paramList;

        // set last request
        $this->lastSoapRequest = $request;

        return $request;
    }


    /**
     * Output node parent.
     *
     * @param field
     * @param whereGroup
     * @param parentTag
     * @return result
     */
    protected function outputNodeParent($field, $whereGroup, &$parentTag)
    {
        // traverse parent tags
        $no = count($whereGroup);
        for ($i = 0; $i < $no; $i++) {
            if ($whereGroup[$i][0] == $field || in_array($field, $whereGroup[$i][3])) {
                $parentTag = $whereGroup[$i][1];

                return true;
            }
        }

        return false;
    }


    /**
     * Send SOAP request.
     *
     * @param request
     * @return result
     */
    protected function sendSoapRequest($request)
    {
        try {
            // allocate SOAP client
            $client = new SoapClient(
                $request["soapUrl"], array(
                    "login" => $this->soapUsername,
                    "password" => $this->soapPassword
                )
            );

            //var_dump( $client->__getFunctions() );

            // call SOAP function
            $result = $client->__soapCall(
                $request["soapAction"],
                $request["paramList"],
                null,
                new SoapHeader(
                    "http://www.w3.org/2001/XMLSchema",
                    "UserAgent"
                )
            );

        } catch (Exception $e) {
            $this->lastSoapError = $e->faultstring;

            return false;
        }

        return $result;
    }


    /**
     * Get last error code.
     *
     * @param none
     * @return integer
     */
    public function getLastErrorCode()
    {
        return $this->lastErrorCode;
    }


    /**
     * Get last error message.
     *
     * @param none
     * @return string
     */
    public function getLastError()
    {
        return $this->lastErrorMsg;
    }


    /**
     * Get last SOAP error message.
     *
     * @param none
     * @return string
     */
    public function getLastSoapError()
    {
        return $this->lastSoapError;
    }


    /**
     * Get last SOAP request.
     *
     * @param none
     * @return string/array
     */
    public function getLastSoapRequest()
    {
        return $this->lastSoapRequest;
    }


    /**
     * Clear query.
     *
     * @param none
     * @return none
     */
    protected function clearQuery()
    {
        $this->clearExprList($this->whereFieldList);

        $this->selectTableList = array();
        $this->updateTableList = array();
        $this->selectFieldList = array();
        $this->setFieldList = array();
        $this->whereFieldList = array();
    }


    /**
     * Clear error.
     *
     * @param none
     * @return none
     */
    protected function clearError()
    {
        $this->lastErrorCode = 0;
        $this->lastErrorMsg = "";
        $this->lastSoapError = "";
        $this->lastSoapRequest = "";
    }


    /**
     * Clear expression list.
     *
     * @param none
     * @return none
     */
    protected function clearExprList($list)
    {
        $no = count($list);

        for ($i = 0; $i < $no; $i++) {
            unset($list[$i]);
        }
    }

    /**
     * (PHP 5 >= 5.4.0)
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link http://docs.php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }
}

;