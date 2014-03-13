$(document).ready(function(){

    function initialize()
    {
        if ($('.how-to-get').length > 0) {
            var howToGetDialog = $('.how-to-get-container').dialog({
                title: "Как пройти",
                autoOpen: false
            });

            $('.how-to-get-open').click(function(){
                howToGetDialog.dialog('open');
                return false;
            })
        }

        $('.feedback-open').click(function(){
            var url = $(this).attr('data-action')
            $.ajax({
                method: 'POST',
                url: url
            }).success(function(data){
                    console.log(data);
                    refreshFeedback(data);
                })
            return false;
        })

        function refreshFeedback (data) {
            $('.feedback-form-container').append(data);

            var feedbackDialog = $('.feedback-form-container .form-container').dialog({
                title: "Отзыв"
            });

            $('.feedback-submit').click(function(){
                var parentForm = $(this).parents('form')[0];
                var action = $(parentForm).attr('action');
                var method = $(parentForm).attr('method');

                $.ajax({
                    url: action,
                    type: method,
                    data:  $(parentForm).serialize()
                }).success(function(data){
                        if ($(data).find('.error').length == 0) {
                            $(feedbackDialog).dialog('close');
                        } else {
                            $('.feedback-form-container').empty();
                            $(feedbackDialog).dialog('close');
                            refreshFeedback(data);
                        }
                    });
                return false;
            })
        }
    }

    initialize();

    var initializeMap = function() {
        var container = $('#event-map');
        var lat = $(container).attr('data-lat');
        var lng = $(container).attr('data-lng');
        var title = $(container).attr('data-title');
        var position = new google.maps.LatLng(lat, lng);
        var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: position,
            zoom: 15
        };
        frontMap = new google.maps.Map(document.getElementById("event-map"),
            mapOptions);

        var eventPlace = new google.maps.Marker({
            position: position,
            title: title,
            map: frontMap
        });
    };
    google.maps.event.addDomListener(window, 'load', initializeMap);

    $('.join-event').click(function(){
        var url = $(this).attr('href');
        var response = $.ajax({
            type: 'GET',
            url: url,
            async: false
        }).responseText;
        response = JSON.parse(response);
        if (response.error > 0) {
            var errorMessage = $(this).parent().find('.join-error');
            $(errorMessage).fadeIn('slow');
            window.setTimeout(function(){$(errorMessage).hide('slow')}, 2000);
        } else {
            var successMessage = $(this).parent().find('.event-joined');
            $(successMessage).fadeIn('slow');
            window.setTimeout(function(){$(successMessage).hide('slow')}, 2000);
        }
        return false;
    });
});