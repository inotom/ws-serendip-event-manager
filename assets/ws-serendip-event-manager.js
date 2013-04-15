//
//
// file created in 2013/03/21 10:40:44.
// LastUpdated :2013/03/28 09:38:40.
// author iNo
//

(function($) {
    if (typeof(serendipEventManagerLanguage) === 'undefined') {
        serendipEventManagerLanguage = {
            address    : 'Addr',
            phone      : 'Tel',
            other      : 'Other'
        };
    }

    var inputPlace = function() {
        var id = parseInt($('#select-place').attr('value'));
        if (id > 0) {
            insertPlace(id);
        } else if (id === 0) {
            insertTemplace();
        }
    };

    var insertTemplace = function() {
        $('#event_place').attr('value', '\n<dl class="ws-serendip-event-manager-place-info">\n<dt>' + serendipEventManagerLanguage.address + '</dt><dd></dd>\n<dt>' + serendipEventManagerLanguage.phone + '</dt><dd></dd>\n<dt>' +serendipEventManagerLanguage.other + '</dt><dd></dd>\n</dl>');
    };

    var insertPlace = function(id) {
        $.getJSON('edit.php', { 'json' : 'true', 'plid' : id }, function(json) {
            $('#event_place').attr('value', getTag(json));
        });
    };

    var getTag = function(json) {
        var SEP = '&#65306;';
        var str = getLinkedLabelTag(json.name, json.web_url);
        str += '\n<dl class="ws-serendip-event-manager-place-info">';
        var addressTag = getLinkedLabelTag(json.address, json.map_url);
        str += addressTag.length > 0 ? '\n<dt>' + serendipEventManagerLanguage.address + '</dt><dd>' + (json.zip.length > 0 ? '&#12306;' +json.zip + ' ' : '') + addressTag + '</dd>' : '';
        str += json.tel.length > 0 ? '\n<dt>' + serendipEventManagerLanguage.phone + '</dt><dd>' + json.tel + '</dd>' : '';
        str += json.desc.length > 0 ? '\n<dt>' + serendipEventManagerLanguage.other + '</dt><dd>' + parseDesc(json.desc) + '</dd>' : '';
        str += '\n</dl>';
        return str;
    };

    var getLinkedLabelTag = function(label, url) {
        var str = '';
        if (label.length > 0) {
            str = url.match(/^https?:\/\//) ? '<a href="' + url + '" rel="external">' + label + '</a>' : label;
        }
        return str;
    };

    var parseDesc = function(desc) {
        var str = desc;
        return str.replace(/\\"/g, '"').replace(/\n/g, "<br />\n");
    };

    $(function() {
        $('#datepicker-start-date').datepicker();
        $('#datepicker-start-date').change(function() {
            $('#datepicker-end-date').attr('value', $('#datepicker-start-date').attr('value'));
        });
        $('#datepicker-end-date').datepicker();
        $('#input-place').click(inputPlace);
        $.getJSON('edit.php', { 'json' : 'true', 'plid' : 0 }, function(json) {
            $('#select-place').html('<option value="0" selected="selected">-</option>');
            for (var i = 0, len = json.length; i < len; i++) {
                var item = json[i];
                $('#select-place').append('<option value="' + item.ID + '">' + item.name + '</option>');
            }
        });
    });
})(jQuery);

/*
vim:fdl=0 fdm=marker:ts=4 sw=4 sts=0:
*/
