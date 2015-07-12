var BetterAnalyticsAdmin={};jQuery.easing.easeOutExpo=function(a,g,e,h,b){return g==b?e+h:h*(-Math.pow(2,-10*g/b)+1)+e};
!function(a,g,e,h){BetterAnalyticsAdmin.Admin=function(){this.__construct()};BetterAnalyticsAdmin.Admin.prototype={__construct:function(){a(e).ready(function(){a("#better-analytics_tabs.nav-tab-wrapper").length&&BetterAnalyticsAdmin._Admin.init_tabs();"function"==typeof a().tooltipster&&a(".tooltip").tooltipster({contentAsHTML:!0,animation:"grow",interactive:!0,speed:150,maxWidth:600,theme:"tooltipster-better_analytics"});"function"==typeof a().chosen&&(a(".chosen-charts").chosen({search_contains:!0,
width:"80%"}),a(".chosen-select").not("#ba_pick_profile").chosen({search_contains:!0,width:"100%"}),a("#ba_pick_profile.chosen-select").chosen({search_contains:!0,width:"70%"}));a("#ba_realtime").on("change",function(){this.checked?(a("#ba_history select").prop("disabled",!0),a("#ba_history").css("color","grey")):(a("#ba_history select").prop("disabled",!1),a("#ba_history").css("color","inherit"))});a("#ba_api_use_own").length&&(a("#ba_api_use_own").on("change",function(){BetterAnalyticsAdmin._Admin.toggle_credentials()}),
BetterAnalyticsAdmin._Admin.toggle_credentials());a("#ba_pick_profile").on("change",function(){a(this).val()&&a("#ba_property_id").val(a(this).val())});a("#dashboard-widgets #better-analytics select").length&&(a("#dashboard-widgets #better-analytics select,#dashboard-widgets #better-analytics input").on("change",function(){BetterAnalyticsAdmin._Admin.chart_draw()}),BetterAnalyticsAdmin._Admin.chart_draw());a("#Heatmap").length&&(BetterAnalyticsAdmin._Admin.heatmap_draw(),a("#Heatmap + form #parameters input,#Heatmap + form #parameters select").on("change",
function(){BetterAnalyticsAdmin._Admin.get_new_heatmap_data()}));a("#area_chart").length&&(BetterAnalyticsAdmin._Admin.get_new_area_chart_data(),a("#area_chart + form #parameters input,#area_chart + form #parameters select").on("change",function(){BetterAnalyticsAdmin._Admin.get_new_area_chart_data()}));a(".ba_monitor_form").length&&(BetterAnalyticsAdmin._Admin.get_new_monitor_data(),a(".ba_monitor_form input").on("change",function(){BetterAnalyticsAdmin._Admin.get_new_monitor_data()}));if(a(".goals").length&&
history.pushState){var b=g.location.search.indexOf("&id=");0<b&&(b=g.location.origin+g.location.pathname+g.location.search.substring(0,b),history.pushState({url:b},e.title,b))}a(".goal_create").length&&(BetterAnalyticsAdmin._Admin.bind_funnel_delete(),a("#new_step").on("click",BetterAnalyticsAdmin._Admin.bind_funnel_add),a("#ba_type").on("change",BetterAnalyticsAdmin._Admin.goal_type_change),BetterAnalyticsAdmin._Admin.goal_type_change())})},init_tabs:function(){var b=g.location.hash.slice(5);b||
(b=a(".nav-tab:first").prop("id").slice(0,-4));BetterAnalyticsAdmin._Admin.select_tab(b);a("#better-analytics_tabs a").each(function(){a(this).on("click",BetterAnalyticsAdmin._Admin.select_tab)})},select_tab:function(b){b="object"===typeof b?a(b.currentTarget.hash.slice(4)+"-tab"):a("#"+b+"-tab");try{a("#ba_current_tab").val(a(b).prop("id").slice(0,-4))}catch(e){a("#ba_current_tab").val("general")}a(".nav-tab").removeClass("nav-tab-active");b.addClass("nav-tab-active");a(".tab_content").css("display",
"none");a(".group_"+a("#ba_current_tab").val()).not(".api_hideable").fadeIn();BetterAnalyticsAdmin._Admin.toggle_credentials();a(".group_"+a("#ba_current_tab").val()+" .pro").length?a("#better-analytics_sidebar .pro").fadeIn():a("#better-analytics_sidebar .pro").fadeOut()},bind_funnel_delete:function(){a(".funnel .delete").each(function(){a(this).off("click").on("click",function(){a(this).closest("li").fadeOut(400,function(){a(this).remove()})})})},bind_funnel_add:function(){a(".funnel li.funnel_step:first").clone(!0).hide().appendTo(".funnel ol").fadeIn(400).find("input").attr("value",
"").last().parent().remove()},goal_type_change:function(){a(".dynamic_options").fadeOut(400);a("."+a("#ba_type").val()).fadeIn(400)},min:999999999,max:0,range:0,toggle_credentials:function(){a("#ba_api_use_own").prop("checked")&&"api"==a("#ba_current_tab").val()?a(".api_hideable").fadeIn():a(".api_hideable").fadeOut()},heatmap_draw:function(){BetterAnalyticsAdmin._Admin.min=999999999;BetterAnalyticsAdmin._Admin.max=0;a(".cell[data-val]").each(function(b,e){var c=a(e).data("val");BetterAnalyticsAdmin._Admin.min>
c&&(BetterAnalyticsAdmin._Admin.min=c);BetterAnalyticsAdmin._Admin.max<c&&(BetterAnalyticsAdmin._Admin.max=c)});BetterAnalyticsAdmin._Admin.range=BetterAnalyticsAdmin._Admin.max-BetterAnalyticsAdmin._Admin.min;a(".cell[data-val]").each(function(b,e){var c=a(e).data("val");a(e).css("opacity",0==BetterAnalyticsAdmin._Admin.range?1:(c-BetterAnalyticsAdmin._Admin.min)/BetterAnalyticsAdmin._Admin.range)})},get_new_heatmap_data:function(){a(".ba_error").remove();a("#chart_loading").css("opacity",1);a.ajax(ajaxurl,
{method:"POST",data:{action:"better-analytics_heatmaps",metric:a("#ba_metric").val(),segment:a("#ba_segment").val(),weeks:a("#ba_weeks").val(),end:a("#ba_end").val(),page_path:a("#ba_page_path").val()},complete:function(){a("#chart_loading").css("opacity",0)}}).done(function(b,e){void 0===b.error?(a.each(b.heatmap_data,function(b,e){a.each(e,function(d,e){a("#slot"+b+"-"+d).data("val",e)})}),BetterAnalyticsAdmin._Admin.heatmap_draw()):BetterAnalyticsAdmin._Admin.hasResponseError(b,e)})},get_new_area_chart_data:function(){a(".ba_error").remove();
a("#chart_loading").css("opacity",1);a.ajax(ajaxurl,{method:"POST",data:{action:"better-analytics_area_charts",dimension:a("#ba_dimension").val(),time_frame:a("#ba_time_frame").val(),scope:a("#ba_scope").val(),minimum:a("#ba_minimum").val(),chart_type:a("input[name=chart_type]:checked").val(),page_path:a("#ba_page_path").val()},complete:function(){a("#chart_loading").css("opacity",0)}}).done(function(b,g){if(void 0===b.error){a.each(b.chart_data,function(a,c){0<a&&(dateString=String(c[0]),8==dateString.length?
b.chart_data[a][0]=new Date(dateString.slice(0,4),dateString.slice(4,6)-1,dateString.slice(6,8)):6==dateString.length&&(b.chart_data[a][0]=new Date(dateString.slice(0,4),dateString.slice(4,6)-1)))});var c=google.visualization.arrayToDataTable(b.chart_data),f=new google.visualization.NumberFormat({fractionDigits:0});a.each(b.chart_data[0],function(a,b){0<a&&f.format(c,a)});var d={title:b.title,isStacked:b.chart_type,hAxis:{format:""},legend:{position:"bottom"},backgroundColor:"transparent",width:a("#area_chart").width(),
height:.6*a("#area_chart").width()};(new google.visualization.AreaChart(e.getElementById("area_chart"))).draw(c,d)}else BetterAnalyticsAdmin._Admin.hasResponseError(b,g)})},get_new_monitor_data:function(){a(".ba_error").remove();a("#chart_loading").css("opacity",1);a.ajax(ajaxurl,{method:"POST",data:{action:"better-analytics_"+a("#ba_monitor").data("type"),days:a("#ba_days").val(),page_path:a("#ba_page_path").val()},complete:function(){a("#chart_loading").css("opacity",0)}}).done(function(b,g){if(void 0===
b.error){if("object"==typeof b.chart_data){var c=google.visualization.arrayToDataTable(b.chart_data);(new google.visualization.NumberFormat({fractionDigits:0})).format(c,1);var f={width:a("#ba_monitor").width(),allowHtml:!0,page:"enable",pageSize:25};(new google.visualization.Table(e.getElementById("ba_monitor"))).draw(c,f)}}else BetterAnalyticsAdmin._Admin.hasResponseError(b,g)})},last_timeout_id:null,chart_draw:function(){a.ajax(ajaxurl,{method:"POST",data:{action:"better-analytics_charts",metric:a("#ba_metric").val(),
dimension:a("#ba_dimension").val(),days:a("#ba_days").val(),realtime:a("#ba_realtime").prop("checked")?1:0}}).done(function(b,g){if(void 0===b.error)if("object"==typeof b.realtime_data?(BetterAnalyticsAdmin._Admin.last_timeout_id=setTimeout(BetterAnalyticsAdmin._Admin.chart_draw,6E4),a("#ba_chart").slideUp(),a("#ba_realtime_charts").slideDown()):(clearTimeout(BetterAnalyticsAdmin._Admin.last_timeout_id),a("#ba_realtime_charts").slideUp(),a("#ba_chart").slideDown()),"object"==typeof b.realtime_data){var c=
b.realtime_data.users,f=c-a("#ba_rt_users .number").data("number");a("#ba_rt_users .number").data("number",c);0!=f&&(a("#ba_rt_users .number").addClass(0<f?"up":"down"),setTimeout("jQuery('#ba_rt_users .number').removeClass('up down')",1));a("#ba_rt_users .number").animate({val:parseInt(c)},{easing:"easeOutExpo",duration:5E3,step:function(b){a(this).html(parseInt(b).toString().replace(/\B(?=(\d{3})+(?!\d))/g,","))}});if("object"==typeof b.realtime_data.country){var c=google.visualization.arrayToDataTable(b.realtime_data.country),
d=new google.visualization.NumberFormat({fractionDigits:0});d.format(c,1);f={title:b.realtime_data.devices[0][0],animation:{startup:!0},width:a("#ba_rt_map").width(),height:.7*a("#ba_rt_map").width()};d=new google.visualization.GeoChart(e.getElementById("ba_rt_map"));d.draw(c,f)}"object"==typeof b.realtime_data.medium&&(c=google.visualization.arrayToDataTable(b.realtime_data.medium),d=new google.visualization.NumberFormat({fractionDigits:0}),d.format(c,1),f={title:b.realtime_data.medium[0][0],is3D:!0,
animation:{startup:!0},width:a("#ba_rt_medium").width(),height:.7*a("#ba_rt_medium").width()},d=new google.visualization.PieChart(e.getElementById("ba_rt_medium")),d.draw(c,f));"object"==typeof b.realtime_data.devices&&(c=google.visualization.arrayToDataTable(b.realtime_data.devices),d=new google.visualization.NumberFormat({fractionDigits:0}),d.format(c,1),f={title:b.realtime_data.devices[0][0],is3D:!0,animation:{startup:!0},width:a("#ba_rt_device").width(),height:.7*a("#ba_rt_device").width()},d=
new google.visualization.PieChart(e.getElementById("ba_rt_device")),d.draw(c,f));f={width:a("#ba_rt_keywords").width(),allowHtml:!0,page:"enable"};"object"==typeof b.realtime_data.keywords&&(c=google.visualization.arrayToDataTable(b.realtime_data.keywords),d=new google.visualization.NumberFormat({fractionDigits:0}),d.format(c,1),d=new google.visualization.Table(e.getElementById("ba_rt_keywords")),d.draw(c,f));"object"==typeof b.realtime_data.referral_path&&(a.each(b.realtime_data.referral_path,function(a,
c){0<a&&"(not set)"!=c[0]&&(b.realtime_data.referral_path[a]=['<a href="http://'+c[0]+'" target="_blank">'+c[0]+"</a>",c[1]])}),c=google.visualization.arrayToDataTable(b.realtime_data.referral_path),d=new google.visualization.Table(e.getElementById("ba_rt_referral_path")),d.draw(c,f));"object"==typeof b.realtime_data.page_path&&(a.each(b.realtime_data.page_path,function(a,c){0<a&&"(not set)"!=c[0]&&(b.realtime_data.page_path[a]=['<a href="'+c[0]+'" target="_blank">'+c[0]+"</a>",c[1]])}),c=google.visualization.arrayToDataTable(b.realtime_data.page_path),
d=new google.visualization.Table(e.getElementById("ba_rt_page_path")),d.draw(c,f))}else if("p"==b.type)c=google.visualization.arrayToDataTable(b.chart_data),d=new google.visualization.NumberFormat({fractionDigits:0}),d.format(c,1),f={title:b.title,is3D:!0,animation:{startup:!0},width:a("#ba_chart").width(),height:.7*a("#ba_chart").width()},d=new google.visualization.PieChart(e.getElementById("ba_chart")),d.draw(c,f);else if("l"==b.type){var h=[];a.each(b.chart_data,function(a,b){0<a?(dateString=String(b[0]),
h[a]=[new Date(dateString.slice(0,4),dateString.slice(4,6)-1,dateString.slice(6,8)),Number(b[1])]):h[a]=[b[0],b[1]]});c=google.visualization.arrayToDataTable(h);d=new google.visualization.NumberFormat({fractionDigits:0});d.format(c,1);f={animation:{startup:!0},width:a("#ba_chart").width(),height:.7*a("#ba_chart").width(),legend:{position:"bottom"}};d=new google.visualization.LineChart(e.getElementById("ba_chart"));d.draw(c,f)}else"g"==b.type&&(c=google.visualization.arrayToDataTable(b.chart_data),
d=new google.visualization.NumberFormat({fractionDigits:0}),d.format(c,1),f={title:b.title,animation:{startup:!0},width:a("#ba_chart").width(),height:.7*a("#ba_chart").width()},d=new google.visualization.GeoChart(e.getElementById("ba_chart")),d.draw(c,f));else BetterAnalyticsAdmin._Admin.hasResponseError(b,g)})},hasResponseError:function(b,e){a('<div class="ba_error">'+b.error+'<input type="submit" class="dismiss button button-primary" value="Okay"></div>').appendTo("body");a(".ba_error .dismiss").on("click",
function(){a(this).closest(".ba_error").remove()});console.log(b.error)}};BetterAnalyticsAdmin._Admin=new BetterAnalyticsAdmin.Admin}(jQuery,this,document);