app.service('pusher',['pusher_settings','user','$route',
function(pusher_settings,user,TOKEN,$route){
    Pusher.logToConsole = true;

    var settings={
        cluster: pusher_settings.cluster,
        forceTLS: pusher_settings.forceTLS,
        authEndpoint:pusher_settings.authEndpoint,
        auth: {
            headers: {
              'X-CSRF-Token': TOKEN
            }
        }
    }

    var name='user-'+user.id;

    var pusher = new Pusher(pusher_settings.pusher_id,settings);
    var channel = pusher.subscribe(name);

    var event_routes={};

    this.on=function(event,callback){
        channel.bind(event,callback);
    }

    this.dismiss=function(){
        pusher.unsubscribe(name);
    }


}]);
