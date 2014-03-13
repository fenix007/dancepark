var frontMap;

var quickFilterForm;
var filterMainForm;
var quickFilter;
var hiddenQuickFilter;
var addressField;
var correctAddress;
var allowSubmit = true;

var siteTable = ['ул', 'наб'];
var googleTable = ['улица', 'набережная'];

var markers = [];
var markerInfo = [];
var lats = [];
var lngs = [];
var defBounds = new google.maps.LatLngBounds();
var markerCluster;
var clusterStyles = [{
    url: '/bundles/front/images/m3.png',
    height: 65,
    width: 66,
    anchor: [8, 0],
    textColor: '#ff0000',
    textSize: 11
}];

var lastGoogleRequest = new Date().getTime();
var lastResponse = [];
function refreshMap(type) {
    clearMarkers();
    readMarkerInfo($('#' + type + ' .data-list'), true);
    initMarkers(markerInfo);
    outMarkers(markers);
    markerCluster = new MarkerClusterer(frontMap, markers, {minZoom: 2, styles: clusterStyles});

    google.maps.event.addListener(markerCluster, 'load', function() {
        setDefaultGmapListeners();
    });

    setMapDefaultsBounds();
}

function removeTodayResults() {
    $('#left-sidebar .tdy-tab').addClass('d-none');
    $('#left-sidebar #today').addClass('d-none');
}
function showTodayResults() {
    $('#left-sidebar .tdy-tab').removeClass('d-none');
    $('#left-sidebar #today').removeClass('d-none');
}

var loadList = function(target, type, needRefreshMap) {
    var url = $(target).attr('href');

    $.ajax({
        url: url,
        method: 'POST',
        data: $(filterMainForm).serialize()
    }).success(function(data){
            if (data.length > 0 && needRefreshMap == true) {
                $('#' + type + ' .data-list').append(data);

                var pieces = url.split('/');
                var nextPage = parseInt(pieces[3]) + 1;
                pieces[3] = nextPage;

                if ($('#' + type + ' .data-list .end').length > 0){
                    $(target).parent().addClass('d-none');
                }

                $(target).attr('href', pieces.join('/'));
                refreshMap(type);
            }
        });
};

var refreshList = function(needRefreshMap) {
    var url = $('.refresh-url').attr('data-url');

    $.ajax({
        url: url,
        method: 'POST',
        data: $(filterMainForm).serialize()
    }).success(function(data){
            if (data.length > 0 && needRefreshMap == true) {
                $('#searched').empty();
                $('#searched').append(data);
                if ($('#searched .data-list')) {
                    if ($('#left-sidebar .srch-tab.d-none').length > 0) {
                        $('#left-sidebar .srch-tab.d-none').removeClass('d-none');
                    }
                    refreshMap('searched');
                    $('.srch-tab a').click();
                    $('#load-search').click(function(){
                        loadList(this, 'searched', true);

                        return false;
                    });
                    removeTodayResults();
                }
            } else {
                showTodayResults();
            }
        });
};

function getAutocompleteData(event) {
    var url = '';
    var result = [];
    if (event.term.indexOf('м.') == 0) {
        var key = event.term.substr(2);
        if (key.indexOf(' ') == 0) {
            key = key.substr(1);
        }
        url = '/admin/address_group/metro/autocomplete?term=' + key;
        var metroResponse = $.ajax({
            type: 'GET',
            url: url,
            async: false
        }).responseText;
        metroResponse = JSON.parse(metroResponse);
        result = [];
        for (var key in metroResponse) {
            if (String(Number(key)) === key && metroResponse.hasOwnProperty(key)) {
                result[result.length] = 'м.' + metroResponse[key];
            }
        }
    }
    else {
        result = [];
        if (event.term.length > 3) {
            var key = event.term;
            if (key.indexOf(' ') == 0) {
                key = key.substr(1);
            }
            url = '/admin/address_group/street/autocomplete?term=' + key;
            var streetResponse = $.ajax({
                type: 'GET',
                url: url,
                async: false
            }).responseText;
            streetResponse = JSON.parse(streetResponse);
            for (var id in streetResponse) {
                if (String(Number(id)) === id && streetResponse.hasOwnProperty(id)) {
                    result[result.length] = streetResponse[id];
                }
            }
            if (result.length == 0) {
                lastGoogleRequest = new Date().getTime();
                url = 'http://maps.google.com/maps/api/geocode/json?' +
                    'address=' + encodeURI(event.term) + '&' +
                    '&sensor=false&' +
                    'language=ru';
                var googleResponse = $.ajax({
                    type: 'GET',
                    url: url,
                    async: false
                }).responseText;
                googleResponse = JSON.parse(googleResponse);
                var preparedResults = [];
                if (googleResponse.length > 0) {
                    for (var i = 0; i < results.length; ++i) {
                        var prepared = prepareResult(results[i]);
                        if (prepared != null) {
                            preparedResults[preparedResults.length] = prepared;
                        }
                    }
                }
                result = preparedResults;
            }
        }
    }
    return result;
}

