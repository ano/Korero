/* 
==========================================================================

  Jerso 1.0.1 - 12 Jan 2014
  by Simon Marussi
  http://codecanyon.net/item/jerso/6507331

==========================================================================
*/

$(document).ready(function()
{
	var $menu = $('.ewMenuColumn');

	if(getCookie('jerso')!=""){
		$menu.addClass(getCookie('jerso'));
	}else{
		$menu.addClass('stato1');
	}

	$menu.addClass('statoForzato');

	var $btn = $('<a/>')
		.html('')
		.attr('href','#')
		.addClass('button-mobile')
		.appendTo('body')
		.on('click',function(event)
		{
			event.preventDefault();

			if ($menu.is('.stato1')){
				$menu.removeClass('stato1');
				$menu.addClass('stato2');
				$menu.removeClass('statoForzato');
				createCookie("jerso", "stato2", 1); 
			}else{
				$menu.removeClass('stato2');
				$menu.addClass('stato1');
				$menu.removeClass('statoForzato');
				createCookie("jerso", "stato1", 1); 
			}
		});

	//looking for the table with breadcrumb
	$(".breadcrumb").closest("table").addClass('tbl_breadcrumb');

	//fix a bug in the export list buttons for the view pages
    if ( $('.ewListExportOptions > .ewExportOption').size() == 0) {
        $('.ewViewExportOptions').addClass('displaynone');
    };


});

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}





