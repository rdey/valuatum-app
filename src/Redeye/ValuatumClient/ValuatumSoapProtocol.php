<?php

namespace Redeye\ValuatumClient;

/**
 * This class is responsible for implementing the objects required in Valuatum SOAP protocol.
 *
 * @since 1.0
 * @author
 */
class ValuatumSoapProtocol
{
    /**
     * The virtual query table list.
     *
     * @var array
     */
    protected $tables = array(

        // Analysts
        array(
            "name" => "Analysts",
            "soapUrl" => "https://ws.valuatum.com/ws/1/analystWebservice/analystWebservice.wsdl",
            "soapAction" => "AnalystData",
            "fields" => array(
                array("id")
            )
        ),
        // ClientMetaData
        array(
            "name" => "ClientMetaData",
            "soapUrl" => "https://ws.valuatum.com/ws/1/clientMetadataWebservice/clientMetadataWebservice.wsdl",
            "soapAction" => "ClientMetadata",
            "fields" => array(
                array("listIndustries"),
                array("listReportTypes"),
                array("listAnalysts"),
                array("listCompanies"),
                array("listExternal"),
                array("listRatingTypes"),
                array("listVariables"),
                array("listConsensusdataProviders"),
            )
        ),
        // Company
        array(
            "name" => "Company",
            "soapUrl" => "https://ws.valuatum.com/ws/1/companyWebservice/companyWebservice.wsdl",
            "soapAction" => "Company",
            "fields" => array(
                array("id")
            )
        ),
        // CompanyData
        array(
            "name" => "CompanyData",
            "soapUrl" => "https://ws.valuatum.com/ws/1/companyWebservice/companyWebservice.wsdl",
            "soapAction" => "CompanyData",
            "requestPart" => "CompanyDataRequestPart",
            "fields" => array(
                array("requestId"),
                array("analysisId"),
                array("startPosition"),
                array("endPosition"),
                array("code", "varItem", "multiple"),
                array("position", "varItem", "multiple")
            )
        ),
        // CompanyDataSave
        array(
            "name" => "CompanyDataSave",
            "soapUrl" => "https://ws.valuatum.com/ws/1/companyWebservice/companyWebservice.wsdl",
            "soapAction" => "CompanyDataSave",
            "fields" => array(
                array("company"),
                array("analystName"),
                array("asp"),
                array("currency"),
                array("currentYear"),
                array("estimateYearsCount"),
                array("reclevel"),
                array("recommendation"),
                array("targetPrice"),
                array("visibility"),
                array("modelTypeId"),
                array("useTempSaving"),
                array("server"),
                array("divName"),
                array("costItemTitle"),
                array("code", "varItem", "multiple"),
                array("position", "varItem", "multiple"),
                array("value", "varItem", "multiple")
            )
        ),
        // ConsensusData
        array(
            "name" => "ConsensusData",
            "soapUrl" => "https://ws.valuatum.com/ws/1/consensusWebservice/consensusWebservice.wsdl",
            "soapAction" => "ConsensusData",
            "requestPart" => "RequestPart",
            "fields" => array(
                array("dataProviderId"),
                array("companyId"),
                array("requestId"),
                array("code", "varItem", "multiple"),
                array("function", "varItem", "multiple"),
                array("position", "varItem", "multiple")
            )
        ),
        // EstimateHistoryData
        array(
            "name" => "EstimateHistoryData",
            "soapUrl" => "https://ws.valuatum.com/ws/1/estimateHistorydataWebservice/estimateHistorydataWebservice.wsdl",
            "soapAction" => "EstimateHistorydata",
            "requestPart" => "EstimateHistorydataRequestPart",
            "fields" => array(
                array("requestId"),
                array("analysisId"),
                array("startDate"),
                array("endDate"),
                array("changesOnly"),
                array("currency"),
                array("code", "varItem", "multiple"),
                array("position", "varItem", "multiple")
            )
        ),
        // EstVsActData
        array(
            "name" => "EstVsActData",
            "soapUrl" => "https://ws.valuatum.com/ws/1/estVsActdataWebservice/estVsActdataWebservice.wsdl",
            "soapAction" => "EstVsActData",
            "fields" => array(
                array("requestId"),
                array("analysisId"),
                array("currency"),
                array("daysBeforeActualization"),
                array("code", "varItem", "multiple"),
                array("position", "varItem", "multiple")
            )
        ),
        // Currency
        array(
            "name" => "Currency",
            "soapUrl" => "https://ws.valuatum.com/ws/1/externalDataWebservice/externalDataWebservice.wsdl",
            "soapAction" => "Currency",
            "fields" => array(
                array("targetCurrency")
            )
        ),
        // Index
        array(
            "name" => "Index",
            "soapUrl" => "https://ws.valuatum.com/ws/1/externalDataWebservice/externalDataWebservice.wsdl",
            "soapAction" => "Index",
            "fields" => array(
                array("indexId"),
                array("startDate"),
                array("endDate")
            )
        ),
        // ListCountries
        array(
            "name" => "ListCountries",
            "soapUrl" => "https://ws.valuatum.com/ws/1/externalDataWebservice/externalDataWebservice.wsdl",
            "soapAction" => "ListCountries"
        ),
        // ListExchanges
        array(
            "name" => "ListExchanges",
            "soapUrl" => "https://ws.valuatum.com/ws/1/externalDataWebservice/externalDataWebservice.wsdl",
            "soapAction" => "ListExchanges"
        ),
        // ListIndexes
        array(
            "name" => "ListIndexes",
            "soapUrl" => "https://ws.valuatum.com/ws/1/externalDataWebservice/externalDataWebservice.wsdl",
            "soapAction" => "ListIndexes"
        ),
        // ListRegions
        array(
            "name" => "ListRegions",
            "soapUrl" => "https://ws.valuatum.com/ws/1/externalDataWebservice/externalDataWebservice.wsdl",
            "soapAction" => "ListRegions"
        ),
        // IndustryTree
        array(
            "name" => "IndustryTree",
            "soapUrl" => "https://ws.valuatum.com/ws/1/industryWebservice/industryWebservice.wsdl",
            "soapAction" => "IndustryTree",
            "fields" => array(
                array("includeCompanies")
            )
        ),
        // IndustryData
        array(
            "name" => "IndustryData",
            "soapUrl" => "https://ws.valuatum.com/ws/1/industryWebservice/industryWebservice.wsdl",
            "soapAction" => "IndustryData",
            "fields" => array(
                array("industryId", "Industry", "multiple"),
                array("companyId", "Industry", "multiple"),
                array("code", "varItem", "multiple"),
                array("position", "varItem", "multiple")
            )
        ),
        // ResearchGet
        array(
            "name" => "ResearchGet",
            "soapUrl" => "https://ws.valuatum.com/ws/1/researchWebservice/researchWebservice.wsdl",
            "soapAction" => "ResearchGet",
            "fields" => array(
                array("reportId")
            )
        ),
        // ResearchSave
        array(
            "name" => "ResearchSave",
            "soapUrl" => "https://ws.valuatum.com/ws/1/researchWebservice/researchWebservice.wsdl",
            "soapAction" => "ResearchSave",
            "fields" => array(
                array("reportId", "researchReport"),
                array("analystId", "researchReport"),
                array("companyId", "researchReport"),
                array("publishDate", "researchReport"),
                array("publishTime", "researchReport"),
                array("extraCompanyId", "researchReport"),
                array("extraIndustryId", "researchReport"),
                array("fileType", "researchReport"),
                array("industryId", "researchReport"),
                array("pages", "researchReport"),
                array("ratingId", "researchReport"),
                array("reportTypeId", "researchReport"),
                array("target", "researchReport"),
                array("title", "researchReport"),
                array("summary", "researchReport"),
                array("reportFile")
            )
        ),
        // ResearchSearch
        array(
            "name" => "ResearchSearch",
            "soapUrl" => "https://ws.valuatum.com/ws/1/researchWebservice/researchWebservice.wsdl",
            "soapAction" => "ResearchSearch",
            "fields" => array(
                array("analystId"),
                array("companyId"),
                array("fileType"),
                array("fromDate"),
                array("industryId"),
                array("mainTypeId"),
                array("maxResults"),
                array("ratingId"),
                array("reportTypeId"),
                array("title"),
                array("toDate")
            )
        ),
        // StockPrice
        array(
            "name" => "StockPrice",
            "soapUrl" => "https://ws.valuatum.com/ws/1/stockpriceWebservice/stockpriceWebservice.wsdl",
            "soapAction" => "StockPrice",
            "requestPart" => "stockPriceRequestPart",
            "fields" => array(
                array("companyId"),
                array("startDate"),
                array("endDate"),
                array("indexId"),
                array("currency")
            )
        ),
        // Variable
        array(
            "name" => "Variable",
            "soapUrl" => "https://ws.valuatum.com/ws/1/variableWebservice/variableWebservice.wsdl",
            "soapAction" => "Variable"
        )
    );


