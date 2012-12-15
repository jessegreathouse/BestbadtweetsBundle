function resizeDashboard() {
    var windowHeight = $(window).height();
    $('.details-pane').css('height', (windowHeight - 120) + 'px');
    $('.pane-components').css('height', (windowHeight - 151) + 'px');
}

function getParameterByName(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.href);
  if(results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}

function addLinkToHash(text) {
    var exp = /(^|\s)#(\w+)/g;
    return text.replace(exp,"$1<a href=\"/search?q=%23$2\">#$2</a>"); 
}

function addLinkToAmp(text) {
    var exp = /(^|\s)@(\w+)/g;
    return text.replace(exp,"$1<a target=\"_blank\" href=\"http://www.twitter.com/$2\">@$2</a>"); 
    
}

function replaceURLWithHTMLLinks(text) {
    var exp = /(\b(https?|ftp|file):\/\/)([-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(exp,"<a target=\"_blank\" href='$1$3'>$3</a>"); 
}

function searchTextBold(text, query) {
    return text.replace(query,"<strong>" + query + "</strong>"); 
}

jQuery(document).ready(function(){
    resizeDashboard();
    $(window).resize(function () {
        resizeDashboard();
        return false;
    });
    $('.details-pane').hide();
    
    function loadTweet(showOrHide) {
        $('.details-pane').toggle(showOrHide);
    }
    
    function loadUser(showOrHide) {
        $('.details-pane').toggle(showOrHide);
    }
    
    $('.glass').live('click', function() {
        $('#search-form').submit();
        return false;
    });
    
    $('.pane-close').live('click', function() {
        $('.details-pane').toggle(false);
    });
    
    $('.dropdown-link > a').live('click', function() {
        $(this).next('.drop-down').toggle();
        return false;
    });
    
    $('.drop-down').live('mouseleave', function() {
        $(this).toggle();
    });
    
    $('#top-bar .loggedin').live('click', function() {
        $('#top-bar .dropdown').toggle();
        return false;
    });

    $('#top-bar .loggedout').live('click', function() {
        window.location = '/secured';
        return false;
    });
    
    $('#top-bar .dropdown').live('mouseleave', function() {
        $(this).toggle();
    });
    
    $('.user-dropdown li a').live('click', function() {
        location.href = $(this).attr('href');
        return false;
    });

    $('.stream-item .tweet, .stream-item .more, .dashboard-list-item, .dashboard-stream-item .tweet, .tweet-timestamp').live('click', function() {
        var hidden = $('.details-pane:hidden');
        var id_str = $(this).attr('data-tweet-id');
        var showOrHide = false;
        if ($('.details-pane').length == 0) {
            window.location = "/tweet?id_str=" + id_str;
            return false;
        }
        if ( hidden.length > 0 || ($('.details-pane-tweet').attr('data-tweet-id') != id_str)) {
          showOrHide = true;
        }
        $('.details-pane').load("/tweet?id_str=" + id_str, {}, loadTweet(showOrHide));
    });
    
    $('.stream-item .user-content-medium, .user-profile-link').live('click', function() {
        var hidden = $('.details-pane:hidden');
        var id_str = $(this).attr('data-user-id');
        var showOrHide = false;
        if ($('.details-pane').length == 0) {
            window.location = "/user?id_str=" + id_str;
            return false;
        }
        if ( hidden.length > 0 || ($('.profile-pane').attr('data-user-id') != id_str)) {
          showOrHide = true;
        }
        $('.details-pane').load("/user?id_str=" + id_str, {}, loadUser(showOrHide));
        return false;
    });
    
    $("abbr.timeago").each(function(i, item) {
        var tweetDate = new Date($(item).attr('title'));
        if (tweetDate.addDays(2) > Date.today()) {
            $(item).timeago();
        } else {
            var formattedDate = new Date($(item).attr('title'));
            $(item).text(formattedDate.toString('d MMM'));
        }
    });
    
    $('.condensed').live('click', function() {
        $(this).removeClass('condensed');
    });
});
