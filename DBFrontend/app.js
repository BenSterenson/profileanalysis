(function () {
  'use strict';

  var app = angular.module('profileanalysis', ['chart.js', 'ui.bootstrap']);

  app.config(function (ChartJsProvider) {
    // Configure all charts
    ChartJsProvider.setOptions({
      colours: ['#97BBCD', '#DCDCDC', '#F7464A', '#46BFBD', '#FDB45C', '#949FB1', '#4D5360'],
      responsive: true
    });
  });

  app.controller('MainCtrl', function ($scope) {

      $scope.selectedUser = {};

      $scope.editUser = function (user) {
          $scope.selectedUser = user;
      }

    // Eye Color Data
    $scope.eyeColorData = [300, 500, 100, 200];
    $scope.eyeColorChartColors = ['#0000FF', '#964B00', '#808080', '#00FF00'];
    $scope.eyeColorLabels = [];
    $scope.eyeColorChartColors.forEach(function (color) {
        $scope.eyeColorLabels.push(ntc.name(color)[1]);
    });

    // Hair Color
    $scope.hairColorData = [100, 500, 100, 200, 300];
    $scope.hairColorChartColors = ['#FFFF00', '#000000', '#FF681F', '#00FF00', '#964B00'];
    $scope.hairColorLabels = [];
    $scope.hairColorChartColors.forEach(function (color) {
        $scope.hairColorLabels.push(ntc.name(color)[1]);
    });

    // Age
    $scope.ageData = [[40, 90, 60, 30, 10]];
    $scope.ageLabels = ['18-24', '25-34', '35-44', '45-54', '55+'];

    // Gender
    $scope.genderData = [100,105];
    $scope.genderLabels = ['Male', 'Female'];

    // Smiles
    $scope.smilesData = [100, 1];
    $scope.smilesLabels = ['Yes', 'No'];
    $scope.smilesChartColors = ['#0099ff', '#ff0000'];

    // Has Glasses
    $scope.glassesData = [100, 605];
    $scope.glassesLabels = ['Yes', 'No'];
    $scope.glassesChartColors = ['#000099', '#33ccff'];

   
  });


})();