    /**
     * Get table parameters.
     *
     * @param name
     * @param soapUrl
     * @param soapAction
     * @return true/false
     */
    public function getTableParameters($name, &$soapUrl, &$soapAction, &$requestPart)
    {
        // traverse tables
        $no = count($this->tables);
        for ($i = 0; $i < $no; $i++) {
            if ($this->tables[$i]["name"] == $name) {
                $soapUrl = $this->tables[$i]["soapUrl"];
                $soapAction = $this->tables[$i]["soapAction"];

                if (isset($this->tables[$i]["requestPart"])) {
                    $requestPart = $this->tables[$i]["requestPart"];
                } else {
                    $requestPart = "";
                }

                return true;
            }
        }

        return false;
    }


    /**
     * Whether valid table.
     *
     * @param name
     * @return true/false
     */
    public function isValidTable($name)
    {
        // traverse tables
        $no = count($this->tables);
        for ($i = 0; $i < $no; $i++) {
            if ($this->tables[$i]["name"] == $name) {
                return true;
            }
        }

        return false;
    }


    /**
     * Whether valid table field.
     *
     * @param table
     * @param field
     * @return true/false
     */
    public function isTableField($table, $field)
    {
        // traverse tables
        $no = count($this->tables);
        for ($i = 0; $i < $no; $i++) {
            if ($this->tables[$i]["name"] == $table) {
                if (!isset($this->tables[$i]["fields"])) {
                    return false;
                }

                // traverse table fields
                $noFields = count($this->tables[$i]["fields"]);
                for ($j = 0; $j < $noFields; $j++) {
                    if ($this->tables[$i]["fields"][$j][0] == $field) {
                        return true;
                    }
                }

                return false;
            }
        }

        return false;
    }


    /**
     * Get table field type.
     *
     * @param table
     * @param field
     * @param structName
     * @param structCount
     * @return true/false
     */
    public function getTableFieldType($table, $field, &$structName, &$structCount)
    {
        // traverse tables
        $no = count($this->tables);
        for ($i = 0; $i < $no; $i++) {
            if ($this->tables[$i]["name"] == $table) {
                // traverse table fields
                if (!isset($this->tables[$i]["fields"])) {
                    return false;
                }
                $list = $this->tables[$i]["fields"];
                $noFields = count($list);
                for ($j = 0; $j < $noFields; $j++) {
                    if ($list[$j][0] == $field) {
                        if (isset($list[$j][1])) {
                            $structName = $list[$j][1];
                        } else {
                            $structName = "";
                        }

                        if (isset($list[$j][2])) {
                            $structCount = $list[$j][2];
                        } else {
                            $structCount = "";
                        }

                        return true;
                    }
                }

                return false;
            }
        }

        return false;
    }

}
