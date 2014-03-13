$(document).ready(function(){
    var quickFilterForm = $('#quick_filter_form');
    var filterMainForm = $('#filter_form');
    var quickFilter = quickFilterForm.find('#quick_filter');
    var hiddenQuickFilter = filterMainForm.find('#quick-filter-hidden');
    var addressField = $('#form_address');
    var correctAddress = $('#form_correct_address');
    var allowSubmit = true;

    var siteTable = ['ул', 'наб'];
    var googleTable = ['улица', 'набережная'];

    // Hide submit buttons
    $('.filter-form-submit').addClass('d-none');

    var dialogCorrectAddress = $('#correct-addr-dialog').dialog({
        autoOpen: false,
        height: 120,
        width: 350
    });

    quickFilterForm.submit(function(){
        $(hiddenQuickFilter).val($(quickFilter).val());
        filterMainForm.submit();
        return false;
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

    var lastGoogleRequest = new Date().getTime();
    var lastResponse = [];

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

    $(filterMainForm).submit(function() {
        var submit = checkAddress();
        if (!submit) {
            showIncorrectAddressMessage();
        }
        return submit;
    });


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
        console.log(group);
        var labelContent = '<div class="marker-label-id-' + group + ' info-content d-none">' +
            '<div class="main-info">';

        if (logo.length > 0) {
            labelContent += '<div class="logo fl-r">' +
                '<img src="' + logo + '" width="100" />' +
                '</div>';
        }

        labelContent += '<div class="name"><a href="' + url + '"> ' + name + '</a></div>' +
            '<div class="address">' + address + '</div>';

        if ($(current).attr('data-org-name')) {
            labelContent += '<div class="org-name"><a href="' + $(current).attr('data-org-url') + '">' + $(current).attr('data-org-name') + '</a></div>'
        }

        if (phone1.length > 0) {
            labelContent +=  '<div class="phone">' + phone1 + '</div>';
        }
        if (phone2.length > 0) {
            labelContent += '<div class="phone">' + phone2 + '</div>';
        }
        if (site.length > 0) {
            labelContent += '<div class="site"><a href="' + site + '">' + site + '</div>';
        }
        if (email.length > 0) {
            labelContent += '<div class="email">' + email + '</div>';
        }
        labelContent += '<div class="cl-b">';
        labelContent +='</div>';

        if ($(current).attr('data-org1-name')) {
            labelContent += '<div class="org-info"><a href="' + $(current).attr('data-org1-url') + '">' + $(current).attr('data-org1-name') + '</a></div>';
            labelContent += '<div class="org-info"><a href="' + $(current).attr('data-org2-url') + '">' + $(current).attr('data-org2-name') + '</a></div>';
        }
        if ($(this).attr('data-org-place1')) {
            labelContent += '<div class="place-info">' + $(current).attr('data-org-place1') + '</div>';
            if ($(current).attr('data-org-place2')) {
                labelContent += '<div class="place-info">' + $(current).attr('data-org-place2') + '</div>';
            }
        }

        labelContent +=
            '</div>' +
                '</div>';
        return labelContent;
    }

    var frontMap;
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

//    function setCorrectMapBounds(bounds) {
//        var inside = 0;
//        var ne = bounds.getNorthEast();
//        var sw = bounds.getSouthWest();
//        var poly = new google.maps.Polygon();
//        poly.setPaths([ne, sw, new google.maps.LatLng(ne.lat(), sw.lng()), new google.maps.LatLng(sw.lat(), ne.lng())]);
//        poly.setMap(frontMap);
//        poly.setVisible(false);
//        markers.forEach(function (target) {
//            if (google.maps.geometry.poly.containsLocation(target.getPosition(), poly)) {
//                inside += 1;
//            }
//        });
//        if (inside < 3) {
//            var distances = [];
//            markers.forEach(function (target) {
//                var distance = google.maps.geometry.spherical.computeDistanceBetween(defBounds.getCenter(), target.getPosition());
//                distances[distances.length] = { position: target.getPosition(), distance: distance };
//            });
//            distances.sort(function (a, b) {
//                return a.distance - b.distance;
//            });
//            for (var i = 0; i < 3 - inside; ++i) {
//                defBounds.extend(distances[i].position);
//            }
//        }
    //}

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
                    calculateCenter.extend(new google.map.LatLng(lat, lng));
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
        if (!defBounds.getNorthEast().equals(defBounds.getSouthWest()) && google.maps.geometry.spherical.computeDistanceBetween(defBounds.getSouthWest(), frontMap.getCenter()) > 1000) {
            frontMap.fitBounds(defBounds);
            frontMap.panToBounds(defBounds);
        } else {
            frontMap.setZoom(15);
            frontMap.setCenter(defBounds.getCenter());
        }
    }
    var initializeMap = function() {
        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: new google.maps.LatLng(55.716, 37.637),
            zoom: 15
        };
        frontMap = new google.maps.Map(document.getElementById("main-map"),
            mapOptions);

        if ($('#searched .data-list').length > 0) {
            readMarkerInfo($('#searched'), true);
        } else if ($('#today .data-list').length > 0)  {
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
                        icon: new google.maps.MarkerImage('/bundles/front/images/conv40.png'),
                        map: null,
                        title: name,
                        id:id,
                        group: markerInfo.length,
                        labelContent: labelContent,
                        clickable: true
                    };
                } else {
                    var index = lats.indexOf(lat.toString());

                    markerInfo[index].labelContent = markerInfo[index].labelContent + generateMarkerLabel(this, id, name, address, phone1, phone2, url, site, email, logo, index);
                    console.log(index);
                }
            }
        });
    };

    var initMarkers = function(markerInfo) {
        markerInfo.forEach(function(target){
            markers[markers.length] = new MarkerWithLabel(target);
            google.maps.event.addListener(markers[markers.length - 1], 'click', function(marker){
                $('.info-content.view').each(function(){$(this).addClass('d-none'); $(this).removeClass('view')});
                $('.marker-label-id-' + this.group).each(function(){$(this).removeClass('d-none');});
                $('.marker-label-id-' + this.group).each(function(){$(this).addClass('view');});
            });
        });
    };

    var outMarkers = function(markers)
    {
        markers.forEach(function(target){
            target.setMap(frontMap);
        });
    };

    var clearMarkers = function()
    {
        markerInfo = [];
        lats = [];
        lngs = [];
        markerCluster.clearMarkers();
        markers = [];
    };

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

    function refreshMap(type) {
        clearMarkers();
        readMarkerInfo($('#' + type + ' .data-list'), true);
        initMarkers(markerInfo);
        outMarkers(markers);
        markerCluster = new MarkerClusterer(frontMap, markers, {minZoom: 2, styles: clusterStyles});

        setMapDefaultsBounds();
    }

    var loadList = function(target, type, needRefreshMap) {
        var url = $(target).attr('href');

        $.ajax({
            url: url,
            method: 'GET'
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
                    } else {
                        refreshMap('today');
                    }
                }
            });
    };



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
    setDefaultTypes();

    $("a#scrollTop").click(function(e) {
        e.preventDefault();
        $.scrollTo("#header",800);
    });






    // Widget Date/Time
    $('#form_date').dateRangeSlider();