function showIncorrectAddressMessage()
{
    $('#incorrect-address').fadeIn('slow');
    window.setTimeout(function(){$('#incorrect-address').hide('slow')}, 3000);
}

function checkAddress() {
    if ($(addressField).val().length == 0) {
        return true;
    }
    var data = $(addressField).val();
    var url = '';
    var results = [];
    if (data.indexOf('м.') == 0) {
        var key = data.substr(2);
        if (key.indexOf(' ') == 0) {
            key = key.substr(1);
        }
        url = '/admin/address_group/metro/check_autocomplete?term=' + key;
        var metroResponse = $.ajax({
            type: 'GET',
            url: url,
            async: false
        }).responseText;
        metroResponse = JSON.parse(metroResponse);
        results = [];
        for (var key in metroResponse) {
            if (String(Number(key)) === key && metroResponse.hasOwnProperty(key)) {
                results[results.length] = 'м.' + metroResponse[key];
            }
        }
        if (results.length > 0) {
            return true;
        }
    }
    else {
        results = [];
        if (data.length > 3) {
            var key = data;
            if (key.indexOf(' ') == 0) {
                key = key.substr(1);
            }
            lastGoogleRequest = new Date().getTime();
            url = 'http://maps.google.com/maps/api/geocode/json?' +
                'address=' + encodeURI(key) + '&' +
                '&sensor=false&' +
                'language=ru';
            var googleResponse = $.ajax({
                type: 'GET',
                url: url,
                async: false
            }).responseText;
            googleResponse = JSON.parse(googleResponse);
            var result = googleResponse.results;
            var prepared = null;
            if (result.length > 0) {
                for (var i = 0; i < result.length; ++i) {
                    prepared = prepareResult(result[i]);
                    if (prepared != null) {
                        break;
                    }
                }
            }
            if (prepared == null) {
                return false;
            }
            key = prepared;
            for(var i = 0; i < googleTable.length; ++i) {
                if(key.indexOf(googleTable[i]) >= 0) {
                    key = key.replace(' ' + googleTable[i], '');
                }
                if (key.indexOf(siteTable[i]) >= 0) {
                    key = key.replace(' ' + siteTable[i], '');
                }
            }

            $('#form_correct_address').val(key);
            return true;
        }
    }
    return false;
}
function prepareResult(object)
{
    var street = '';
    var station = '';
    for (var i = 0; i < object.address_components.length; ++i) {
        var component = object.address_components[i];
        var allowBreak = false;
        for (var j = 0; j < component.types.length; ++j) {

            if (component.types[j].indexOf('route') >= 0 && component.types[j].length == new String('reute').length) {
                street = component.long_name;
                allowBreak = true;
                break;
            } else if (component.types[j].indexOf('train_station') >= 0 && component.types[j].length == new String('train_station').length) {
                station = component.long_name;
                allowBreak = true;
                break;
            }

        }
        if (allowBreak) {
            break;
        }
    }
    if (street.length > 0) {
        for(var i = 0; i < googleTable.length; ++i) {
            if(street.indexOf(googleTable[i]) >= 0) {
                street = street.replace(googleTable[i], '');
                street = street + ' ' + siteTable[i];
            }
        }
        return street;
    }
//            if (station) {
//                return 'ст. ' + station;
//            }
    return null;
}
function autocompleteChange(event, response)
{
    var result = getAutocompleteData(event);
    response(result);
}



