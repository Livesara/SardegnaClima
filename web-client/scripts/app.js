'use strict';

/**
 * @ngdoc overview
 * @name bueleApp
 * @description
 * # bueleApp
 *
 * Main module of the application.
 */
console.log("hello from app.js file");
angular
  .module('sardegnaclima', [
        'ngAnimate',
        'ngCookies',
        'ngResource',
        'ngRoute',
        'ngTouch',
        'ngSanitize',
        'hmTouchEvents',
        'mobile-angular-ui'
    'ui.bootstrap',
    
  ])
    .config(['$routeProvider', function ($routeProvider) {
    $routeProvider
      .when('/main1/:station_id', {
        templateUrl: 'views/main1.html',
        controller: 'MainCtrl',
            resolve: {
                currentStation: function(Stations2, $route){
                    if(Stations2.model )
                        return _.findWhere(Stations2.model, {id: parseInt($route.current.params.station_id)});
                    else
                        return null;
                }
            }
      })
        .when('/station/:id', {
            templateUrl: 'views/station_details.html',
            controller: 'StationDetailsCtrl',
            resolve: {
                station: function(Stations2, $route){
                    return _.findWhere(Stations2.model, {id: parseInt($route.current.params.id)});
                }
            }
        })
      .when('/credits/',{
        templateUrl: 'views/credits.html',
        controller: 'CreditsCtrl'
      })
      .otherwise({
        redirectTo: '/main1/0'
      });
  }])

    
    .run(function($rootScope) {
        $rootScope.$on("$routeChangeStart", function(){
            $rootScope.loading = true;
        });

        $rootScope.$on("$routeChangeSuccess", function(){
            $rootScope.loading = false;
        });



}).factory('App',function() {
        return{
            mode: "prod",
            status: 'LOADING',
            //baseUrl: "http://www.sardegna-clima.it/stazioni/",
            baseUrl: "../",
            configurations: {
                currentMapZoom : 7
            }
        };
    }).filter('scdate', function() {
        return function(date) {
            return moment(date).lang("it").fromNow();
        };
    });


