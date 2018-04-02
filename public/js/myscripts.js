
  const vm = new Vue({
			  el: '#app',   
			  //Mock data for the value of BTC in USD
			  data: { 
				users:[],
				items:[],
				graficoBar:false,
				graficoPie:false,
				relatorio:false
			  },
			  mounted:function(){
			  	this.usuarios();
			  },
			  methods:{
					getData:function(){
						this.relatorio=true;
						this.graficoBar=false;
						this.graficoPie=false;
					  var dateFrom = $('#dateFrom').val();
					  var dateTo = $('#dateTo').val();
					  var selected=[];
					  $('#multiselect_to > option').each(function(){
						selected.push(this.value);
					  });
						if(selected.length > 0 ){
							params = {
							  dateFrom:dateFrom,
							  dateTo:dateTo,
							  users:selected
							  };

						let url = 'relatorio';
						this.$http.get(url,{params}).then(response => {
						 	this.items = response.body;
						  console.log(this.items);
						}).catch(function (error) {
						   console.log('Error: ' + error);
						});
					  }
					  else{
						alert('selecione uno mas usuarios');
					  } 
					},
					lucro:function(receita,custo,comissao){
						//console.log(receita+':'+custo+':'+comissao);
						return (receita - (custo + comissao))
					},
					usuarios:function(){
						let url = 'usuarios';
						this.$http.get(url).then(response => {
						  this.users = response.body;
						}).catch(function (error) {
						   console.log('Error: ' + error);
						});
					},
					getGraficoBar:function(){ 
						this.relatorio=false;
						this.graficoBar=true;
						this.graficoPie=false; 
						var dateFrom = $('#dateFrom').val();
						var dateTo = $('#dateTo').val();
						var selected=[];
						$('#multiselect_to > option').each(function(){
							selected.push(this.value);
						  });
					  	if(selected.length > 0 ){
							params = {
							  dateFrom:dateFrom,
							  dateTo:dateTo,
							  users:selected
							};
							let url = 'bar_chart';
							this.$http.get(url,{params}).then(res => {
							  graficoBar(res.body);
							}).catch(function (error) {
							   console.log('Error: ' + error);
							});
							
						}
						else{
							alert('selecione uno mas usuarios');
						} 
					},
					getGraficoPie:function(){  
						this.relatorio=false;
						this.graficoBar=false;
						this.graficoPie=true;
						var dateFrom = $('#dateFrom').val();
						var dateTo = $('#dateTo').val();
						var selected=[];
						$('#multiselect_to > option').each(function(){
							selected.push(this.value);
						  });
					  	if(selected.length > 0 ){
							params = {
							  dateFrom:dateFrom,
							  dateTo:dateTo,
							  users:selected
							};
							let url = 'pie_chart';
							this.$http.get(url,{params}).then(res => {
							  graficoPie(res.body);
							}).catch(function (error) {
							   console.log('Error: ' + error);
							});
							
						}
						else{
							alert('selecione uno mas usuarios');
						} 
					}

			  }
		  });




$('#multiselect').multiselect();


function graficoBar(params){

   Highcharts.chart('grafico_bar', {
	chart: {
		type: 'column'
	},
	title: {
		text: 'receita liquida (ganancias netas) por Cada Usuario'
	},
	xAxis: {
		categories: params.categories,
		crosshair: true
	},
	yAxis: {
		min: 0,
		title: {
			text: 'Receita Liquida'
		}
	},
	tooltip: {
		headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
		pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			'<td style="padding:0"><b>{point.y:.1f} Receita Liquida</b></td></tr>',
		footerFormat: '</table>',
		shared: true,
		useHTML: true
	},
	plotOptions: {
		column: {
			pointPadding: 0.2,
			borderWidth: 0
		}
	},
	series: params.series,
}); 
}
function graficoPie(params){
	Highcharts.chart('grafico_pie', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'porcentaje de receita líquida (ganancias netas) generada por cada consultor'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    series: [{
        name: 'receita líquida',
        colorByPoint: true,
        data: params.series,
    }]
});
}