function generateMarkerLabel(current, id, name, address, phone1, phone2, url, email, site, logo, group) {
    var labelContent = '<div class="map-arrow fl-l"></div><div class="marker-label-id-' + group + ' info-content d-nonei" style="z-index:1000;">' +
        '<div class="main-info" id="event-map-block-' + id + '">';

    labelContent += '<div class="gmap-event-close"></div><div class="content">';

    if (logo.length > 0) {
        labelContent += '<div class="logo fl-r">' +
            '<img onclick="clickMapEventName(' + id + ')" src="' + logo + '" width="100" />' +
            '</div>';
    }

    labelContent += '<div class="name"><a class="gmap-event-name-link" onclick="clickMapEventName(' + id + ')" href="' + url + '"> ' + name + '</a></div>' +
        '<div class="address">' + address + '</div>';

    if ($(current).attr('data-org-name')) {
        labelContent += '<div class="org-name"><a onclick="clickMapOrganization(' + id + ', 0)" href="' + $(current).attr('data-org-url') + '">' + $(current).attr('data-org-name') + '</a></div>'
    }

    if (phone1.length > 0) {
        labelContent +=  '<div class="phone">' + phone1 + '</div>';
    }
    if (phone2.length > 0) {
        labelContent += '<div class="phone">' + phone2 + '</div>';
    }
    if (site.length > 0) {
        labelContent += '<div class="site"><a onclick="clickMapSite(' + id + ')" href="' + site + '">' + site + '</div>';
    }
    if (email.length > 0) {
        labelContent += '<div class="email">' + email + '</div>';
    }
    labelContent += '<div class="cl-b">';
    labelContent +='</div>';

    if ($(current).attr('data-org1-name')) {
        labelContent += '<div class="org-info org-1 fl-l"><a onclick="clickMapOrganization(' + id + ', 1)" href="' + $(current).attr('data-org1-url') + '">' + $(current).attr('data-org1-name') + '</a></div>';
        labelContent += '<div class="org-info org-2 fl-l"><a onclick="clickMapOrganization(' + id + ', 2)" href="' + $(current).attr('data-org2-url') + '">' + $(current).attr('data-org2-name') + '</a></div>';
        labelContent += '<div class="cl-b"></div>'
    }
    if ($(this).attr('data-org-place1')) {
        labelContent += '<div class="place-info">' + $(current).attr('data-org-place1') + '</div>';
        if ($(current).attr('data-org-place2')) {
            labelContent += '<div class="place-info">' + $(current).attr('data-org-place2') + '</div>';
        }
    }

    labelContent +=
        '</div></div>' +
            '</div>';
    return labelContent;
}

