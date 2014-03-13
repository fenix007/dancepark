$(document).ready(function(){
    function addDanceType(value, label) {
        value = parseInt(value);
        var val = $('#dancer_profile_danceTypesHidden').attr('value');
        var needSet = true;
        val = JSON.parse(val);
        for (var i = 0; i < val.length; ++i) {
            if (val[i].id == value) {
                needSet = false;
            }
        }
        if (needSet) {
            val[val.length] = {id: value, pro:false};
            $('#dancer_profile_danceTypesHidden').attr('value', JSON.stringify(val));
        }
        addDanceTypeToBlock(value, label);
    }

    function removeSelectedFromList(value) {
        $('#dancer_dance_type_danceType [value=' + value + ']').remove();
    }

    function addButtonsToList(value, label) {
        $('#dancer_dance_type_danceType').append('<option value="' + value + '">' + label +'</option>');
    }

    function addDanceTypeToBlock(value, label) {
        var block = $('#dance-types-block');
        var isProInfo = JSON.parse($('#dancer_profile_pro_info').val());
        var types = $(block).find('.types');
        var element = document.createElement('div');
        var lab = document.createElement('span');

        var link = document.createElement('a');
        link.setAttribute('class', 'rbm');
        link.setAttribute('href', '#' + value);
        link.appendChild(document.createTextNode($(block).find('div.rmb').text()));
        $(link).click(removeDanceType);


        var pro = document.createElement('input');
        pro.setAttribute('type', 'checkbox');
        pro.setAttribute('class','pro-btn');
        pro.setAttribute('data-id', value);
        if (isProInfo[value] == true) {
            pro.setAttribute('checked', 'checked');
        }

        lab.appendChild(document.createTextNode(label));
        element.appendChild(lab);
        element.appendChild(pro);
        element.appendChild(link);
        var clb = document.createElement('div');
        clb.setAttribute('class', 'cl-b');
        element.appendChild(clb);
        element.setAttribute('class', 'dance-type');

        $(types).append($(element));
        $(pro).iphoneStyle({
            checkedLabel: 'PRO',
            uncheckedLabel: 'AM',
            background: "#ccc",
            duration: 100,
            onChange: isProChanged
        });
    }

    var removeDanceType = function(){
        var val = $(this).attr('href');
        if (val) {
            val = val.substr(1);
            var types = $('#dancer_profile_danceTypesHidden').attr('value');
            types = types.replace('\\', '');
            types = JSON.parse(types);
            for (var i = 0; i < types.length; ++i) {
                if (types[i].id == val) {
                    types.splice(i, 1);
                }
            }
            $('#dancer_profile_danceTypesHidden').attr('value', JSON.stringify(types));
            var label = $(this).parent().find('span').text();
            addButtonsToList(val, label);
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
                'Добавить': function() {
                    var selected = $('#dancer_dance_type_danceType :selected');
                    addDanceType($(selected).attr('value'), $(selected).text());
                    removeSelectedFromList($(selected).attr('value'));
                    $(danceTypeChooser).dialog('close');
                },
                'Отмена': function() {
                    $(danceTypeChooser).dialog('close');
                }
            }
        });

        $('#dancer-photo').click(function(){
            $('#dancer_profile_photo').click();
        });

        
        $('#dancer_profile_is_pro').iphoneStyle({
            checkedLabel: 'PRO',
            uncheckedLabel: 'AM',
            background: "#ccc",
            duration: 100
        });

        function readURL(input) {
            var endHeight = 120;
            var endWidth = 120;

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#dancer-photo').attr('width', '');
                    $('#dancer-photo').attr('height', '');
                    $('#dancer-photo').attr('src', e.target.result);
                    var width = $('#dancer-photo').width();
                    var height = $('#dancer-photo').height();
                    if (width / endWidth > height / endHeight) {
                        var horizontalOffset = (width / (height / endHeight) - endWidth) / 2;
                        $('#dancer-photo').attr('width', width / (height / endHeight));
                        $('#dancer-photo').attr('height', endHeight);
                        $('#dancer-photo').css('left', -1 * horizontalOffset);
                    } else {
                        var verticalOffset = (height / (width / endWidth) - endHeight) / 2;
                        $('#dancer-photo').attr('height', height / (width / endWidth));
                        $('#dancer-photo').attr('width', endWidth);
                        $('#dancer-photo').css('top', -1 * verticalOffset);
                    }

                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#dancer_profile_photo').change(function(){
            var path = $(this).val();
            if (path.length > 0) {
                readURL(this);
            }
        })

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

function isProChanged(elem, isChacked) {
    var id = $(elem).attr('data-id');
    var isProInfo = JSON.parse($('#dancer_profile_pro_info').val());
    isProInfo[id] = isChacked;
    $('#dancer_profile_pro_info').val(JSON.stringify(isProInfo));
    var val = $('#dancer_profile_danceTypesHidden').attr('value');
    val = JSON.parse(val);
    for (var i = 0; i < val.length; ++i) {
        if (val[i].id == id) {
            val[i].pro = isChacked;
        }
    }
    $('#dancer_profile_danceTypesHidden').attr('value', JSON.stringify(val));
}