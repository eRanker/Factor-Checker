var urlLeadGenerate = "/content/themes/eranker/libs/leadgenerator.php";
var flagAppend1 = false; var flagAppend2 = false; var flagAppend3 = false;
var appendFlag = false;

jQuery(document).ready(function () {
    // create an observer instance
    var observer = new MutationObserver(function() {
        $('.expandtable').click(function(){
            clickExpandTable();
            observer.disconnect();
        });        
    });
    
    // pass in the target node, as well as the observer options
    observer.observe(document.querySelector('#factor-page-in-links'), { attributes: true, childList: true});   
    
    clickExpandTable();
    
    Highcharts.setOptions({
        plotOptions: {
            series: {
                animation: false
            }
        }
    });
        
    var leadsent = false;
    function checkModalLead() {
        if (window.pageYOffset >= 600 && !leadsent) {
            jQuery('#leadGenerator').modal('show');
        }
    }
    jQuery(window).scroll(checkModalLead);
    jQuery(document).ready(checkModalLead);

    function sendLeadGenerate() {
        console.log(urlLeadGenerate);
        jQuery.ajax({
            method: "POST",
            dataType: "json",
            url: urlLeadGenerate,
            data: jQuery('#formLeadGenerator').serialize(),
            success: function (data) {
                if (data && !data.error) {
                    jQuery('#msgleadgenerator').html(data.msg);
                    jQuery('#leadGenerator').modal('hide');
                    leadsent = true;
                } else {
                    jQuery('#msgleadgenerator').html('<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error: </span> ' + data.msg + '</div>');
                }
            },
            fail: function (data) {
                jQuery('#msgleadgenerator').html('Occurred internal error while sending data');
            }
        });

    }

    jQuery("#formLeadGenerator").submit(function (e) {
        e.preventDefault();
        sendLeadGenerate();
        return false;
    });
    
    var reportScoreCircle;

    function reportinit() {

        try {
            var divGoogleMaps = '#map-googlemaps';
            googleMapsMapInit(divGoogleMaps, jQuery(divGoogleMaps).attr("data-googlemaps-latitude"),
                    jQuery(divGoogleMaps).attr("data-googlemaps-longitude"), jQuery(divGoogleMaps).attr("data-googlemaps-title"),
                    jQuery(divGoogleMaps).html());
        } catch (e) {
            console.log(e);
        }

        try {
            var divServerLocationMap = '#mapserverlocation';
            serverLocationMapInit(divServerLocationMap, jQuery(divServerLocationMap).attr("data-serverlocation-latitude"),
                    jQuery(divServerLocationMap).attr("data-serverlocation-longitude"), jQuery(divServerLocationMap).attr("data-serverlocation-accuracy"),
                    jQuery(divServerLocationMap).attr("data-serverlocation-title"), jQuery(divServerLocationMap).html());
        } catch (e) {
            console.log(e);
        }

        try {
            //backlinkspiecharts();
            setTimeout(backlinkspiecharts, 1000);
        } catch (e) {
            console.log(e);
        }
        try {
            //anchorschart();
            setTimeout(anchorschart, 1000);
        } catch (e) {
            console.log(e);
        }

        try {
            //speedanalysispiechartsrequest();
            setTimeout(speedanalysispiechartsrequest, 500);
        } catch (e) {
        }
        
        try {
            //inpagelinkspiecharts();
            setTimeout(inpagelinkspiecharts, 500);
        } catch (e) {
        }

        try {
            //speedanalysispiechartsweight();
            setTimeout(speedanalysispiechartsweight, 500);
        } catch (e) {
        }

        try {
            if (jQuery('#circles') && jQuery('#circles').attr('data-circle-started') != "1") {
                var options = {
                    id: 'circles',
                    radius: 70,
                    value: jQuery('#circles').attr('data-percent'),
                    maxValue: 100,
                    width: 10,
                    text: "",
                    colors: ['#E5E5E5', '#0281C4'],
                    duration: 400,
                    wrpClass: 'circles-wrp',
                    textClass: 'circles-text'
                };
//                if (_e.pdf) {
//                    options["text"] = parseInt(jQuery('#circles').attr('data-percent'));
//                }
                reportScoreCircle = Circles.create(options);
                jQuery('#circles').attr('data-circle-started', 1);
            }
        } catch (e) {
            console.log(e);
        }

        try {
            jQuery(".erankertooltip[title]").tooltip({
                show: {
                    effect: "slideDown",
                    delay: 250
                },
                position: {
                    my: "left top",
                    at: "left bottom"
                },
                placement: "bottom"
            });
        } catch (e) {
            console.log(e);
        }              
    }

    function googleMapsMapInit(div, lat, lon, title, content) {
        jQuery(".erfactor[data-factorready='1'] " + div + "[data-gmapsmapready='false']").each(function (i, e) {
            // Create map
            var mapGoogleMaps = new GMaps({
                div: div,
                scrollwheel: true,
                zoom: 15,
                lat: lat,
                lng: lon
            });

            if (lat !== null && lon !== null) {
                // Create infoWindow
                var infoWindowGmapsLocation = new google.maps.InfoWindow({
                    content: content
                });

                var markerGoogleMaps = mapGoogleMaps.addMarker({
                    lat: lat,
                    lng: lon,
                    title: title,
                    icon: "//www.eranker.com/content/themes/eranker/img/establishment_location-32.png",
                    //infoWindow: infoWindowGmapsLocation
                });

                // This opens the infoWindow
                try {
                    //infoWindowGmapsLocation.open(mapGoogleMaps, markerGoogleMaps);
                } catch (e) {
                    console.log(e);
                }
            }
        });
        jQuery(div).attr('data-gmapsmapready', 'true');

    }

    function serverLocationMapInit(div, lat, lon, accuracy, title, content) {

        jQuery(".erfactor[data-factorready='1'] " + div + "[data-mapready='false']").each(function (i, e) {

            // Create map
            var mapServerLocation = new GMaps({
                div: div,
                scrollwheel: true,
                zoom: 7,
                lat: lat,
                lng: lon
            });

            if (lat !== null && lon !== null) {
                // Create infoWindow
                var infoWindowServerLocation = new google.maps.InfoWindow({
                    content: content
                });


                //Fix accuracy
                if (accuracy <= 0) {
                    accuracy = 30000;
                }

                // Add the circle for this city to the map.
                new google.maps.Circle({
                    center: new google.maps.LatLng(lat, lon),
                    radius: accuracy,
                    strokeColor: "#4293e5",
                    strokeOpacity: 0.3,
                    strokeWeight: 1,
                    fillColor: "#4293e5",
                    fillOpacity: 0.2,
                    map: mapServerLocation.map
                });


                var markerServerLocation = mapServerLocation.addMarker({
                    lat: lat,
                    lng: lon,
                    title: title,
                    icon: "//www.eranker.com/content/themes/eranker/img/datacenter_location-32.png",
                    infoWindow: infoWindowServerLocation
                });

                // This opens the infoWindow
                try {
                    infoWindowServerLocation.open(mapServerLocation, markerServerLocation);
                } catch (e) {
                    console.log(e);
                }
            }

        });

        jQuery(div).attr('data-mapready', 'true');
    }


    function downloadFactorsHTML() {

        reportDownloadRetries++;
        if (reportDownloadRetries > 120 || jQuery(".erfactor[data-factorready='0']").size <= 0) { // retry until finished or 10 minutes
            reportinit();
            return;
        }
        if (console) {
            console.log("Downloading missing factors...");
        }
        var factorList = "";
        jQuery(".erfactor[data-factorready='0']").each(function (idx, el) {
            factorList += jQuery(el).attr('data-id') + ",";
        });
        factorList = factorList.substring(0, factorList.length - 1);


        if (factorList === "") {
            if (console) {
                console.log("Finished download the factors data.");                
            }
            reportinit();
            return;
        }
        var jsonURL = updateQueryStringParameter(updateQueryStringParameter(window.location.href, "factors", factorList), "ajax", "1");
        console.log(jsonURL);
        jQuery.getJSON(jsonURL, function (data) {        
//            updateReportScore(data.score);
            //reload report page if data null
            //break js
            if(data === null){
                console.log('json_encode return null');
                window.location.reload();
            }
            
            if (data.status !== 'DONE') {
                jQuery(".loadingCircle").show();                
                if(appendFlag == false){
                    appendFlag = true;
                    jQuery('.overall-score > p').css('visibility','hidden');
                    jQuery('#circles').css('visibility','hidden');
                    jQuery('.loadingCircleExternal').prepend('<div class="loadingmessage">Loading...</div>');
                }                
                //Try download again in 5 seconds
                setTimeout(function dfact() {
                    downloadFactorsHTML();
                }, 3000);
            } else {
                updateReportScore(data.score);
                jQuery('#circles').css('visibility','visible');
                jQuery('.overall-score > p').css('visibility','visible');
                jQuery(".loadingCircle").hide();    
                jQuery('.loadingmessage').hide();                
                if (console) {
                    console.log("Finished download the factors data.");
                }
            }
            jQuery.each(data, function (index, value) {
                console.log("Factor '" + index + "' loaded from ajax...");
                
                jQuery('.erfactor[data-id="' + index + '"]').attr('data-factorready', '1');

                if (index === "score" || index === "status") {
                    return;
                }
                var section = jQuery('.erfactor[data-id="' + index + '"]');
                                            
                if (index == 'backlinks') {
                    section.find(".factor-data").html('');
                    section.find(".factor-data-backlinks").html(value.html);
                } else {                   
                    section.find(".factor-data").html(value.html);
                }

                jQuery(".printscreen").html('<img id="sitescreen" alt="Website Screenshot" src="' + data.score.thumbnail + '">');

                var statusclass = "info";
                switch (value.status) {
                    case "RED":
                    case "MISSING":
                        statusclass = 'times';
                        break;
                    case "ORANGE":
                        statusclass = 'minus';
                        break;
                    case "GREEN":
                        statusclass = 'check';
                        break;
                    case "NEUTRAL":
                        statusclass = 'info';
                        break;
                    default:
                        statusclass = "question-circle";
                        break;
                }
                var statuscolor = value.status.toLowerCase();

                section.find(".factor-name-inside").html('<i class="fa fa-' + statusclass + ' ' + statuscolor + '"></i> ' + value.friendly_name);

            });
            reportinit();
            pdfIsmobileCond();
        }).fail(function () {
            //If an error happens, try download again in 10 seconds
            setTimeout(function dfact() {
                downloadFactorsHTML();
            }, 5000);
            reportinit();
        });
    }


    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
            return uri + separator + key + "=" + value;
        }
    }

    var reportDownloadRetries = 0;

    function updateReportScore(value) {        
        if (console) {
            console.log("Updating scores...");
        }
        reportScoreCircle.update(Math.round(value.percentage), 300);

        jQuery('.superreport-seo .overall-score .reportfinalscore').html(Math.round(value.percentage));
        jQuery('#rating-stars .rating-stars').css('width', (Math.round(value.percentage) / 10 * 10.6) + 'px');
        //Multi colors not implemented yet....
        //reportScoreCircle.updateColors(['#E5E5E5', '#0281C4']);

        var total = value.factors.green + value.factors.orange + value.factors.red + value.factors.missing;

        jQuery('.score-table .factors-score .green .factor-score span').html(value.factors.green);
        jQuery('.score-table .factors-score .green .factorbar').css('width', Math.round((value.factors.green / total) * 100) + '%');
        jQuery('.score-table .factors-score .orange .factor-score span').html(value.factors.orange);
        jQuery('.score-table .factors-score .orange .factorbar').css('width', Math.round((value.factors.orange / total) * 100) + '%');
        jQuery('.score-table .factors-score .red .factor-score span').html(value.factors.red);
        jQuery('.score-table .factors-score .red .factorbar').css('width', Math.round((value.factors.red / total) * 100) + '%');
    }


    function printSeoReport() {
        jQuery("#erreport").print();
    }

    function backlinkspiecharts() {
        if(_e.mobileflag === true){
            var pieOptions = {
                size: '100px',                        
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    format: '<b>{point.name}</b>: {point.y}',
                    distance: 20,
                    color: 'black'
                }
            };
        }else{
            var pieOptions = {                                               
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false,
                    format: '<b>{point.name}</b>: {point.y}',
                    distance: 20,
                    color: 'black'
                }
            };          
        }
                
        jQuery(".backlinkchart[data-chartready='false']").each(function (i, e) {

            jQuery(this).highcharts({
                chart: {
                    animation: false,
                    plotBackgroundColor: 'transparent',
                    plotBorderWidth: null,
                    plotShadow: false,
                    backgroundColor: 'transparent'
                },
                title: {
                    text: jQuery(this).attr('data-title1') + ' vs ' + jQuery(this).attr('data-title2'),
                    margin: 5
                },
                colors: ['#0281C4', '#FF9000', '#04B974', '#F45B5B'],
                credits: {
                    enabled: false
                },
                subtitle: {
                    text: "Total: " + (parseInt(jQuery(this).attr('data-value1')) + parseInt(jQuery(this).attr('data-value2')))
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'bottom',
                    enabled: true
                },
                exporting: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: '<b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                plotOptions: {
                    pie: pieOptions
                },
                series: [{
                        type: 'pie',
                        name: jQuery(this).attr('data-title1') + ' ' + jQuery(this).attr('data-title2'),
                        showInLegend: true,
                        data: [
                            {
                                name: jQuery(this).attr('data-title1'),
                                y: parseInt(jQuery(this).attr('data-value1')),
                                sliced: true,
                                selected: true
                            },
                            {
                                name: jQuery(this).attr('data-title2'),
                                y: parseInt(jQuery(this).attr('data-value2')),
                                sliced: false,
                                selected: false
                            }
                        ]
                    }]
            });

            jQuery(this).attr("data-chartready", "true");
        });
    }
    
    function inpagelinkspiecharts() {
        if(typeof $('.chartinpagelinks').attr('data-total') !== "undefined" && $('.chartinpagelinks').attr('data-total') !== "0"){
            jQuery('.chartinpagelinks[data-chartready=\"false\"]').highcharts({
                chart: {
                    animation: false,
                    plotBackgroundColor: 'transparent',
                    plotBorderWidth: null,
                    plotShadow: false,
                    backgroundColor: 'transparent'
                },
                title: {
                    text: 'In Page Links',
                    margin: 0
                },
                colors: ['#FF9000', '#0281C4', '#04B974',  '#F45B5B', '#444444', '#5F65E0'],
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                },
                credits: {
                    enabled: false
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'bottom',                            
                    enabled: false
                },
                exporting:{
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                           enabled: true,
                           format: '<b>{point.name}</b>: {point.percentage:.1f}%',
                           color: 'black',
                           distance: 20
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'In Page Links',
                    showInLegend: false,
                    data: [['External Follow',parseInt($('.chartinpagelinks').attr('data-external_follow'))],['External NoFollow',parseInt($('.chartinpagelinks').attr('data-external_nofollow'))],['Internal',parseInt($('.chartinpagelinks').attr('data-internal'))]]
                }]
            });

            jQuery('.chartinpagelinks').attr('data-chartready','true');
        }        
    }

    function anchorschart() {
        if(_e.mobileflag){
            var pieOptions = {
                size: '70px',
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    formatter: function(){
                        return '<b>'+ this.point.name.toString().substring(0,10) +'...</b>: ' + this.point.y;
                    },                            
                    distance: 20,
                    color: 'black'
                }
            };
            var chartOptions = {
                margin: [0, 0, 0, 0],
                spacingTop: 0,
                spacingBottom: 0,
                spacingLeft: 0,
                spacingRight: 0,
                animation: false,
                plotBackgroundColor: 'transparent',
                plotBorderWidth: null,
                plotShadow: false,
                backgroundColor: 'transparent'
            };
        }else{
            var pieOptions = {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: !_e.mobileflag,
                    //format: '<b>{point.name}</b>: {point.y}',                    
                    formatter: function(){
                        var ret = '';
                        if(this.point.name.toString().length > 28){
                            ret = '<b>'+ this.point.name.toString().substring(0,28) +'...</b>: ' +this.point.y;
                        }else{
                            ret = '<b>'+ this.point.name +'</b>: ' +this.point.y;
                        }
                        return ret;
                    },
                    distance: 20,
                    color: 'black'
                }
            };
            var chartOptions = {             
                animation: false,
                plotBackgroundColor: 'transparent',
                plotBorderWidth: null,
                plotShadow: false,
                backgroundColor: 'transparent'            
            } ;
        }
        
        if(jQuery(".anchorschart").attr("data-totali") !== "0"){
            jQuery(".anchorschart[data-chartready='false']").each(function (i, e) {
                var dataCharts = [];

                for (i = 0; i < jQuery(this).attr('data-totali'); i++) {
                    if (jQuery(this).attr('data-backlinks-' + i).length > 0) {
                        dataCharts[i] = [jQuery(this).attr('data-anchor-' + i), parseInt(jQuery(this).attr('data-backlinks-' + i))];
                    }
                }

                jQuery(this).highcharts({
                    chart:chartOptions,
                    title: {
                        text: 'Anchors Text',
                        margin: 5
                    },
                    credits: {
                        enabled: false
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'center',                   
                        verticalAlign: 'bottom',
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    tooltip: {
                        pointFormat: '<b>{point.y} ({point.percentage:.1f}%)</b>'
                    },
                    plotOptions: {
                        pie: pieOptions
                    },
                    series: [{
                            type: 'pie',
                            name: 'Anchors Text',
                            showInLegend: true,
                            data: dataCharts
                        }]
                });

                jQuery(this).attr("data-chartready", "true");
            });
        }
    }

    var ssnotavailable_icon = "data:image/png;base64,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";
    var ssloading_icon = "data:image/gif;base64,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";

    reportinit();
    downloadFactorsHTML();
    
    pdfIsmobileCond();


    $('#download-pdf').click(function() {
        var downloadPdf = $(this);
        var text = $(this).text();
        
        if (downloadPdf.attr('data-enabled') != 'true') {
            return;
        }
        
        downloadPdf.attr('data-enabled', 'false');
        $(this).text("Generating report...");
        $.post([_e.url, $(this).attr('data-href')].join(''), {
            "patch_lang": _e.lang,
            "patch_backlinks": encodeURIComponent($('#backlinkspie').html()),
            "patch_anchors":encodeURIComponent($('#anchorschart').html()),
            "patch_speedanalysispie":encodeURIComponent($('#speedanalysispiegroup').html()),
            "patch_overallscore":encodeURIComponent($('#overall-score').html())
        }, function(pdf) {
            try {
                pdf = JSON.parse(pdf);
                downloadPdf.attr('data-enabled', 'true');
                window.open(_e.url + pdf.link, '_blank');
                $.post(_e.url + '/ping', {
                    "remove": pdf.link
                }, null);
            } catch (e) {
                console.warn("Bad response");
                downloadPdf.text("Report failed. Try again");
            }
            setTimeout(function() {
                downloadPdf.text(text);
            }, 4000);
            
        });
    });   
    
    
});