function setMapDefaultsBounds() {
    defBounds = new google.maps.LatLngBounds();
    for (var key in markers) {
        defBounds.extend(markers[key].getPosition());
    }
    var custom = false;
    var data = null;
    if ($('#searched .data-list').length > 0 ) {
        data = $('#searched .data-list');
    }
    else if ($('#today .data-list').length > 0 ) {
        data = $('#today .data-list');
    }
    if (data != null) {
        var calculateCenter = new google.maps.LatLngBounds();

        if (data.attr('data-top-lat') != null) {
            // if find by address
            custom = true;
            var topLeft = new google.maps.LatLng(data.attr('data-top-lat'), data.attr('data-lft-lng'));
            var botRight = new google.maps.LatLng(data.attr('data-bot-lat'), data.attr('data-rgh-lng'));
            calculateCenter.extend(topLeft);
            calculateCenter.extend(botRight);
        } else if(data.attr('data-0-lat') != null) {
            custom = true;
            // if find by metro
            var i = 0;
            while (data.attr('data-' + i + '-lat')) {
                var lat = data.attr('data-' + i + '-lat');
                var lng = data.attr('data-' + i + '-lng');
                calculateCenter.extend(new google.maps.LatLng(lat, lng));
                i++;
            }
        }
    }
    function getBoundsZoomLevel(bounds, mapDim) {
        var WORLD_DIM = { height: 650, width: 650 };
        var ZOOM_MAX = 21;

        function latRad(lat) {
            var sin = Math.sin(lat * Math.PI / 180);
            var radX2 = Math.log((1 + sin) / (1 - sin)) / 2;
            return Math.max(Math.min(radX2, Math.PI), -Math.PI) / 2;
        }

        function zoom(mapPx, worldPx, fraction) {
            return Math.floor(Math.log(mapPx / worldPx / fraction) / Math.LN2);
        }

        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();

        var latFraction = (latRad(ne.lat()) - latRad(sw.lat())) / Math.PI;

        var lngDiff = ne.lng() - sw.lng();
        var lngFraction = ((lngDiff < 0) ? (lngDiff + 360) : lngDiff) / 360;

        var latZoom = zoom(mapDim.height, WORLD_DIM.height, latFraction);
        var lngZoom = zoom(mapDim.width, WORLD_DIM.width, lngFraction);

        return Math.min(latZoom, lngZoom, ZOOM_MAX);
    }
    setMapBounds();

    if (custom == true) {
//            var zoom = getBoundsZoomLevel(defBounds, {width: 662, height: 645});
//            frontMap.setZoom(zoom);
        frontMap.setCenter(calculateCenter.getCenter());
        //setCorrectMapBounds(calculateCenter);
    }
}
function setMapBounds() {
    if (markers.length > 0 && !defBounds.getNorthEast().equals(defBounds.getSouthWest()) && google.maps.geometry.spherical.computeDistanceBetween(defBounds.getSouthWest(), frontMap.getCenter()) > 1000) {
        frontMap.fitBounds(defBounds);
        frontMap.panToBounds(defBounds);
    } else {
        frontMap.setZoom(10);
        if (markers.length == 0) {
            frontMap.setCenter(new google.maps.LatLng(55.716, 37.637));
        } else {
            frontMap.setCenter(defBounds.getCenter());
        }
    }
}
var initializeMap = function() {
    var mapOptions = {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        center: new google.maps.LatLng(55.716, 37.637),
        zoom: 10
    };
    frontMap = new google.maps.Map(document.getElementById("main-map"),
        mapOptions);

    if ($('.srch-tab.active').length > 0) {
        readMarkerInfo($('#searched'), true);
    } else if ($('.tdy-tab.active').length > 0)  {
        readMarkerInfo($('#today'), true);
    } else {
        frontMap.setZoom(8);
    }

    if (markerInfo.length > 0) {
        initMarkers(markerInfo);
        outMarkers(markers);

        markerCluster = new MarkerClusterer(frontMap, markers, {
            styles: clusterStyles
        });

        google.maps.event.addListener(markerCluster, 'clusteringend', function(){
            setDefaultGmapListeners();
        });

        setMapDefaultsBounds();
    }
};

var readMarkerInfo = function(eventsContainer, autoLocate)
{
    $(eventsContainer).find('.data-group .data').each(function(){
        var id = $(this).attr('data-id');
        var lng = $(this).attr('data-lng');
        var lat = $(this).attr('data-lat');
        var name = $(this).find('.name').text();
        var address = $(this).attr('data-address');
        var phone1 = $(this).attr('data-phone1');
        var phone2 = $(this).attr('data-phone2');
        var url = $(this).attr('data-url');
        var email = $(this).attr('data-email');
        var logo = $(this).attr('data-logo');
        var site = $(this).attr('data-site');


        if (lat.length > 0 && lng.length > 0) {
            if (lats.indexOf(lat.toString()) == -1 && lngs.indexOf(lng.toString()) == -1) {
                lats[lats.length] = lat.toString();
                lngs[lats.length] = lng.toString();
                var location = new google.maps.LatLng(lat, lng);

                if (autoLocate == true) {
                    defBounds.extend(location);
                }

                var labelContent = generateMarkerLabel(this, id, name, address, phone1, phone2, url, email, site, logo, markerInfo.length );
                markerInfo[markerInfo.length] = {
                    position: location,
                    icon: new google.maps.MarkerImage('/bundles/front/images/marker.png'),
                    map: null,
                    title: name,
                    id:id,
                    group: markerInfo.length,
                    labelContent: labelContent,
                    clickable: true,
                    labelInBackground: true,
                    zIndex: -2
                };
            } else {
                var index = lats.indexOf(lat.toString());

                markerInfo[index].labelContent = markerInfo[index].labelContent + generateMarkerLabel(this, id, name, address, phone1, phone2, url, site, email, logo, index);
            }
        }
    });
};

