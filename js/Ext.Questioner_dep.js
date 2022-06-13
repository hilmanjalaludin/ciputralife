/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */

Ext.DOM.OnUnloadWindow = function()
{
	if( window.opener )
	{
		window.opener.Searchquestion();
	}
}
 
Ext.DOM.CloseSelfWindow = function(WinName) {	
	if( window.opener )
	{
		window.opener.Searchquestion(); 
		window.close(WinName);
	}	
}

/* 
 * @ Ext /EUI :	frame work ready function 
 * @ Will render on global definition 
 */
 
$(document).ready(function(){
	var HeightWindow = parseInt($(window).height()-200);
	if( HeightWindow )
	{
		$("#container_question").css({height : HeightWindow+"px","overflow":'auto'});
	}
	
	var Quest =(Ext.Ajax({ 
				url 	: '../class/class.questioner.php', 
				method :'GET', 
				param 	: { 
				action	: '_get_tag_question'
						}
				}).json() );
	var ans =(Ext.Ajax({ 
				url 	: '../class/class.questioner.php', 
				method :'GET', 
				param 	: { 
				action	: '_get_tag_answer'
						}
				}).json() );
				
	var input_user = (Ext.Ajax({ 
				url 	: '../class/class.questioner.php', 
				method :'GET', 
				param 	: { 
				action	: '_get_type_input'
						}
				}).json() );
				
	var li_except = (Ext.Ajax({ 
				url 	: '../class/class.questioner.php', 
				method :'GET', 
				param 	: { 
				action	: '_get_exception_ans'
						}
				}).json() );
	
	// var store=[[]];
	var excep_ans = "4";
	var excep_quest="3";
	var no_pertanyaan=1,no_jawaban=1,no_except=1;
	var limit_question = 35;
	var limit_lbl_ans = 35;
	var limit_lbl_ex_ans = 35;
	Ext.DOM.AddAnswer = function (no)
	{
		var answer,rule;
		/*** Fail
		var show_no_question = no_pertanyaan - 1;
		
		if(show_no_question != no)
		{
			// alert("Please set answer label in question "+show_no_question);
			// return false;
			no_jawaban = parseInt( Ext.Cmp('incr_jwb_'+no).getValue() ) + 1;
			if(no_jawaban > limit_lbl_ans)
			{
				alert("Sorry, Maximum Answer Label is "+limit_lbl_ans);
				return false;
			}
			tag = ans.answer;
			var res = tag.replace(/REPLACENOQUESTION/g,no);
			var result = res.replace(/REPLACENOANSWER/g,no_jawaban);
			// alert(result);
			$("#jawaban_"+no).append("<li>"+result+"</li>");
			Ext.Cmp('incr_jwb_'+no).setValue(no_jawaban);
		}
		else
		{
			no_jawaban++;
			if(no_jawaban > limit_lbl_ans)
			{
				alert("Sorry, Maximum Answer Label is "+limit_lbl_ans);
				return false;
			}
			// alert(no_jawaban);
			tag = ans.answer;
			var res = tag.replace(/REPLACENOQUESTION/g,no);
			var result = res.replace(/REPLACENOANSWER/g,no_jawaban);
			// alert(result);
			$("#jawaban_"+no).append("<li>"+result+"</li>");
			Ext.Cmp('incr_jwb_'+no).setValue(no_jawaban);
		}
		END of Fail ***/
		
		no_jawaban = parseInt( Ext.Cmp('incr_jwb_'+no).getValue() ) + 1;
		if(no_jawaban > limit_lbl_ans)
		{
			alert("Sorry, Maximum Answer Label is "+limit_lbl_ans);
			return false;
		}
		answer = ans.answer;
		rule = ans.rule;
		var res_ans = answer.replace(/REPLACENOQUESTION/g,no);
		var tag_ans = res_ans.replace(/REPLACENOANSWER/g,no_jawaban);
		
		var res_rule = rule.replace(/REPLACENOQUESTION/g,no);
		var tag_rule = res_rule.replace(/REPLACENOANSWER/g,no_jawaban);
		// alert(result);
		$("#jawaban_"+no).append("<li>"+tag_ans+"</li>");
		$("#rule_"+no).append("<li>"+tag_rule+"</li>");
		Ext.Cmp('incr_jwb_'+no).setValue(no_jawaban);
	}
	
	Ext.DOM.AddQuestion = function()
	{
		
		if(no_pertanyaan > limit_question)
		{
			alert("Sorry, Maximum Question is "+limit_question);
			return false;
		}
		no_jawaban=1;
		var tag = Quest.question;
		var res = tag.replace(/REPLACENOQUESTION/g,no_pertanyaan);
		var result = res.replace(/REPLACENOANSWER/g,no_jawaban);
		$("#pertanyaan").append("<li>"+result+"</li>");
		
		Ext.Cmp('count_pertanyaan').setValue(no_pertanyaan);
		Ext.Cmp('incr_jwb_'+no_pertanyaan).setValue(no_jawaban);
		no_pertanyaan++;
		
		// alert(no_jawaban);
		// console.log(Quest);
		
	}
	
	Ext.DOM.SaveSetupQuestioner = function()
	{
		if(Ext.DOM.validation())
		{
			var VAR_POST_DATA = [];
			VAR_POST_DATA['action'] = '_save_questioner';
			VAR_POST_DATA['ProductId']  = Ext.Cmp('Product').getValue();
			VAR_POST_DATA['QuestType']  = Ext.Cmp('Quest_type').getValue();
			VAR_POST_DATA['QuestionDecs']  = Ext.Cmp('Question_decs').getValue();
			
			Ext.Ajax
			({
				url 	: '../class/class.questioner.php', 
				method 	: 'GET',
				param 	: ( Ext.Join(new Array(VAR_POST_DATA, Ext.Serialize('form_setup_questioner').getElement())).object() ),
				ERROR 	: function(e)
				{
					var ERR = JSON.parse(e.target.responseText);
					if(ERR.status==1){
						Ext.Msg("Add Questioner").Success();
						Ext.DOM.CloseSelfWindow('WinAddQuestioner');
						// Ext.DOM.OnUnloadWindow();
					}
					else{
						Ext.Msg("Add Questioner").Failed();
					}
				 
				}
			}).post();
		}
	}
	
	Ext.DOM.change = function (checkbox)
	{
		if( checkbox.checked )
		{
			// alert(checkbox.value);
			 $('#lbl_madatory_'+checkbox.value).text('Mandatory');
			// Ext.Cmp('lbl_madatory_'+checkbox.value).setValue('Mandatory');
		}
		else
		{
			$('#lbl_madatory_'+checkbox.value).text('Not Mandatory');
			// alert("tidak terceek");
			// Ext.Cmp('lbl_madatory_'+checkbox.value).setValue('Not Mandatory');
		}
	}
	
	Ext.DOM.validation = function()
	{
		var valid = true;
		var availQuestion = Ext.Cmp('count_pertanyaan').getValue();
		
		if(Ext.Cmp('Product').getValue()=='')
		{
			alert('Please Choose Product !');
			valid = false;
		}
		else if(Ext.Cmp('Quest_type').getValue()=='')
		{
			alert('Please Choose Question type !');
			valid = false;
		}
		else if (availQuestion=="0")
		{
			alert('Please Add Some Question !');
			valid = false;
		}
		else
		{
			for (var i = 1; i <= availQuestion; i++) 
			{
				var type_answer = Ext.Cmp('Type_answer_'+i).getValue();
				var availAnswer = Ext.Cmp('incr_jwb_'+i).getValue();
				if ( Ext.Cmp('question_'+i).getValue()=="" )
				{
					alert('Question '+i+ ' is empty !');
					valid = false;
					break;
				}
				else if(type_answer=="")
				{
					alert('Please choose Type answer '+i);
					valid = false;
					break;
				}
				else if ( type_answer!="")
				{
					for ( var indx in input_user )
					{
						if (type_answer!=indx)
						{
							for (var j = 1; j <= availAnswer; j++)
							{
								if( Ext.Cmp('answer_'+i+'_'+j).getValue()=="" )
								{
									alert('Answer Label '+j+' in question '+i+' is empty');
									valid = false;
									break;
								}
								if( Ext.Cmp('rule_answer_'+i+'_'+j).getValue()=="" )
								{
									alert('Rule '+j+' in question '+i+' is empty');
									valid = false;
									break;
								}
								if( Ext.Cmp('rule_answer_'+i+'_'+j).getValue()== excep_quest )
								{
									if ( Ext.Cmp('ex_question_'+i+'_'+j).getValue() =="" )
									{
										alert('Exception question '+i+' is empty');
										valid = false;
										break;
									}
								}
								if ( Ext.Cmp('rule_answer_'+i+'_'+j).getValue()== excep_ans )
								{
									if(Ext.Cmp('ex_type_ans_'+i+'_'+j).getValue()=="")
									{
										alert('Type answer in exception ( question ' +i+ ' ) is empty');
										valid = false;
										break;
									}
									var ex_ans_count = Ext.Cmp('except_count_'+i+'_'+j).getValue();
									for (var k = 1; k <= ex_ans_count; k++)
									{
										if(Ext.Cmp('ex_ans_'+i+'_'+j+'_'+k).getValue()=="")
										{
											alert('Answer Label '+k + ' in exception ( question ' +i+ ' ) is empty');
											valid = false;
											break;
										}
										if(Ext.Cmp('ex_rule_'+i+'_'+j+'_'+k).getValue()=="")
										{
											alert('Rule '+k + ' in exception ( question ' +i+ ' ) is empty');
											valid = false;
											break;
										}
									}
								}
								
							}
							if (valid === false)
							{
								break;
							}
						}
					}
					if (valid === false)
					{
						break;
					}
				}
			}
		}
		
		
		return valid;
	}
	
	Ext.DOM.CheckRule = function (no_question,no_jawaban)
	{
		var rule = Ext.Cmp("rule_answer_"+no_question+"_"+no_jawaban).getValue();
		var except_bool = Ext.Cmp("except_bool_"+no_question).getValue();
		var availAnswer = Ext.Cmp('incr_jwb_'+no_question).getValue();

		// console.log("Nilai Rule");
		// console.log(rule);
		// console.log(typeof rule);
		// console.log("Nilai awal except bool");
		// console.log(except_bool);

		// console.log("Nilai awal avail Answer question "+ no_question);
		// console.log(availAnswer);

		if( rule == excep_ans )
		{
			// console.log("call exception answer");
			// console.log(except_bool);
			if(except_bool=="NO")
			{
				// console.log("Memenuhi syarat exception answer");
				Ext.Ajax({
				url: '../class/class.questioner.php',
				method: 'GET',
				param: {
					action: '_get_first_except',
					no_question : no_question,
					no_jawaban : no_jawaban
					}
				}).load("excep_"+no_question);
				Ext.Cmp("except_bool_"+no_question).setValue('YES');
				no_except = 1;
			}
			else
			{
				alert("Exception only for one answer (can\'t choose exception answer)");
				Ext.Cmp("rule_answer_"+no_question+"_"+no_jawaban).setValue('');
			}
		}
		else if (rule == excep_quest)
		{
			// console.log("call exception question");
			// console.log(except_bool);
			if(except_bool=="NO")
			{
				// console.log("Memenuhi syarat exception question");
				Ext.Ajax({
				url: '../class/class.questioner.php',
				method: 'GET',
				param: {
					action: '_get_question_except',
					count_question : Ext.Cmp('count_pertanyaan').getValue(),
					no_question : no_question,
					no_jawaban : no_jawaban
					}
				}).load("excep_"+no_question);
				Ext.Cmp("except_bool_"+no_question).setValue('YES');
			}
			else
			{
				alert("Exception only for one answer (can\'t choose exception question)");
				Ext.Cmp("rule_answer_"+no_question+"_"+no_jawaban).setValue('');
			}
		}
		
		var available_exception = false;
		for (var j = 1; j <= availAnswer; j++)
		{
			// alert("rule_answer_"+no_question+"_"+j);
			var rule_checking = Ext.Cmp("rule_answer_"+no_question+"_"+j).getValue();
			if(rule_checking == excep_ans || rule_checking == excep_quest)
			{
				available_exception = true;
				break;
			}
		}
		if(!available_exception)
		{
			// alert("Tidak ada exception");
			Ext.Cmp("except_bool_"+no_question).setValue("NO");
			Ext.Cmp("excep_"+no_question).setText("None Exception");
		}

	}
	
	Ext.DOM.AddExceptAnswer= function( no_question, no_answer )
	{
		var except_ans,except_rule;
		var no_except = parseInt( Ext.Cmp("except_count_"+no_question+"_"+ no_answer).getValue() ) + 1;

		if(no_except > limit_lbl_ex_ans)
		{
			alert("Sorry, Maximum Answer Label is "+limit_lbl_ex_ans);
			return false;
		}
		except_ans = li_except.ex_answer;
		except_ans = except_ans.replace(/REPLACENOQUESTION/g,no_question);
		except_ans = except_ans.replace(/REPLACENOANSWER/g,no_answer);
		except_ans = except_ans.replace(/REPLACENOEXCEPT/g,no_except);


		except_rule = li_except.ex_rule;
		except_rule = except_rule.replace(/REPLACENOQUESTION/g,no_question);
		except_rule = except_rule.replace(/REPLACENOANSWER/g,no_answer);
		except_rule = except_rule.replace(/REPLACENOEXCEPT/g,no_except);

		$("#ex_jwb_"+no_question).append("<li>"+except_ans+"</li>");

		$("#ex_rule_"+no_question).append("<li>"+except_rule+"</li>");

		Ext.Cmp("except_count_"+no_question+"_"+ no_answer).setValue(no_except);
	}

  // console.log(store);
});