function niceToggle(id) {    
    if (jQuery('#' + id + ' i.expandtoggle').hasClass('show-details')) {
        jQuery('#' + id + ' i.expandtoggle').removeClass('fa-minus').addClass('fa-plus');
    } else {
        jQuery('#' + id + ' i.expandtoggle').removeClass('fa-plus').addClass('fa-minus');
    }
    jQuery('#' + id + ' i.expandtoggle').toggleClass('show-details');
    jQuery('#' + id + ' .factor-info').toggle();
}

function robotsTxtToggle(text){    
    if(jQuery('.robotstoggle').hasClass('rttoggledown')){
       jQuery('.robotstoggle').removeClass('rttoggledown').addClass('rttoggleup');
       jQuery('.robotstoggle').css('height','auto');       
       jQuery('.robotstxt').text(text);
       jQuery('.robotstxt').prepend('<i class="fa fa-angle-up"></i>');
    }else if(jQuery('.robotstoggle').hasClass('rttoggleup')){
        jQuery('.robotstoggle').removeClass('rttoggleup').addClass('rttoggledown');
        jQuery('.robotstoggle').css('height','160px');        
        jQuery('.robotstxt').text(text);
        jQuery('.robotstxt').prepend('<i class="fa fa-angle-down"></i>');
    }
}