var initMarkers = function(markerInfo) {

    markerInfo.forEach(function(target){
        markers[markers.length] = new MarkerWithLabel(target);
        google.maps.event.addListener(markers[markers.length - 1], 'click', function(marker){
            $('.info-content.view').each(function(){
                $(this).addClass('d-none');
                $(this).removeClass('view');
                $(this).parent().css('z-index', -3);
            });
            if (!$('.marker-label-id-' + this.group).hasClass('manually-hidden')) {
                $('.marker-label-id-' + this.group).parent().css('z-index', 1000);
                $('.marker-label-id-' + this.group).each(function(){$(this).removeClass('d-nonei');});
                $('.marker-label-id-' + this.group).each(function(){$(this).addClass('view');});
            } else {
                $('.marker-label-id-' + this.group).addClass('d-nonei');
                $('.marker-label-id-' + this.group).removeClass('view');
                $('.marker-label-id-' + this.group).parent().css('z-index', 1000);
                $('.marker-label-id-' + this.group).removeClass('manually-hidden');
            }
        });
    });
};

function setDefaultGmapListeners () {
    $('.gmap-event-close').each(function(){
        google.maps.event.addDomListener($(this).get(0), 'click', function(){
            $($(this).parents()[2]).find('.info-content').each(function(){
                $(this).addClass('manually-hidden');
            })
        });
    })
}

var outMarkers = function(markers)
{
    markers.forEach(function(target){
        target.setMap(frontMap);
    });
};



function setDefaultTypes() {
    $('.dance_type_filter .content input').each(function(){
        $(this).hide();
    })
    $('.event_type_filter .content input').each(function(){
        $(this).hide();
    })
    $('.dance_type_filter .content input:checked').each(function(){
        var label = $(this).parents()[1];
        label = $('label', label);
        $(label).addClass("label-act");
    })
    $('.event_type_filter .content input:checked').each(function(){
        var label = $(this).parents()[1];
        label = $('label', label);
        $(label).addClass("label-act");
    })
}

var clearMarkers = function()
{
    markerInfo = [];
    lats = [];
    lngs = [];
    if (typeof  (markerCluster) != 'undefined') {
        markerCluster.clearMarkers();
    }
    markers = [];
};

function setDefaultFilters() {
    var dateFrom = $('#form_date_from').attr('value');
    var dateTo = $('#form_date_to').attr('value');
    if (dateFrom && dateFrom.length > 0) {
        $('#form_date').attr('value', dateFrom.replace('-', '/').replace('-', '/'));
    }
    if (dateTo && dateTo.length > 0) {
        $('#form_date').attr('value', $('#form_date').attr('value') + ' - ' + dateTo.replace('-', '/').replace('-', '/'));
    }

    var timeFrom = $('#form_time_from').attr('value');
    var timeTo = $('#form_time_to').attr('value');
    if (timeFrom && timeFrom.length > 0) {
        $('#form_time').attr('value', timeFrom.replace('_', ':').replace('_', ':'));
    }
    if (timeTo && timeTo.length > 0) {
        $('#form_time').attr('value', $('#form_time').attr('value') + ' - ' + timeTo.replace('_', ':').replace('_', ':'));
    }
}

