TravellingSalesMan = {
  arrMaps : [],
  arrAlgorithms : [],
  arrStateGenerators : [],

  sURL : '/index.php',

  initialize : function(jsonInterfaceConfiguration) {
    this.arrMaps = jsonInterfaceConfiguration.maps;
    this.arrAlgorithms = jsonInterfaceConfiguration.algorithms;
    this.arrStateGenerators = jsonInterfaceConfiguration.stateGenerators;
    this.pageView.renderPage();
    this.communicationsPanel.displayMessage('Page now fully loaded', 'success');
  },

  pageView : {
    renderPage : function() {
      var sHTML = '';
      sHTML += '<div class="container">';
      /* A communication panel */
      sHTML += '<div id="communications-panel"></div>';

      /* The main navigation */
      sHTML += '<!-- Nav tabs -->';
      sHTML += '<ul class="nav nav-tabs" role="tablist">';
      sHTML += '  <li role="presentation" class="active"><a href="#explanation-panel" role="tab" data-toggle="tab">Explanation</a></li>';
      sHTML += '  <li role="presentation"><a href="#settings-panel" role="tab" data-toggle="tab">Settings</a></li>';
      sHTML += '  <li role="presentation"><a href="#results-panel" role="tab" data-toggle="tab">Results</a></li>';
      sHTML += '</ul>';

      /* The page content */
      sHTML += '<!-- Content -->';
      sHTML += '<div class="tab-content">';
      sHTML += '  <div role="tabpanel" class="tab-pane fade in active" id="explanation-panel">'+TravellingSalesMan.explanationPanel.getPanelMarkup()+'</div>';
      sHTML += '  <div role="tabpanel" class="tab-pane fade" id="settings-panel">'+TravellingSalesMan.settingsPanel.getPanelMarkup()+'</div>';
      sHTML += '  <div role="tabpanel" class="tab-pane fade" id="results-panel">'+TravellingSalesMan.resultsPanel.getPanelMarkup()+'</div>';
      sHTML += '</div>';

      sHTML += '</div>';
      $('body').append(sHTML);

      this.setListeners();
    },
    setListeners : function() {
      TravellingSalesMan.settingsPanel.setListeners();
      TravellingSalesMan.resultsPanel.setListeners();
    }
  },

  explanationPanel : {
    getPanelMarkup : function() {
      var sHTML = '<h2>The Travelling Salesman</h2>';
      sHTML += '<blockquote>';
      sHTML += '  <p>The Travelling Salesman Problem (often called TSP) is a classic algorithmic problem in the field of computer science. It is focused on optimization. In this context better solution often means a solution that is cheaper. TSP is a mathematical problem. It is most easily expressed as a graph describing the locations of a set of nodes.</p>';
      sHTML += '  <footer>The Travelling Salesman problem page in <cite title="Source Title">Wikipedia</cite></footer>';
      sHTML += '</blockquote>';
      sHTML += '<p>This interface allows you to select various maps, algorithms and stategenerators to use on the problem in the settings tab. The results are then stored and, thanks to the Google chart API, turned into graphs on the results page.</p>';
      return sHTML;
    },
    renderPanel : function() {
      $('#explanation-panel').html(this.getPanelMarkup());
    },
    setListeners : function() {}
  },

  settingsPanel : {
    getPanelMarkup : function() {
      var sHTML = '';
      sHTML += '<br>';
      sHTML += '<div class="form-horizontal">';

      /* A drop down allowing the user to select which map to use */
      sHTML += '  <div class="form-group">';
      sHTML += '    <label for="use_map" class="col-sm-3 control-label">Map</label>';
      sHTML += '    <div class="col-sm-9">';
      sHTML += '      <select id="use_map" class="form-control">';
      for(var key in TravellingSalesMan.arrMaps) {
        sHTML += '<option value="'+TravellingSalesMan.arrMaps[key]+'">'+TravellingSalesMan.arrMaps[key]+'</option>';
      }
      sHTML += '      </select>';
      sHTML += '    </div>';
      sHTML += '  </div>';

      /* A drop down allowing the user to select which algorithm to use */
      sHTML += '  <div class="form-group">';
      sHTML += '    <label for="use_algorithm" class="col-sm-3 control-label">Algorithm</label>';
      sHTML += '    <div class="col-sm-9">';
      sHTML += '      <select id="use_algorithm" class="form-control">';
      for(var key in TravellingSalesMan.arrAlgorithms) {
        sHTML += '<option value="'+TravellingSalesMan.arrAlgorithms[key]+'">'+TravellingSalesMan.arrAlgorithms[key]+'</option>';
      }
      sHTML += '      </select>';
      sHTML += '    </div>';
      sHTML += '  </div>';

      /* A drop down allowing the user to select which stategenerator to use */
      sHTML += '  <div class="form-group">';
      sHTML += '    <label for="use_stategenerator" class="col-sm-3 control-label">State Generator</label>';
      sHTML += '    <div class="col-sm-9">';
      sHTML += '      <select id="use_stategenerator" class="form-control">';
      for(var key in TravellingSalesMan.arrStateGenerators) {
        sHTML += '<option value="'+TravellingSalesMan.arrStateGenerators[key]+'">'+TravellingSalesMan.arrStateGenerators[key]+'</option>';
      }
      sHTML += '      </select>';
      sHTML += '    </div>';
      sHTML += '  </div>';

      /* A drop down allowing the user to select how many times the algorithm
       * should be run */
      sHTML += '  <div class="form-group">';
      sHTML += '    <label for="run_count" class="col-sm-3 control-label">Run X times</label>';
      sHTML += '    <div class="col-sm-9">';
      sHTML += '      <select id="run_count" class="form-control">';
      sHTML += '        <option value="1">Run algorithm once</option>';
      for(var i = 2; i < 100; i++) {
        sHTML += '<option value="'+i+'">Run algorithm '+i+' times</option>';
      }
      sHTML += '      </select>';
      sHTML += '    </div>';
      sHTML += '  </div>';

      /* The button allowing the user to request the stats */
      sHTML += '  <div class="form-group">';
      sHTML += '    <div class="col-sm-offset-3 col-sm-9">';
      sHTML += '      <button id="runAlgorithm" type="btn btn-default" class="btn btn-default">Run Algorithm</button>';
      sHTML += '    </div>';
      sHTML += '  </div>';
      sHTML += '</div>';

      sHTML += '<hr>';

      return sHTML;
    },
    renderPanel : function() {
      $('#settings-panel').html(this.getPanelMarkup());
      this.setListeners();
    },
    setListeners : function() {
      $('#runAlgorithm').on('click', function() {
        $.ajax({
          url  : TravellingSalesMan.sURL,
          data : {
            action:'getSolution',
            runCount:$('#run_count').val(),
            sMapName:$('#use_map').val(),
            sAlgorithmName :$('#use_algorithm').val(),
            sStateGeneratorName :$('#use_stategenerator').val()
          }
        }).done(function(data){
          data = $.parseJSON(data);

          /* Parse the data returned for the shortest route in this run */
          var nShortestRouteLength = 999999;
          var sShortestRoute = '';
          for (var run in data.arrRunData) {
            for (var nRoute in data.arrRunData[run].states) {
              if (parseInt(data.arrRunData[run].states[nRoute].length) < nShortestRouteLength) {
                nShortestRouteLength = data.arrRunData[run].states[nRoute].length;
                sShortestRoute = data.arrRunData[run].states[nRoute].route;
              }
            }
          }

          var sMessage = 'Shortest route found ['+sShortestRoute+'] was '+nShortestRouteLength+' long.';
          TravellingSalesMan.communicationsPanel.displayMessage(sMessage, 'success');
        });
      });
    }
  },

  resultsPanel : {
    getPanelMarkup : function() {
      var sHTML = '';
      sHTML += '<br>';
      sHTML += '<div class="form-horizontal">';

      /* A drop down allowing the user to select which map to use */
      sHTML += '  <div class="form-group">';
      sHTML += '    <label for="use_map" class="col-sm-3 control-label">Map</label>';
      sHTML += '    <div class="col-sm-9">';
      sHTML += '      <select id="use_map" class="form-control">';
      for(var key in TravellingSalesMan.arrMaps) {
        sHTML += '<option value="'+TravellingSalesMan.arrMaps[key]+'">'+TravellingSalesMan.arrMaps[key]+'</option>';
      }
      sHTML += '      </select>';
      sHTML += '    </div>';
      sHTML += '  </div>';

      /* A drop down allowing the user to select which algorithm to use */
      sHTML += '  <div class="form-group">';
      sHTML += '    <label for="use_algorithm" class="col-sm-3 control-label">Algorithm</label>';
      sHTML += '    <div class="col-sm-9">';
      sHTML += '      <select id="use_algorithm" class="form-control">';
      for(var key in TravellingSalesMan.arrAlgorithms) {
        sHTML += '<option value="'+TravellingSalesMan.arrAlgorithms[key]+'">'+TravellingSalesMan.arrAlgorithms[key]+'</option>';
      }
      sHTML += '      </select>';
      sHTML += '    </div>';
      sHTML += '  </div>';

      /* A drop down allowing the user to select which stategenerator to use */
      sHTML += '  <div class="form-group">';
      sHTML += '    <label for="use_stategenerator" class="col-sm-3 control-label">State Generator</label>';
      sHTML += '    <div class="col-sm-9">';
      sHTML += '      <select id="use_stategenerator" class="form-control">';
      for(var key in TravellingSalesMan.arrStateGenerators) {
        sHTML += '<option value="'+TravellingSalesMan.arrStateGenerators[key]+'">'+TravellingSalesMan.arrStateGenerators[key]+'</option>';
      }
      sHTML += '      </select>';
      sHTML += '    </div>';
      sHTML += '  </div>';

      /* The button allowing the user to request the stats */
      sHTML += '  <div class="form-group">';
      sHTML += '    <div class="col-sm-offset-3 col-sm-9">';
      sHTML += '      <button id="getStats" type="btn btn-default" class="btn btn-default">Fetch Stats</button>';
      sHTML += '    </div>';
      sHTML += '  </div>';
      sHTML += '</div>';

      sHTML += '<hr>';

      sHTML += '<div id="stats_div"></div>';
      sHTML += '<div id="chart_div"></div>';
      sHTML += '<div id="table_div"></div>';

      return sHTML;
    },
    renderPanel : function() {
      $('#result-panel').html(this.getPanelMarkup());
    },
    setListeners : function() {
      $('#getStats').on('click', function() {
        $.ajax({
          url  : TravellingSalesMan.sURL,
          data : {'action':'getStats'}
        }).done(function(responseData){
          responseData = $.parseJSON(responseData);
          TravellingSalesMan.resultsPanel.renderStats(responseData.data);
          TravellingSalesMan.communicationsPanel.displayMessage('Stats fetched', 'success');
        });
      });
    },
    renderStats : function(objData) {
      var sHTML = '';
      sHTML += '<p>Map: <b>'+objData.map+'</b></p>';
      sHTML += '<p>Algorithm: <b>'+objData.algorithm+'</b></p>';
      sHTML += '<p>State Generator: <b>'+objData.stategenerator+'</b></p>';
      sHTML += '<p>Shortest Route Found: <b>'+objData.shortest_route+'</b></p>';
      sHTML += '<p>Length of Shortest Route Found: <b>'+objData.shortest_route_length+'</b></p>';
      $('#stats_div').html(sHTML);
      TravellingSalesMan.resultsPanel.renderChart(objData);
      TravellingSalesMan.resultsPanel.renderTable(objData);
    },
    renderChart : function(objChartData) {
      var arrTemp = [['State Count', 'Average Distance']];
      for (var iteration in objChartData.average_length_of_iteration) {
        arrTemp.push([parseInt(iteration), parseFloat(objChartData.average_length_of_iteration[iteration])]);
      }
      var data = google.visualization.arrayToDataTable(arrTemp);
      var options = {
        title: 'Average distance reached per iteration',
        legend: { position: 'bottom' },
        series: {
          0: {targetAxisIndex: 0},
          1: {targetAxisIndex: 1}
        },
        vAxes: {
          0: {title: 'Distance'}
        },
        hAxes:{
          0: {title: 'Iteration'}
        }
      };
      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    },
    renderTable : function(objTableData) {
      console.log(objTableData);
      var sHTML = '';
      sHTML += '<h3>Average Distance Across All Runs</h3>';
      sHTML += '<table class="table table-striped">';
      sHTML += '  <thead>';
      sHTML += '    <tr>';
      sHTML += '      <th>#</th>';
      sHTML += '      <th>Distance</th>';
      sHTML += '      <th>Times this iteration was reached</th>';
      sHTML += '    </tr>';
      sHTML += '  </thead>';

      sHTML += '  <tbody>';
      for (var key in objTableData.average_length_of_iteration) {
        sHTML += '    <tr>';
        sHTML += '      <th>'+key+'</th>';
        sHTML += '      <th>'+objTableData.average_length_of_iteration[key]+'</th>';
        sHTML += '      <th>'+objTableData.count_of_iteration[key]+'</th>';
        sHTML += '    </tr>';
      }
      sHTML += '  </tbody>';

      sHTML += '</table>';
      $('#table_div').html(sHTML);
    }
  },

  communicationsPanel : {
    displayMessage : function(sMessage, sType) {
      this.renderPanel(sMessage, sType);
    },
    getPanelMarkup : function(sMessage, sType) {
      var sHTML = '';
      sHTML += '<div class="alert alert-'+sType+'" role="alert">';
      sHTML += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
      sHTML += sMessage;
      sHTML += '</div>';
      return sHTML;
    },
    renderPanel : function(sMessage, sType) {
      $('#communications-panel').html(this.getPanelMarkup(sMessage, sType));
    },
    setListeners : function() {}
  }
}

$(document).ready(function(){
  TravellingSalesMan.initialize(jsonInterfaceConfiguration);
});