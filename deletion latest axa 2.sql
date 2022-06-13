select distinct
a.CustomerNumber as 'pros_prospect_Id',
d.CampaignNumber as 'pros_campaign_Id',
a.CustomerFirstName as 'pros_name',
DATE_FORMAT(a.CustomerDOB,  '%d-%m-%Y' ) as 'pros_dob',
r.PayerAddressLine1 as 'pros_haddress1',
'NULL' as 'pros_haddress2',
r.PayerAddressLine3 as 'pros_haddress3',
r.PayerAddressLine4 as 'pros_haddress4',
r.PayerCity as 'pros_hcity',
a.CustomerHomePhoneNum as 'pros_hphone1',
a.CustomerHomePhoneNum2 as 'pros_hphone2',
a.CustomerMobilePhoneNum as 'pros_mphone',
a.CustomerMobilePhoneNum2 as 'pros_mphone1',
a.CustomerWorkPhoneNum as 'pros_mphone2',
m.ProductCode as 'pros_Product_Id_master',
h.CallReasonCode as 'pros_call_Id',
i.CallReasonCategoryCode as 'pros_Call_group',
a.CustomerUpdatedTs as 'pros_Calldate',
n.id as'pros_agent_Id',
o.id as 'pros_spv_Id',
r.PayerAddressLine2 as 'pros_remark1',
(select distinct ss.CallHistoryNotes from t_gn_callhistory ss
where  a.CustomerId = ss.CustomerId
order by ss.CallHistoryCallDate DESC
limit 1 ) as 'pros_remark2',
'NULL'as 'pros_remark3',
'NULL'as 'pros_remark4',
'NULL'as 'pros_remark5',
'NULL'as 'pros_Accnumber',
'NULL'as 'pros_cifnumber',
r.PayerAddressLine2 as 'pros_Refnumber',
d.CampaignName as 'pros_camp_name',
d.CampaignEndDate as 'pros_Initialdate',
d.CampaignStartDate as 'pros_uploaddate',
'null'as 'pros_totalprosp',
p.PolicyNumber as 'pros_policy_Id',
m.ProductCode as 'pros_Product_Id',
p.PolicyEffectiveDate as 'pros_input',
p.PolicyEffectiveDate as 'pros_effdt',
p.Premi as 'pros_premium',
IF(q.PayModeId= 2,12*p.Premi,p.Premi) AS 'pros_nbi',
r.PayerCreditCardNum as 'acctnum',
r.PayerCreditCardExpDate as 'ccexpdate',
r.PayerEmail as 'posemail'

from t_gn_customer a
LEFT join t_gn_campaign d on a.CampaignId = d.CampaignId
LEFT join t_gn_policyautogen f on a.CustomerId = f.CustomerId
LEFT join t_gn_product g on f.ProductId = g.ProductId
LEFT join t_lk_callreason h on a.CallReasonId = h.CallReasonId
LEFT join t_lk_callreasoncategory i on h.CallReasonCategoryId = i.CallReasonCategoryId
LEFT join tms_agent n on a.SellerId = n.UserId
LEFT join tms_agent o on n.spv_id = o.UserId
LEFT join t_gn_policy p on f.PolicyNumber = p.PolicyNumber
LEFT join t_gn_productplan q on p.ProductPlanId = q.ProductPlanId
LEFT join t_gn_product m on q.ProductId = m.ProductId
LEFT join t_gn_payer r on a.CustomerId = r.CustomerId


where d.CampaignStartDate between '2014-05-01 00:00:00' and '2014-05-30 23:59:59' 
order by d.CampaignId