function refreshMainPage() {
    refreshList(true);
    //refreshMap('searched');
}

$(document).ready(function(){
    quickFilterForm = $('#quick_filter_form');
    filterMainForm = $('#filter_form');
    quickFilter = quickFilterForm.find('#quick_filter');
    hiddenQuickFilter = filterMainForm.find('#quick-filter-hidden');
    addressField = $('#form_address');
    correctAddress = $('#form_correct_address');
    // Hide submit buttons
    $('.filter-form-submit').addClass('d-none');

    quickFilterForm.submit(function(){
        $(hiddenQuickFilter).val($(quickFilter).val());
        filterMainForm.submit();
        return false;
    });

    var dialogCorrectAddress = $('#correct-addr-dialog').dialog({
        autoOpen: false,
        height: 120,
        width: 350
    });

    $(correctAddress).change(function(){
        allowSubmit = true;
        $(addressField).val($(this).val());
        dialogCorrectAddress.dialog('close');
    });

    $('#form_address').autocomplete({
        source: autocompleteChange,
        select: function(event, ui) {
            $('#form_correct_address').val(ui.item.value);
        }
    });
    $(filterMainForm).submit(function() {
        var submit = checkAddress();
        if (!submit) {
            showIncorrectAddressMessage();
        }
        return submit;
    });

    addressField.select(function(target){
        if (typeof(target.toElement) != 'undefined') {
            $('#form_addr_group').val(target.toElement.innerText);
        }
    });

    $('#load-search').click(function(){
        loadList(this, 'searched', true);

        return false;
    });
    $('#load-today').click(function(){
        loadList(this, 'today', true);

        return false;
    });

    $('#left-sidebar ul li').click(function(){
         var type = $(this).find('a').attr('href').substr(1);
        refreshMap(type);
    });


    google.maps.event.addDomListener(window, 'load', initializeMap);

    $('.event_type_filter .control-label').click(function() {
        var active = 0;
        if ($(this).hasClass('label-act')) {
            active = 1;
        }
        var forElement = $(this).attr('for');

        if(active != 1) {
            $(this).addClass("label-act");
            $('#' + forElement).addClass('checked');
        } else {
            $(this).removeClass("label-act");
            $('#' + forElement).removeClass('checked');
        }
    });

    $('.dance_type_filter .control-label').click(function() {
        var active = 0;
        if ($(this).hasClass('label-act')) {
            active = 1;
        }
        var forElement = $(this).attr('for');

        if(active != 1) {
            $(this).addClass("label-act");
            $('#' + forElement).addClass('checked');
        } else {
            $(this).removeClass("label-act");
            $('#' + forElement).removeClass('checked');
        }
    });
    setDefaultTypes();

    $("a#scrollTop").click(function(e) {
        e.preventDefault();
        $.scrollTo("#header",800);
    });

    var currentDate = new Date();
    // Widget Date/Time
    $('#form_date').dateRangeSlider({
        bounds: {
            min: currentDate,
            max: new Date(currentDate.getFullYear(), currentDate.getMonth() + 3, currentDate.getDate())
        },
        arrows: false,
        defaultValues: {
            min: currentDate,
            max: new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() + 14)
        }
    });

    $('#form_date').bind('valuesChanging', function() {
        //var rLabel = $('.date_filter .ui-rangeSlider-rightLabel')
    })

    $('#form_time').rangeSlider({
        bounds: {
            min: 0,
            max: 1440
        },
        arrows: false,
        defaultValues: {
            min: 400,
            max: 1000
        },
        formatter: function(val) {
            var date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDay(), 0, val);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            if (hours == 0) {
                hours = '00';
            }
            if (minutes == 0) {
                minutes = '00';
            }
            if (val == 1440) {
                hours = '24';
            }
            return hours + ':' + minutes;
        }
    });
    $('#form_price').rangeSlider({
        bounds: {
            min: 0,
            max: 10000
        },
        arrows: false,
        defaultValues: {
            min: 0,
            max: 1000
        }
    });

    // Hide all defined elements
    $('.js-hidden').each(function(){
        var offset = $(this).offset();
        $(this).removeClass('js-hidden');
        $(this).addClass('d-none');
        $(this).offset(offset);
    })

    setDefaultFilters();

    $('.recommended-events').jCarouselLite({
        btnNext: ".next",
        btnPrev: ".prev",
        circular: true
    });
});

