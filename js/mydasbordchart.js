/**! 
	Thank to god 
	This class modified hightchart
	you can add function or properties
	for fleksibelity your realtime chart
	create@Omens<rahmattullah>
**/

var chart;

var chartJs = {
		chartFile :'act_load_chart.php?act=get_data',
		chartData :{'aprv':0,'decl':0,'folw':0,'empt':0,'tots':0,'cmp':'-','noct':0},
		
		cahrtyAxis:{'min':0,'max':0},
		eventLoad:function(){
			var data = this.chartPost(this.chartFile);
			var i = data.split('|');
					this.cahrtyAxis['max'] = parseFloat(i[0]);
					this.chartData['empt'] = parseFloat(i[1]);
					this.chartData['folw'] = parseFloat(i[2]);
					this.chartData['aprv'] = parseFloat(i[3]);
					this.chartData['decl'] = parseFloat(i[4]);
					this.chartData['tots'] = parseFloat(i[5]);
					this.chartData['cmp']  = i[6].toString();
					this.chartData['noct'] = parseFloat(i[7]);
					this.create();
		},
		create:function(){
			var colors = Highcharts.getOptions().colors,
			chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						defaultSeriesType: 'column'
					},
					title: {
						text: '<p style="font-size:9px;"> Target ( '+this.chartData['tots'].toString()+' ) Campaign : ( '+this.chartData['cmp']+' )</p>',
						enable:false
					},
					
					xAxis: {
						categories: [
									 '<p style="font-size:8px;">APRV</p>',
									 '<p style="font-size:8px;">RDPC</p>',
									 '<p style="font-size:8px;">FOLW</p>',
									 '<p style="font-size:8px;">NOCT</p>',
									 '<p style="font-size:8px;">NULL</p>'
									 ]
						
					},
					yAxis: {min:this.cahrtyAxis['min'],max:this.cahrtyAxis['max'],
					title: {text: ''}},
					tooltip: {
						
						formatter: function(){
							return ' <p style="font-size:11px;">'+this.x +': '+ this.y+'</p>';
						}
					},
					plotOptions: {column: {pointPadding: 0.1,borderWidth:1,borderColor:"#CCC"}},
				        series: [{showInLegend:false,name: 'Tokyo',
						data: [
								this.chartData['aprv'], 
								this.chartData['decl'],
								this.chartData['folw'],
								this.chartData['noct'],
								this.chartData['empt']
							  ]}]
			});
			
			
	  },
	  
	 chartPost:function(theUrl){
		var xmlGet = null;
		xmlGet = new XMLHttpRequest();
		xmlGet.open( "GET", theUrl, false );
			xmlGet.send( null );
		return xmlGet.responseText;
	}
	
	

}