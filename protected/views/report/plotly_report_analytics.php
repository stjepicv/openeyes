<style>
  .js-plotly-plot .plotly .modebar{
    right: 20px;
  }
</style>
<div id="<?=$report->graphId();?>_container" class="report-container">
  <?php if (method_exists($report, 'renderSearch')) :?>
        <?= $report->renderSearch(true); ?>
    <?php else : ?>
    <form class="report-search-form mdl-color-text--grey-600" action="/report/reportData">
      <input type="hidden" name="report" value="<?= $report->getApp()->getRequest()->getQuery('report'); ?>" />
      <input type="hidden" name="template" value="<?= $report->getApp()->getRequest()->getQuery('template'); ?>" />
    </form>
    <?php endif;?>
  <div id="<?=$report->graphId();?>" class="chart-container"></div>
</div>
<script>
    var data =  <?= $report->tracesJson();?>;
    var layout = JSON.parse('<?= $report->plotlyConfig();?>');
    const plotly_min_width = 800;
    const plotly_min_height = 650;
    var page_height = $('.oe-analytics').height()-50;
    var layout_height = plotly_min_height > page_height? plotly_min_height : page_height;

    var page_width = $('.analytics-charts').width();
    var layout_width = plotly_min_width > page_width? plotly_min_width : page_width;
    layout['font'] = {
            color: '#fff'
        };
    layout['paper_bgcolor'] = '#101925';
    layout['plot_bgcolor'] = '#101925';
    layout['width'] = layout_width;
    layout['height'] = layout_height;
    layout['xaxis']['mirror'] = true;
    layout['xaxis']['rangemode'] = 'tozero';
    layout['xaxis']['linecolor'] = '#fff';
    layout['yaxis']['linecolor'] = '#fff';
    layout['yaxis']['automargin'] = true;
    layout['yaxis']['mirror'] = true;
    if (layout['yaxis']['showgrid']){
        layout['yaxis']['gridcolor'] = '#aaa';
    }
    if (layout['xaxis']['showgrid']){
        layout['xaxis']['gridcolor'] = '#aaa';
    }
    <?php if (($report->graphId() === 'PcrRiskReport')) {?>
        layout['shapes'][0]['line']['color'] = '#fff';
    <?php }?>
    Plotly.newPlot('<?=$report->graphId();?>',
        data,
        layout,
    {
      modeBarButtonsToRemove: ['sendDataToCloud','zoom2d', 'pan', 'pan2d',
        'autoScale2d', 'select2d', 'lasso2d', 'zoomIn2d', 'zoomOut2d',
        'orbitRotation', 'tableRotation', 'toggleSpikelines',
        'resetScale2d', 'hoverClosestCartesian', 'hoverCompareCartesian'],
      responsive: true,
      displaylogo: false,
    }
  );
    <?php if ($report->graphId() !== 'PcrRiskReport') {?>
  var report  = document.getElementById('<?=$report->graphId()?>');
  report.on('plotly_click',function(data){
          for(var i=0; i < data.points.length; i++){
              if (data.points[i].customdata){
                  $('.analytics-charts').hide();
                  $('.analytics-event-list').show();
                  $('.analytics-event-list-row').hide();
                  $('#js-back-to-chart').show();
                  var showlist = data.points[i].customdata;
                  for (var j=0; j<showlist.length; j++){
                      var id = showlist[j].toString();
                      $('#'+id).show();
                  }
              }
          }
  });
    <?php }?>
  var side_bar_inner_filter =  $('#search-form-report-search-section');
  var search_form = $('#search-form-to-side-bar').html();
  side_bar_inner_filter.html("");
  side_bar_inner_filter.html(search_form);
  $('#search-form-to-side-bar').html("");

    <?php
    if ($report->graphId() === 'OEModule_OphCiExamination_components_RefractiveOutcomeReport') {?>
    $('#refractive-outcome-proc-all').change(function(){
        if (this.checked){
            $(".refractive_outcome_specific_procedure").prop("checked", false);
        }
    });
    $('.refractive_outcome_specific_procedure').change(function(){
        if (this.checked){
            $("#refractive-outcome-proc-all").prop("checked", false);
        }
    });
    <?php }?>
</script>