function imgAltToggle(text){
    if(jQuery('.imgalttoggle').hasClass('imgalttoggledown')){
       jQuery('.imgalttoggle').removeClass('imgalttoggledown').addClass('imgalttoggleup');
       jQuery('.imgalttoggle').css('height','auto'); 
       jQuery('ul.imgalttoggle li.lastnotoggle').addClass('toggledlist').removeClass('lastnotoggle');
       jQuery('.showmoreimgalt').text(text);
       jQuery('.showmoreimgalt').prepend('<i class="fa fa-angle-up"></i>');
    }else if(jQuery('.imgalttoggle').hasClass('imgalttoggleup')){
        jQuery('.imgalttoggle').removeClass('imgalttoggleup').addClass('imgalttoggledown');
        jQuery('.imgalttoggle').css('height','105px'); 
        jQuery('ul.imgalttoggle li.toggledlist').addClass('lastnotoggle').removeClass('toggledlist');
        jQuery('.showmoreimgalt').text(text);
        jQuery('.showmoreimgalt').prepend('<i class="fa fa-angle-down"></i>');
    }
}

function sitemapToggle(text){
    if(jQuery('.sitemaptoggle').hasClass('sitemaptoggledown')){
       jQuery('.sitemaptoggle').removeClass('sitemaptoggledown').addClass('sitemaptoggleup');
       jQuery('.sitemaptoggle').css('height','auto'); 
       jQuery('ul.sitemaptoggle li.lastnotoggle').addClass('toggledlist').removeClass('lastnotoggle');
       jQuery('.showmoresitemap').text(text);
       jQuery('.showmoresitemap').prepend('<i class="fa fa-angle-up"></i>');
    }else if(jQuery('.sitemaptoggle').hasClass('sitemaptoggleup')){
        jQuery('.sitemaptoggle').removeClass('sitemaptoggleup').addClass('sitemaptoggledown');
        jQuery('.sitemaptoggle').css('height','105px'); 
        jQuery('ul.sitemaptoggle li.toggledlist').addClass('lastnotoggle').removeClass('toggledlist');
        jQuery('.showmoresitemap').text(text);
        jQuery('.showmoresitemap').prepend('<i class="fa fa-angle-down"></i>');
    }
}