//    $('#time_from_container').timepicker({
//        button: '#form_time_from',
//        showOn: 'both',
//        onSelect: timeSelect
//    });
//    $('#time_to_container').timepicker({
//        button: '#form_time_to',
//        showOn: 'both',
//        onSelect: timeSelect
//    });
//    $('#date_from_container').datepicker({
//        button: '#form_date_from',
//        showOn: 'both',
//        onSelect: dateSelect
//    });
//    $('#date_to_container').datepicker({
//        button: '#form_date_to',
//        showOn: 'both',
//        onSelect: dateSelect
//    });
//
//    var openedType = '';
//    var openedDateType = '';
//
//    /**
//     * Callback on time select
//     * @param variable
//     */
//    function timeSelect(variable) {
//        var prevDate = $('#form_time').val();
//        if (prevDate && openedType.indexOf('from') < 0) {
//            prevDate = prevDate.substr(0,5);
//            $('#form_time').val(prevDate + ' - ' + variable);
//        } else {
//            $('#form_time').val(variable);
//        }
//        $('#form_time_' + openedType).attr('value', variable);
//
//    }
//
//    function dateSelect(variable) {
//        var prevDate = $('#form_date').val();
//        if (prevDate) {
//            prevDate = prevDate.substr(0,10);
//            $('#form_date').val(prevDate + ' - ' + variable);
//        } else {
//            $('#form_date').val(variable);
//        }
//        $('#form_date_' + openedDateType).attr('value', variable);
//    }
//
//    function hideTimePicker() {
//        if (openedType.length > 0) {
//            $('#time_' + openedType + '_container').hide();
//            openedType = '';
//        }
//    }
//
//    function hideDatePicker() {
//        if (openedDateType.length > 0) {
//            $('#date_' + openedDateType + '_container').hide();
//            openedDateType = '';
//        }
//    }
//
//    $(document).click(function(e){
//        if (e.target.id != 'form_time') {
//            hideTimePicker();
//        }
//        if (e.target.id != 'form_date') {
//            hideDatePicker();
//        }
//    })
//
//    $('#form_time').click(function(){
//        var type = 'from';
//        if ($(this).val()) {
//            type = 'to';
//        }
//        if (openedType.length == 0) {
//            $('#time_' + type + '_container').fadeIn();
//            openedType = type;
//        }
//    });
//    $('#form_date').click(function(){
//        var type = 'from';
//        if ($(this).val()) {
//            type = 'to';
//        }
//        if (openedDateType.length == 0) {
//            $('#date_' + type + '_container').fadeIn();
//            openedDateType = type;
//        }
//    });
//
//    $('#form_time').change(function(){
//        var value = $(this).val();
//        var from = value.substr(0, 5);
//        var to = '';
//        if (value.length > 5) {
//            to = value.slice(-5);
//        }
//        $('#form_time_to').attr('value', to);
//        $('#form_time_from').attr('value', from);
//        refreshMainPage();
//    })
//
//    $('#form_date').change(function(){
//        var value = $(this).val();
//        var from = value.substr(0, 10);
//        var to = '';
//        if (value.length > 10) {
//            to = value.slice(-10);
//        }
//        $('#form_date_to').attr('value', to);
//        $('#form_date_from').attr('value', from);
//        refreshMainPage();
//    })

    // Hide all defined elements
    $('.js-hidden').each(function(){
        var offset = $(this).offset();
        $(this).removeClass('js-hidden');
        $(this).addClass('d-none');
        $(this).offset(offset);
    })

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

    setDefaultFilters();


    $('.event_type_filter input[type=checkbox]').change(function(){
        refreshMainPage();
    })

    $('.dance_type_filter input[type=checkbox]').change(function(){
        refreshMainPage();
    })

    $('#form_address').change(function(){
        refreshMainPage();
    })


    function refreshMainPage() {
        refreshList(true);
        //refreshMap('searched');
    }
});