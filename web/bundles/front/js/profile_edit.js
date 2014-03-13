$(document).ready(function(){
    function addDanceType(value, label) {
        value = parseInt(value);
        var val = $('#dancer_profile_danceTypesHidden').attr('value');
        var needSet = true;
        val = JSON.parse(val);
        for (var i = 0; i < val.length; ++i) {
            if (val[val.length] == value) {
                needSet = false;
            }
        }
        if (needSet) {
            val[val.length] = value;
            $('#dancer_profile_danceTypesHidden').attr('value', JSON.stringify(val));
        }

        addDanceTypeToBlock(value, label);
    }

    function removeSelectedFromList(value) {
        $('#dancer_dance_type_danceType [value=' + value + ']').remove();
    }

    function addRemovedFromList(value, label) {
        $('#dancer_dance_type_danceType').append('<option value="' + value + '">' + label +'</option>');
    }

    function addDanceTypeToBlock(value, label) {
        var block = $('#dance-types-block');
        var types = $(block).find('.types');
        var element = document.createElement('div');
        var lab = document.createElement('span');
        var link = document.createElement('a');
        link.setAttribute('class', 'rbm');
        link.setAttribute('href', '#' + value);
        link.appendChild(document.createTextNode($(block).find('div.rmb').text()));
        $(link).click(removeDanceType);
        lab.appendChild(document.createTextNode(label));
        element.appendChild(lab);
        element.appendChild(link);
        element.setAttribute('class', 'dance-type');

        $(types).append($(element));
    }

    var removeDanceType = function(){
        var val = $(this).attr('href');
        if (val) {
            val = val.substr(1);
            var types = $('#dancer_profile_danceTypesHidden').attr('value');
            types = types.replace('\\', '');
            types = JSON.parse(types);
            for (var i = 0; i < types.length; ++i) {
                if (types[i] == val) {
                    types.splice(i, i);
                }
            }
            $('#dancer_profile_danceTypesHidden').attr('value', JSON.stringify(val));
            var label = $(this).parent().find('span').text();
            addRemovedFromList(val, label);
            $(this).parent().remove();
        }
        return false;
    };

    function initialize() {
        var options = [];
        $('#dancer_profile_danceType_control_group').addClass('d-none');
        $('#dancer_profile_photo_control_group').addClass('d-none');
        var addBtn = $('#dance-type-dialog').attr('data-add-btn');
        var cancelBtn = $('#dance-type-dialog').attr('data-cancel-btn');
        var danceTypeChooser = $('#dance-type-dialog').dialog({
            autoOpen:false,
            modal:true,
            buttons: {
                "Добавить": function() {
                    var selected = $('#dancer_dance_type_danceType :selected');
                    addDanceType($(selected).attr('value'), $(selected).text());
                    removeSelectedFromList($(selected).attr('value'));
                    $(danceTypeChooser).dialog('close');
                },
                "Отменить": function() {
                    $(danceTypeChooser).dialog('close');
                }
            }
        });

        $('#dancer-photo').click(function(){
            $('#dancer_profile_photo').click();
        });

        $('#dancer_profile_danceType [selected="selected"]').each(function(){
            addDanceType($(this).attr('value'), $(this).text());
            removeSelectedFromList($(this).attr('value'));
        });

        $('#dance-types-block .dance-type .rmb').bind('click', removeDanceType());

        $('#dance-types-block .add-more').click(function(){
            $(danceTypeChooser).dialog('open');
            return false;
        })
    }

    initialize();
});