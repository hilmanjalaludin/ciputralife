-- MySQL dump 10.11
--
-- Host: localhost    Database: axadb
-- ------------------------------------------------------
-- Server version	5.0.51b-community
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping routines for database 'axadb'
--
DELIMITER ;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 FUNCTION `F_getEfectiveDate`(`CutoffOffDate` DATE, `IndentifyDate` DATE) RETURNS date
BEGIN
DECLARE EfectiveDate  DATE;
		
	SELECT 
		DISTINCT( IF( month(CutoffOffDate) =  month(IndentifyDate), 
		IF(  day(IndentifyDate) <= day(CutoffOffDate),
			  DATE(IndentifyDate),
			  IF( month(CutoffOffDate) >=12, 
			  	concat( (year(CutoffOffDate)+1),'-','01','-','01'),
				concat(year(CutoffOffDate),'-',IF( LENGTH(MONTH(IndentifyDate)+1)=1,concat('0',MONTH(IndentifyDate)+1),MONTH(IndentifyDate)+1)  ,'-','01') )  
			) ,
			'0000-00-00')) INTO EfectiveDate 
	FROM t_lk_cutoffdate;
	
	RETURN EfectiveDate;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO"*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`enigma`@`%`*/ /*!50003 FUNCTION `F_GetIdentificationNum`(`intCallReason` int, `strIdNumHolder` varchar(20), `strIdNumSpouse` varchar(20)) RETURNS varchar(20) CHARSET latin1
BEGIN
 DECLARE strIdNum varchar(20);
 IF intCallReason = 402 THEN 
  SET strIdNum = strIdNumSpouse;
 ELSE 
  SET strIdNum = strIdNumHolder;
 END IF;
 RETURN strIdNum;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE="NO_AUTO_VALUE_ON_ZERO"*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`enigma`@`%`*/ /*!50003 FUNCTION `F_getLastReasonStatus`(`FCustomerId` INT, `FTelesalesId` INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
	DECLARE REASON_ID INT;
	SELECT 
		a.CallReasonId INTO REASON_ID
	FROM t_gn_callhistory a
	WHERE a.CustomerId= FCustomerId 
			AND a.CreatedById = FTelesalesId
	ORDER BY a.CallHistoryCreatedTs DESC LIMIT 1;
	
	RETURN REASON_ID;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`enigma`@`%`*/ /*!50003 FUNCTION `F_GetPlanId`(`ProductId` INT, `ProductPlan` INT, `PayModeId` INT, `Age` INT) RETURNS int(11)
BEGIN
	DECLARE RowID INT;
		SELECT 
			a.ProductPlanId INTO RowID
		FROM t_gn_productplan a
			WHERE a.ProductId=ProductId
				AND a.ProductPlan = ProductPlan
				AND a.PayModeId = PayModeId
				AND Age BETWEEN a.ProductPlanAgeStart AND a.ProductPlanAgeEnd;
		RETURN RowID;
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
/*!50003 SET SESSION SQL_MODE=""*/;;
/*!50003 CREATE*/ /*!50020 DEFINER=`enigma`@`%`*/ /*!50003 FUNCTION `F_GetPremiumAge`(`ProductPlan` INT, `ProductId` INT, `PayModeId` INT, `FindAge` INT) RETURNS decimal(10,0)
    COMMENT 'F_GetPremiumAge'
BEGIN
	DECLARE PremiByAge DECIMAL;
	SELECT 
	  a.ProductPlanPremium INTO PremiByAge
	 FROM  t_gn_productplan a 
	WHERE a.ProductPlan= ProductPlan	
		and a.ProductId= ProductId
		and a.PayModeId= PayModeId
		and FindAge BETWEEN ProductPlanAgeStart and ProductPlanAgeEnd;
	return PremiByAge;	
END */;;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE*/;;
DELIMITER ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-11 11:46:50
