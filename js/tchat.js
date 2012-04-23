var url = 'ajax/tchat.php';
var intervalGetMess = null;
var intervalRefresh = null;
var idm = null;
var tmpSansMess = 0;
var wait = 7;
var refresh = true;

$(function(){
    $('#scroll_mess').animate({scrollTop:9999});
    idm = $("#tchat form input[name=idm]").val();
    intervalGetMess = setInterval("getMessages("+idm+")", 7000);
    intervalRefresh = setInterval("refreshTime()", 20500);
    
    $("#tchat form").submit(function(){
        var message = $("#tchat form input[name=message]").val();
        
        if(message != '' && message != ' '){
            $('#tchat .loader').fadeIn(250);
            $('#tchat form').animate({'opacity':'0.2'}, 250);
            $("#tchat form input[name=message]").blur();

            $("#tchat form input[name=message]").val('');
            $.post(url, {a:'addMessage', message:message, idm:idm}, function(data){
                if(data.error == 'ok'){
                    clearInterval(intervalGetMess);
                    intervalGetMess = setInterval("getMessages("+idm+")", 7000);
                    getMessages(idm);
                } else if(data.error == 'message') {
                    addMessage(data.message.type, data.message.mess);
                } else {
                    addMessage('error', data.error);
                }

                $('#tchat .loader').fadeOut(250);
                $('#tchat form').animate({'opacity':'1'}, 250, 'linear', function(){
                    $("#tchat form input[name=message]").focus();
                });
            }, "json");
        }

        return false;
    });
});

function getMessages(idm){
    $.post(url, {a:'getMessages', idm:idm}, function(data){
        if(data.error == 'ok'){
            if(data.messages != '' || data.to_add != ''){
                $('#all_mess .grp_mess:last .date_mess').before(data.to_add);
                $('#all_mess').append(data.messages);
                $('#scroll_mess').animate({scrollTop:9999});
                
                if(data.participants != ''){
                    $('#participants').empty().append(data.participants);
                }
                
                if(tmpSansMess > 60){
                    clearInterval(intervalGetMess);
                    intervalGetMess = setInterval("getMessages("+idm+")", 7000);
                    wait = 7;
                    
                    if(tmpSansMess > 15*60){
                        $('#alert_inactif').hide();
                    }
                }
                
                if(!refresh){
                    intervalRefresh = setInterval("refreshTime()", 20000);
                    refresh = true;
                }
                
                tmpSansMess = 0;
            } else {
                tmpSansMess += wait;
                
                if(tmpSansMess > 15*60){
                    clearInterval(intervalGetMess);
                    $('#scroll_mess').next('form').after('<span id="alert_inactif">Vous et votre interlocuteur êtes inactifs, le tchat ne se met plus à jour automatiquement.</span>');
                } else if(tmpSansMess > 5*60){
                    clearInterval(intervalGetMess);
                    intervalGetMess = setInterval("getMessages("+idm+")", 60000);
                    wait = 60;
                } else if(tmpSansMess > 60){
                    clearInterval(intervalGetMess);
                    intervalGetMess = setInterval("getMessages("+idm+")", 20000);
                    wait = 20;
                }
            }
        } else if(data.error == 'message') {
            addMessage(data.message.type, data.message.mess);
        } else {
            addMessage('error', data.error);
        }
    }, "json");
}


function refreshTime(){
    if($('#all_mess .grp_mess .date_mess.to_refresh').size() > 0){
        refresh = true;
        
        $('#all_mess .grp_mess .date_mess.to_refresh').each(function(){
            var elem = $(this);
            var date = '';
            var timestamp = elem.attr('class');
            timestamp = parseInt(timestamp.replace('date_mess to_refresh t_', ''));
            date = parse_date(timestamp, true);
            elem.empty().append(date);
        });
    } else {
        refresh = false;
        clearInterval(intervalRefresh);
    }
}