function clickExpandTable(){
    $('.expandtable').click(function(){
        console.log('The click****');
        if($('.expandtable > i').hasClass('fa-expand')){
            $('.expandtable > i').removeClass('fa-expand').addClass('fa-compress');
        }else if($('.expandtable > i').hasClass('fa-compress')){
            $('.expandtable > i').removeClass('fa-compress').addClass('fa-expand');
        }
        var count = 0;

        $('.tabletocollapse > tbody > tr').each(function(){
            count++;
            if($(this).hasClass('hiderows') && count > 10){
                $(this).removeClass('hiderows');
            }else if(count > 10){
                $(this).addClass('hiderows');
            }
        });        
    }); 
}

function pdfIsmobileCond(){
    //console.log('conditions');
    
    if(jQuery('.robotstxtcontainer').text().length < 250){
       jQuery('.robotstoggle').css('height','auto');
       jQuery('.robotstxt').css('display','none');       
    }else{
        jQuery('.robotstoggle').css('height','160px');
        if(flagAppend1 === false){
            jQuery('.robotstxt').prepend('<i class="fa fa-angle-down"></i>');
            flagAppend1 = true;
        }        
        jQuery('.robotstxt').css('display','block');
    }
    
    if(jQuery('.imgalttoggle li').length <= 6){
        jQuery('.imgalttoggle').css('height','auto');
        jQuery('.showmoreimgalt').css('display','none');        
    }else{
        jQuery('.imgalttoggle').css('height','105px');
        if(flagAppend2 === false){
            jQuery('.showmoreimgalt').prepend('<i class="fa fa-angle-down"></i>');
            flagAppend2 = true;
        }        
        jQuery('.showmoreimgalt').css('display','block');
    } 
    
    if(jQuery('.sitemaptoggle li').length <= 6){
        jQuery('.sitemaptoggle').css('height','auto');
        jQuery('.showmoresitemap').css('display','none');        
    }else{
        jQuery('.sitemaptoggle').css('height','105px');
        if(flagAppend3 === false){
            jQuery('.showmoresitemap').prepend('<i class="fa fa-angle-down"></i>');
            flagAppend3 = true;
        }        
        jQuery('.showmoresitemap').css('display','block');
    }
    
    if(_e.mobileflag){
        jQuery('div.backlinkchartwrapper').each(function(){
            jQuery(this).removeClass('backlinkchartwrapper').addClass('col-xs-12').addClass('col-sm-12').addClass('col-md-6').addClass('col-lg-4');
        });
        jQuery('#erreport .robotstxt').css({'font-size':'11px','margin-bottom':'12px'});
        jQuery('#erreport .showmoreimgalt').css({'font-size':'11px'});
    }else{
        jQuery('div.backlinkchartwrapper').each(function(){
            jQuery(this).removeClass('backlinkchartwrapper').removeClass('col-xs-12').removeClass('col-sm-12').removeClass('col-md-6').removeClass('col-lg-4').addClass('backlinkchartwrapper');
        });            
    }
    
    if(_e.pdf || window.location.href.toString().indexOf('export') != -1){
        jQuery('#topcontrol').hide();
        jQuery('.robotstoggle').css('height','auto');
        jQuery('.robotstxt').css('display','none');
        jQuery('.imgalttoggle').css('height','auto');
        jQuery('ul.sitemaptoggle li.lastnotoggle').removeClass('lastnotoggle');
        jQuery('ul.imgalttoggle li.lastnotoggle').removeClass('lastnotoggle');
        jQuery('.sitemaptoggle').css('height','auto');
        jQuery('.showmoreimgalt').css('display','none');
        jQuery('.showmoresitemap').css('display','none');
        jQuery('.expandtoggle').css('display','none');
    }
}