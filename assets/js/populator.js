'use strict';

var app = angular.module('store',  ['ngSanitize']).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('[[').endSymbol(']]');
});


app.controller('StoreCtrl', function ($scope, $http, $timeout, $interval) {
    var vm = this;


    $scope.availableMaxResults = [
        { id: 10, name: 10 },
        { id: 25, name: 25 },
        { id: 50, name: 50 },
        { id: 100, name: 100 },
        { id: 250, name: 250 },
        { id: 500, name: 500 },
        { id: 1000, name: 1000 },
        { id: 2000, name: 2000 },
        { id: 3000, name: 3000 }
    ];
    // Pre-select city by id
    $scope.maxResults = 100;
    $scope.currentMaxResults = 100;


    $scope.init = function(id, entity , child , childEntity,childMethod,parent)
    {

        $scope.currentSearchCount = 0;
        $scope.childSearchCount = 0;

        $scope.id = id;
        $scope.entity = entity;
        $scope.child = child;
        $scope.childEntity = childEntity;
        $scope.childMethod = childMethod;
        $scope.parent = parent;


        $scope.setCounts();
    };

    $scope.setCounts = function() {

        var url = Routing.generate('app_ajax_getCounts',{
            id:$scope.id,
            entity:$scope.entity,
            child:$scope.child,
            childEntity:$scope.childEntity

        });


        $http.post(url).then(function successCallback(response) {
            $scope.availableCount = response.data["availableCount"];
            $scope.currentCount = response.data["currentCount"];

        }, function errorCallback(response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });
    }


    $scope.currentData = {};

    $scope.currentData.removeAll = function(item, event) {
        $scope.currentSearchCountHtml = '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>';
        $scope.currentProductsID = [];
        angular.forEach($scope.currentProducts, function(value, key) {
            if(value["selected"] == true)
                $scope.currentProductsID.push(value['cId']);
        });


        var url = Routing.generate('app_ajax_removeAll',{
            id:$scope.id,
            entity:$scope.entity,
            child:$scope.child,
            childEntity:$scope.childEntity,
            childMethod:$scope.childMethod,
            datas:JSON.stringify($scope.currentProductsID)
        });


        $http.post(url).then(function successCallback(response) {
            $scope.currentProducts = [];
            $scope.setCounts();
            $scope.currentSearchCountHtml = '';
            $scope.currentSearchCount = '?';

        }, function errorCallback(response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });
    }
    $scope.currentData.remove = function(item, event) {
        $scope.currentSearchCountHtml = '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>';
        $scope.currentProductsID = [];
        angular.forEach($scope.currentProducts, function(value, key) {
            if(value["selected"] == true)
                $scope.currentProductsID.push(value['cId']);
        });


        var url = Routing.generate('app_ajax_removeChild',{
            id:$scope.id,
            entity:$scope.entity,
            child:$scope.child,
            childEntity:$scope.childEntity,
            childMethod:$scope.childMethod,
            datas:JSON.stringify($scope.currentProductsID)
        });


        $http.post(url).then(function successCallback(response) {
            $scope.currentProducts = [];
            $scope.setCounts();
            $scope.currentSearchCountHtml = '';
            $scope.currentSearchCount = '?';

        }, function errorCallback(response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });
    }
    $scope.currentData.doSearch = function(item, event) {
        $scope.currentSearchCountHtml = '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>';
        $scope.currentSearchCount = '?';
        var url = Routing.generate('app_ajax_searchCurrentChild',{
            id:$scope.id,
            entity:$scope.entity,
            child:$scope.child,
            childEntity:$scope.childEntity,
            maxResults:$scope.currentMaxResults,
            searchString:$scope.currentSearchString
        });

        $http.post(url).then(function successCallback(response) {

            $scope.currentProducts = [];


            angular.forEach(response.data, function(value, key) {
                value["selected"]=true;
                value["name"]= value["brand"] + " " + value["name"] + " (" + value["categories"] + ")" ;
                $scope.currentProducts.push(value);
            });
            $scope.currentSearchCount = $scope.currentProducts.length;
            $scope.currentSearchCountHtml = '';

        }, function errorCallback(response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });

    }

    $scope.myData = {};


    $scope.myData.add = function(item, event) {
        $scope.childSearchCountHtml = '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>';
        $scope.productsID = [];
        angular.forEach($scope.products, function(value, key) {
            if(value["selected"] == true)
                $scope.productsID.push(value['id']);
        });

        var url = Routing.generate('app_ajax_addChild',{
            id:$scope.id,
            entity:$scope.entity,
            child:$scope.child,
            childEntity:$scope.childEntity,
            childMethod:$scope.childMethod,
            datas:JSON.stringify($scope.productsID)
        });


        $http.post(url).then(function successCallback(response) {
            $scope.products = [];
            $scope.setCounts();
            $scope.childSearchCountHtml = '';
            $scope.childSearchCount = '?';
        }, function errorCallback(response) {
        });

    }


    $scope.myData.addAll = function(item, event) {
        $scope.childSearchCountHtml = '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>';
        $scope.productsID = [];
        angular.forEach($scope.products, function(value, key) {
            if(value["selected"] == true)
                $scope.productsID.push(value['id']);
        });

        var url = Routing.generate('app_ajax_addAll',{
            id:$scope.id,
            entity:$scope.entity,
            child:$scope.child,
            childEntity:$scope.childEntity,
            childMethod:$scope.childMethod,
            parent:$scope.parent,
            datas:JSON.stringify($scope.productsID)
        });


        $http.post(url).then(function successCallback(response) {
            $scope.products = [];
            $scope.setCounts();
            $scope.childSearchCountHtml = '';
            $scope.childSearchCount = '?';
        }, function errorCallback(response) {
        });

    }


    $scope.myData.doSearch = function(item, event) {
        $scope.childSearchCountHtml = '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i>';
        $scope.childSearchCount = '?';




        var url = Routing.generate('app_ajax_searchChild',{
            id:$scope.id,
            entity:$scope.entity,
            child:$scope.child,
            childEntity:$scope.childEntity,
            maxResults:$scope.maxResults,
            searchString:$scope.searchString
        });



        $http.post(url).then(function successCallback(response) {
            $scope.products = [];
            angular.forEach(response.data, function(value, key) {
                value["selected"]=true;
                value["name"]=value["brand"] + " " + value["name"] + " (" + value["categories"] + ")" ;
                $scope.products.push(value);
            });
            $scope.childSearchCount = $scope.products.length;
            $scope.childSearchCountHtml = '';

        }, function errorCallback(response) {
        });
    }



});