$(window).load(function(){
    $('.date_filter .links a').click(function(){
        var from = $(this).attr('data-from').split('-');
        var to = $(this).attr('data-to').split('-');
        $('#form_date').dateRangeSlider('values', new Date(from[0], parseInt(from[1] - 1), from[2]), new Date(to[0], parseInt(to[1] - 1), to[2]));
        return false;
    })

    $('.time_filter .links a').click(function(){
        var from = $(this).attr('data-from').split(':');
        var to = $(this).attr('data-to').split(':');
        $('#form_time').rangeSlider('values', parseInt(from[0]) * 60 + parseInt(from[1]), parseInt(to[0]) * 60 + parseInt(to[1]));
        return false;
    })

    $('.price_filter .links a').click(function(){
        var from = $(this).attr('data-from');
        var to = $(this).attr('data-to');
        if (!to) {
            to = 1000000;
        }
        $('#form_price').rangeSlider('values', parseInt(from), parseInt(to));
        return false;
    })

    $('#form_date').bind('valuesChanged', function(e, data){
        /** @var min Date */
        var min = data.values.min;
        /** @var max Date */
        var max = data.values.max;
        $('#form_date_to').attr('value', max.getFullYear() + '-' + (max.getMonth() + 1) + '-' + max.getDate());
        $('#form_date_from').attr('value', min.getFullYear() + '-' + (min.getMonth() + 1) + '-' + min.getDate());
        refreshMainPage();
    })

    $('#form_time').bind('valuesChanged', function(e, data){
        /** @var min Date */
        var min = new Date(0, 0, 0, 0, data.values.min);
        var max = new Date(0, 0, 0, 0, data.values.max);
        $('#form_time_to').attr('value', max.getHours() + ':' + (max.getMinutes() < 10 ? '0' + max.getMinutes() : max.getMinutes()));
        $('#form_time_from').attr('value', min.getHours() + ':' + (min.getMinutes() < 10 ? '0' + min.getMinutes() : min.getMinutes()));
        refreshMainPage();
    })

    $('#form_price').bind('valuesChanged', function(e, data){
        /** @var min Date */
        var min = data.values.min;
        var max = data.values.max;
        $('#form_price_to').attr('value', max);
        $('#form_price_from').attr('value', min);
        refreshMainPage();
    })

    $('.event_type_filter input[type=checkbox]').change(function(){
        refreshMainPage();
    })

    $('.dance_type_filter input[type=checkbox]').change(function(){
        refreshMainPage();
    })

    $('#form_address').change(function(){
        refreshMainPage();
    })

    function refreshLabel() {
        if ($(this).is(':checked')) {
            $(this).parent().find('label').addClass('active');
        } else {
            $(this).parent().find('label').removeClass('active');
        }
    }

    $('#form_children').change(function(){
        refreshLabel.call(this);
        refreshMainPage();
    });

    $('#form_abonement').change(function(){
        refreshLabel.call(this);
        refreshMainPage();
    });

    initializePageHeight();
});

function clickMapEventName(id){
    var href = $('#event-map-block-' + id + ' .gmap-event-name-link').attr('href');
    window.location.replace(href);
}

function clickMapOrganization(id, num) {
    var href = '';
    if (num == 0) {
        href = $('#event-map-block-' + id + ' .org-name a').attr('href');
    } else if (num == 1) {
        href = $('#event-map-block-' + id + ' .org-1 a').attr('href');
    } else if (num == 2) {
        href = $('#event-map-block-' + id + ' .org-2 a').attr('href');
    }
    window.location.replace(href);
}

function clickMapSite(id) {
    var href = $('#event-map-block-' + id + ' .site a').attr('href');
    window.location.replace(href);
}