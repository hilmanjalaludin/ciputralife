var QaScore = ( function(){

	var tools;
	var link;
	
	
	var cal_score = function ()
	{
		var ValScore;
		if(valid_submit())
		{
			var form_data = $("#score_form").serialize();
			$.post(link.score,form_data, function(data, status){
		 	 	if(status==="success")
		 	 	{
		 	 		ValScore=data;
					for( i in tools.score_place)
					{
						$("#score_"+i).val(ValScore["score_"+i]);
					}
		 	 	}
		 	},"json");
			
		}
	};
	
	var get_tools = function()
	{
		$.getJSON( link.tools, function( data ) {
			tools = data;
		});
	};
	
	var set_link = function(link_source)
	{
		link = link_source;
		get_tools();
	};
	
	var submit_action = function()
	{
		var form_data = $("#score_form").serialize();
		
		if(valid_submit())
		{
			
			$.post(link.send,form_data, function(data, status){
		 	 	if(status==="success")
		 	 	{
		 	 		alert(data.message);
		 	 	}
		 	 },"json");
		}
	};
	var valid_submit = function()
	{
		
		var valid=false;
		var mandat = tools.sub_mandat;
		var category = tools.category;
		var sub_category = tools.sub_category;
		
		for( i in category)
		{
			for( j in sub_category[i] )
			{
				if( mandat[i][j] == 1)
				{
					if( $("#qa_quest_"+j).val()== "" )
					{
						alert(sub_category[i][j]+" is empty");
						$("#mark_score_"+j).addClass("warning");
						valid=false;
						$("#qa_quest_"+j).focus();
						return valid;
					}
					else
					{
						valid = true;
						$("#mark_score_"+j).removeClass("warning");
					}
				}
			}
		}
		return valid;
	};
	
	
	
	return {
		CalculateScore	: cal_score,
		SetSource		: set_link,
		SubmitAction	: submit_action
	};
})();