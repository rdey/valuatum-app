<?php
	
/**
 * Valuatum Query Builder test file.
 *
 * @since 1.0
 * @author 
 */
 
 
require_once "ValuatumQueryBuilder.php";


/**
 * Valuatum Query Builder.
 *
 * @var array
 */
$qb = false;


/**
 * Test count.
 *
 * @var integer
 */
$testCount = 1;


/**
 * Analysis Test.
 *
 * @param none
 * @return none
 */
function analystsTest()
{	
	global $qb;

	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// all analysts
	$result = $qb->select('a')
				 ->from('Analysts', 'a')
				 ->run();
				 
	outputResult("Analysts (all)", $result);
			
	// single analyst by id
	$result = $qb->select('a')
				 ->from('Analysts', 'a')
				 ->where('a.id = 38616')
				 ->run();
				 
	outputResult("Analysts (single)", $result);
			
	// multiple analysts by id
	$result = $qb->select('a')
				 ->from('Analysts', 'a')
				 ->where('a.id = 38616')
				 ->orWhere('a.id = 38385')
				 ->run();
				 
	outputResult("Analysts (multiple)", $result);
}



/**
 * ClientMetadata Test.
 *
 * @param none
 * @return none
 */
function clientMetaDataTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();

	// client info
	$result = $qb->select('c')
				 ->from('ClientMetaData', 'c')	
				 ->where('c.listIndustries = true')	
				 ->andWhere('c.listReportTypes = true')		
				 ->andWhere('c.listAnalysts = true')	
				 ->andWhere('c.listCompanies = true')			
				 ->andWhere('c.listExternal = true')	
				 ->andWhere('c.listRatingTypes = true')	
				 ->andWhere('c.listVariables = true')		
				 ->andWhere('c.listConsensusdataProviders = true')		
				 ->run();					 
				 
	outputResult("ClientMetaData", $result);			 
}


	
/**
 * Companies Test.
 *
 * @param none
 * @return none
 */
function companiesTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// all companies
	$result = $qb->select('c')
				 ->from('Company', 'c')
				 ->run();					 
				 
	outputResult("Company (all)", $result);		
			
	// single company by id
	$result = $qb->select('c')
				 ->from('Company', 'c')
				 ->where('c.id = 3044')
				 ->run();
			
	outputResult("Company (single)", $result);	
	
	// multiple companies by id
	$result = $qb->select('c')
				 ->from('Company', 'c')
				 ->where('c.id = 3012')
				 ->orWhere('c.id = 3042')
				 ->orWhere('c.id = 2967')
				 ->run();					 
				 
	outputResult("Company (multiple)", $result);			
	
	// company data
	$result = $qb->select('c')
				 ->from('CompanyData', 'c')
				 ->where('c.requestId = GET COMPANY DATA')
				 ->andWhere('c.analysisId = 3231')					 
				 ->andWhere('c.startPosition = 2008')
				 ->andWhere('c.endPosition = 2012')
				 ->andWhere('(c.code = EPS AND c.position = 2006) OR (c.code = EKS)')
				 ->run();					 
				 
	outputResult("CompanyData", $result);
			
	// company data save
	$result = $qb->update('CompanyDataSave', 'c')
				 ->set('c.company', 'Nokia')		
				 ->set('c.analystName', 'Test Analyst')						 
				 ->set('c.asp' , 'Finland')
				 ->set('c.currency', 'EUR')
				 ->set('c.currentYear', '2010')
				 ->set('c.estimateYearsCount', '10')
				 ->set('c.reclevel', '3')
				 ->set('c.recommendation', 'Hold')
				 ->set('c.targetPrice', '10.5')
				 ->set('c.visibility', 'public')
				 ->set('c.modelTypeId', '1')
				 ->set('c.useTempSaving', 'false')
				 ->set('c.server', 'http://localhost')
				 ->set('c.divName', 'Test Division')
				 ->set('c.costItemTitle', 'Test cost 1')
				 ->where('c.code = ns AND c.position = 2010 OR c.value = 1000')
				 ->andWhere('c.code = ebit AND c.position = 2010 OR c.value = 1000')
				 ->andWhere('c.code = tax_rate_wacc AND c.position = 2010 OR c.value = 0.28')
				 ->run();
				 
	outputResult("CompanyDataSave", $result);	
}



/**
 * Consensus Test.
 *
 * @param none
 * @return none
 */
function consensusTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// consensus data request
	$result = $qb->select('c')
				 ->from('ConsensusData', 'c')
				 ->where('c.dataProviderId = 1')
				 ->andWhere('c.companyId = 3042 OR c.companyId = 3012')				 
				 ->andWhere('(c.code = EPS AND c.function = all AND c.position = 20011) OR (c.code = EV AND c.function = max AND c.position = 20023)')
				 ->andWhere('c.requestId = SOME FIGURES')				 
				 ->run();
				 
	outputResult("ConsensusData", $result);			
}



/**
 * EstimateHistoryData Test.
 *
 * @param none
 * @return none
 */
function estimateHistoryDataTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// estimate history request
	$result = $qb->select('e')
				 ->from('EstimateHistoryData', 'e')
				 ->where('e.analysisId = 2083 OR e.analysisId = 2084')
				 ->andWhere('e.startDate = 2002-11-01')
				 ->andWhere('e.endDate = 2013-11-30')
				 ->andWhere('e.changesOnly = true')
				 ->andWhere('e.currency = EUR')
				 ->andWhere('e.requestId = SOME ESTIMATE FIGURES')
				 ->andWhere('(e.code = EPS) OR (e.code = EV AND e.position = 20031)')
				 ->run();
				 
	outputResult("EstimateHistoryData", $result);
}



/**
 * EstimatesVsActualizedData Test.
 *
 * @param none
 * @return none
 */
function estimatesVsActualizedDataTest()
{
	global $qb;

	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// estimate history request
	$result = $qb->select('e')
				 ->from('EstVsActData', 'e')
				 ->where('e.analysisId = 2083')
				 ->andWhere('e.currency = EUR')
				 ->andWhere('e.daysBeforeActualization = 3')
				 ->andWhere('e.requestId = SOME ESTIMATE FIGURES')
				 ->andWhere('(e.code = EPS AND e.position = 20091) OR (e.code = EPS AND e.position = 20092)')
				 ->run();

	outputResult("EstVsActData", $result);
}



/**
 * ExternalData Test.
 *
 * @param none
 * @return none
 */
function externalDataTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// currency with targetCurrency
	$result = $qb->select('c')
				 ->from('Currency', 'c')
				 ->where('c.targetCurrency = EUR')
				 ->run();
				 
	outputResult("Currency (with target)", $result);
			
	// currency without targetCurrency
	$result = $qb->select('c')
				 ->from('Currency', 'c')
				 ->run();

	outputResult("Currency (without target)", $result);
			
	// single index values by id
	$result = $qb->select('i')
				 ->from('Index', 'i')
				 ->where('i.indexId = 1')
				 ->andWhere('i.startDate = 2013-01-01')
				 ->andWhere('i.endDate = 2013-01-05')
				 ->run();						 

	outputResult("Index (single)", $result);
			
	// multiple indexes values by id
	$result = $qb->select('i')
				 ->from('Index', 'i')
				 ->where('i.indexId = 1 OR i.indexId = 2 OR i.indexId = 3')
				 ->andWhere('i.startDate = 2013-01-01')
				 ->andWhere('i.endDate = 2013-01-05' )
				 ->run();
				 
	outputResult("Index (multiple)", $result);
				 
	// all countries
	$result = $qb->select('c')
				 ->from('ListCountries', 'c')
				 ->run();

	outputResult("ListCountries", $result);
				 
	// all exchanges
	$result = $qb->select('e')
				 ->from('ListExchanges', 'e')
				 ->run();

	outputResult("ListExchanges", $result);
				 
	// all indexes
	$result = $qb->select('i')
				 ->from('ListIndexes', 'i')
				 ->run();	 

	outputResult("ListIndexes", $result);
				 
	// all regions
	$result = $qb->select('r')
				 ->from('ListRegions', 'r')
				 ->run();	 

	outputResult("ListRegions", $result);
}



/**
 * IndustryCompanies Test.
 *
 * @param none
 * @return none
 */
function industryCompaniesTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// industries with companies
	$result = $qb->select('i')
				 ->from('IndustryTree', 'i')
				 ->where('i.includeCompanies = true')
				 ->run();						 

	outputResult("IndustryTree (with companies)", $result);
			
	// industries without companies
	$result = $qb->select('i')
				 ->from('IndustryTree', 'i')
				 ->run();

	outputResult("IndustryTree (without companies)", $result);
			
	// industry data
	$result = $qb->select('i')
				 ->from('IndustryData', 'i')
				 ->where('i.industryId = 1 AND i.companyId = 3012')
				 ->andWhere('i.industryId = 1 AND i.companyId = 3042')
				 ->andWhere('(i.code = EPS AND i.position = 2009) OR (i.code = EBIT AND i.position = 2009)')
				 ->run();

	outputResult("IndustryData", $result);
}



/**
 * ResearchReport Test.
 *
 * @param none
 * @return none
 */
function researchReportsTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();
	
	// get report
	$result = $qb->select('r')
				 ->from('ResearchGet', 'r')
				 ->where('r.reportId = 45')
				 ->run();
				 
	outputResult("ResearchGet", $result);
									 
	// save report
	$result = $qb->update('ResearchSave', 'r')
				 ->set('r.reportId', '1')
				 ->set('r.analystId', '1')
				 ->set('r.companyId', '1')
				 ->set('r.publishDate', '2010-07-01')
				 ->set('r.publishTime', '16:30:30')
				 ->set('r.extraCompanyId', '50')
				 ->set('r.extraIndustryId', '4')
				 ->set('r.fileType', 'pdf')
				 ->set('r.industryId', '1')
				 ->set('r.pages', '5')
				 ->set('r.ratingId', '3')
				 ->set('r.reportTypeId', '2')
				 ->set('r.target', '30')
				 ->set('r.title', 'Morning Report')
				 ->set('r.summary', 'This is a test report')
				 ->set('r.reportFile', 'cid:602429728959')
				 ->run();

	outputResult("ResearchSave", $result);

	// search report
	$result = $qb->select('r')
				 ->from('ResearchSearch', 'r')
				 ->where('r.analystId = 38616 OR r.analystId = 38385')
				 ->andWhere('r.companyId = 3044')
				 ->andWhere('r.fileType = doc')
				 ->andWhere('r.fromDate = 2013-01-05')
				 ->andWhere('r.industryId = 1133 OR r.industryId = 1132 OR r.industryId = 1121')
				 ->andWhere('r.mainTypeId = 1')	 
				 ->andWhere('r.maxResults = 1')
				 ->andWhere('r.ratingId = 1')		 
				 ->andWhere('r.reportTypeId = 1')			 
				 ->andWhere('r.title = 1')	 
				 ->andWhere('r.toDate = 2013-01-05')
				 ->run();					 

	outputResult("ResearchSearch", $result);
}



/**
 * StockPrices Test.
 *
 * @param none
 * @return none
 */
function stockPricesTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();		
	
	// search stock price
	$result = $qb->select('s')
				 ->from('StockPrice', 's')
				 ->where('s.companyId = 3044')
				 ->andWhere('s.startDate = 2013-01-01')
				 ->andWhere('s.endDate = 2013-01-05')
				 ->andWhere('s.currency = EUR')				 
				 ->andWhere('s.indexId = 1')
				 ->run();					 
				 
	outputResult("StockPrice", $result);
}



/**
 * Variables test.
 *
 * @param none
 * @return none
 */
function variablesTest()
{
	global $qb;
	
	// allocate query builder
	$qb = new ValuatumQueryBuilder();		
	
	// get variables
	$result = $qb->select('v')
				 ->from('Variable', 'v')
				 ->run();					 
				 
	outputResult("Variable", $result);
}



/**
 * Output results.
 *
 * @param test
 * @param result 
 * @return none
 */
function outputResult( $test, $result )
{
	global $qb;
	global $testCount;
	
	echo "<br><h3>Test " . $testCount++ . ": " . $test . "</h3>";
	flush();
	
	// request
	echo "<b>Request:</b><br><pre>" . print_r($qb->getLastSoapRequest(), true) . "</pre><br>";
	flush();
	
	// outputs results rows
	if ($result == false) {
		echo "Error Code: " . $qb->getLastErrorCode() . "<br>";
		echo "Error Message: " . $qb->getLastError() . "<br>";
		echo "SOAP Error: " . $qb->getLastSoapError() . "<br>";			
		flush();
		return;
	}
	
	// output fields
	echo "<b>Response:</b><br><pre>" . print_r($result, true) . "</pre>";
	flush();
}



/**
 * Main function.
 *
 * @param none
 * @return none
 */
function main()
{
	echo "<h3>Valuatum Query Builder Test Cases</h3>";

	// Analysts test
	analystsTest();
	
	// ClientMetadata test
	clientMetaDataTest();
	
	// Companies test
	companiesTest();
	
	// Consensus test
	consensusTest();
	
	// EstimateHistoryData test
	estimateHistoryDataTest();
	
	// EstimatesVsActualizedData test
	estimatesVsActualizedDataTest();
	
	// ExternalData test
	externalDataTest();
	
	// IndustryCompanies test
	industryCompaniesTest();
	
	// ResearchReports test
	researchReportsTest();
	
	// StockPrices test
	stockPricesTest();
	
	// Variables test
	variablesTest();
	
	echo "<br><h3>End Test Cases</h3>";		
}	


// main function
main();


?>