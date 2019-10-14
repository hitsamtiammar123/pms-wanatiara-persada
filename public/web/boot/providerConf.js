app.provider('cP',['routingProvider',function(routingProvider){
    this.$locationProvider=function($locationProvider){
        $locationProvider.html5Mode({
            enabled:false
        });
    }

    this.$routeProvider=function($routeProvider){
        var p=$routeProvider;
        var routelist=routingProvider.routelist
        for(r in routelist){
            var route=routelist[r];
            p=p.when(route.url,route.config);
        }
        p.otherwise({redirectTo:'/target-manajemen'});
    }

    this.$rootScopeProvider=function($rootScopeProvider){
        $rootScopeProvider.digestTtl(10);
    }

    this.formModalProvider=function(formModalProvider){
        formModalProvider.except(['alertModal','confirmModal','promptModal']);
    }


    this.$get=function(notifier,$route,alertModal){
        return {
            onRouteChangeStart:{
                default:function(event,next,current){
                    E('[ng-view]').html('Mohon Tunggu...');
                },
                RealisasiController:function(event,next,current){
                    notifier.flushNotifier('realisasi-content');
                    notifier.flushNotifier('add-content');
                },
                RealisasiGroup:function(event,next,current){
                    notifier.flushNotifier('realisasi-content');
                    notifier.flushNotifier('add-content');
                }
            },
            onlocationChangeStart:{
                changeNavBar:function(event,next,current){
                    var extendable_url=['realisasi','ikhtisar'];
                    var i=next.search('#!/');
                    var j=current.search('#!/');

                    var next_url=next.substr(i+3,next.length);
                    var current_url=current.substr(j+3,current.length);

                    var n_s=next_url.search('/');
                    var route_name=next_url.substr(0,n_s)?next_url.substr(0,n_s):next_url;

                    if(extendable_url.indexOf(route_name)!==-1){
                        for(var i in extendable_url){
                            var u=extendable_url[i];
                            var u_len=u.length;
                            var reg_str=u+'(\/\w+)*';
                            var pathReg=new RegExp(u);
                            if(pathReg.test(next_url)){
                                next_url=next_url.substr(0,u_len);
                            }
                        }
                    }

                    var next_route='#'+next_url;
                    var curr_route='#'+current_url;

                    var j_curr=E('[tab-target="'+curr_route+'"]');
                    var j_next=E('[tab-target="'+next_route+'"]');

                    E('.tab-selected').removeClass('tab-selected');
                    j_next.addClass('tab-selected');
                },

                hideModal:function(event,next,current){
                    alertModal.hide();
                },
                scrollToTop:function(event,next,current){
                    E(window).scrollTop(0);
                }
            }

    }};
}]);
