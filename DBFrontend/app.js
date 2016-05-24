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
  app.service('UsersService', function ($http) {
	  this.getPhotos = function (start,stop,gender, eyeColor, hairColor, hasBeard, hasGlasses, hasSmile ,age) {
          return $http.get('../php/api/getphotos/'+start+'/'+stop+'/'+gender+'/'+eyeColor+'/'+hairColor+'/'+hasBeard+'/'+hasGlasses+'/'+hasSmile+'/'+age).then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on getPhotos!");
			});
      }
  })
  app.service('ChartService', function ($http) {
	  	  
		// Gets json object and returns object of keys and values
		function getKeysAndValuesOfObject(obj){
			var keys = []
			var values = []
			for (var key in obj){
				keys.push(key);
				values.push(obj[key]);
			}
			return  {keys:keys,values:values};
		}
	  
      this.getEyeColors = function () {
          return $http.get('../php/api/geteyecolors').then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
			}, function errorCallback(response) {
				alert("Error on GetEyeColors!");
			});
      }
	  
	  this.getHairColors = function () {
          return $http.get('../php/api/getHairColors').then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
				}, function errorCallback(response) {
				alert("Error on getHairColors!");
			});
      }
	  
	  this.getGender = function () {
          return $http.get('../php/api/getGender').then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
			}, function errorCallback(response) {
				alert("Error on getGender!");
			});
      }
	  
	  this.getGlasses = function () {
          return $http.get('../php/api/getGlasses').then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
			}, function errorCallback(response) {
				alert("Error on getGlasses!");
			});
      }
	  
	  this.getBeard = function () {
          return $http.get('../php/api/getBeard').then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
			}, function errorCallback(response) {
				alert("Error on getBeard!");
			});
      }
	  
	  this.getSmile = function () {
          return $http.get('../php/api/getSmile').then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
			}, function errorCallback(response) {
				alert("Error on getSmile!");
			});
      }
	  
	  this.getAge = function () {
          return $http.get('../php/api/getAge').then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
			}, function errorCallback(response) {
				alert("Error on getAge!");
			});
      }
  });
  app.controller('MainCtrl', function ($scope,$http, $uibModal, ChartService,UsersService) {

	// Init
	$scope.size = 12;
	$scope.start = 0;
	$scope.stop = $scope.size;

	// Handle Facebook
	$scope.loggedOnUser = {};
	$scope.login = function (accessToken) {
	  FB.api('/me', function (response) {
		  $scope.loggedOnUser = response;
		  console.log(JSON.stringify(response));
		  $scope.$apply();
	  });
	  
	}

	$scope.successRun = "none";
	$scope.run = function(i,stop){
	  $http.get('../php/api/addattributes/'+i).then(function successCallback(response) {
		$scope.successRun = i;
		if(i<=stop){
			$scope.run(i+1,stop);
		}
	  },
	  function errorCallback(response) {
		$scope.successRun = i + " Error: " + response.statusText;
	  });
	}

	$scope.runApi = function () {
	  $scope.run($scope.from,$scope.to);
	  
	}
      // Load the data


	
	function convertColorsToViewable(colors){
		var newArray = []
		var colorDict = {
			"red":"#d92626",
			"green":"#7EE831",
			"yellow":"#ffbf00",
			"blue":"#007FFF",
			"orange":"#ff8000",
			"purple":"#8000ff",
			"pink":"#ff0040",
			"brown":"#a64a2b",			
			"black":"#000",
			"gray":"#808080",
			"white":"#fff"
		}
		for (var i in colors){
			newArray[i] = colorDict[colors[i]];
		}
		return newArray;
	}
	
    // Eye Color Data
	$scope.eyeColorFilter = -1;
	$scope.eyeColorData =[];
	$scope.eyeColorLabels =[];		
	ChartService.getEyeColors().then(function successCallback(data) {
		$scope.eyeColorData = data.values;
		$scope.eyeColorLabels = data.keys;
		$scope.eyeColorChartColors = convertColorsToViewable(data.keys);
	});
	

    // Hair Color
	$scope.hairColorFilter = -1;
	$scope.hairColorData =[];
	$scope.hairColorLabels =[];
	ChartService.getHairColors().then(function successCallback(data) {
		$scope.hairColorData = data.values;
		$scope.hairColorLabels = data.keys;
		$scope.hairColorChartColors = convertColorsToViewable(data.keys);
	});

    // Age
	$scope.ageFilter = -1;
	$scope.ageData =[];
	$scope.ageLabels =[];
	ChartService.getAge().then(function successCallback(data) {
		$scope.ageData = [data.values];
		$scope.ageLabels = data.keys;
	});

    // Gender
	$scope.genderFilter = -1;
	$scope.genderData =[];
	$scope.genderLabels =[];
	ChartService.getGender().then(function successCallback(data) {		
		$scope.genderData = data.values;
		$scope.genderLabels = data.keys;
		$scope.genderChartColors = ['#ffc0cb', '#7AC8F5'];
	});
    

    // Smiles
	$scope.smilesFilter = -1;
	$scope.smilesData =[];
	$scope.smilesLabels =[];
	ChartService.getSmile().then(function successCallback(data) {		
		$scope.smilesData = data.values;
		$scope.smilesLabels = data.keys;
		$scope.smilesChartColors = ['#004d4d', '#99ffff'];
	});

    // Has Glasses	
	$scope.glassesFilter = -1;
	$scope.glassesData =[];
	$scope.glassesLabels =[];
	ChartService.getGlasses().then(function successCallback(data) {		
		$scope.glassesData = data.values;
		$scope.glassesLabels = data.keys;
		$scope.glassesChartColors = ['#39ac39', '#d9f2d9'];
	});

    // Has Beard
	$scope.beardFilter = -1;
	$scope.beardData =[];
	$scope.beardLabels =[];
	ChartService.getBeard().then(function successCallback(data) {		
		$scope.beardData = data.values;
		$scope.beardLabels = data.keys;
		$scope.beardChartColors = ['#003399', '#99bbff'];
	});

	
		
	// Get Photos
	$scope.users = [];
	$scope.getPhotos = function(start,stop){
		if($scope.start <=0){
			$scope.start = 0;
			$scope.stop = $scope.size;
		}
		// TODO: check maximum right
		$scope.start = start;
		$scope.stop = stop;
		UsersService.getPhotos($scope.start,$scope.stop,$scope.genderFilter, $scope.eyeColorFilter, $scope.hairColorFilter, $scope.beardFilter, $scope.glassesFilter, $scope.smilesFilter ,$scope.ageFilter).then(function successCallback(data) {
			$scope.users = data;
		});
	}
	$scope.getPhotos($scope.start,$scope.stop);
	
	$scope.open = function (user) {

		var modalInstance = $uibModal.open({
		  animation: true,
		  templateUrl: 'image-modal.html',
		  controller: 'ModalCtrl',
		  size: 'lg',
		  resolve: {
			user: function () {
			  return user;
			},
			labels: function(){
				return {'hairColorLabels':$scope.hairColorLabels,'eyeColorLabels':$scope.eyeColorLabels};
			}
		  }
		});

	  };
	  


});
  app.controller('ModalCtrl', function ($scope,$http, $uibModalInstance, user, labels) {
		
		var colorArr = ["red","green","yellow","blue","orange","purple","pink","brown","black","gray","white"];
	  
		$scope.user = user;
		
		// Fix Hair and eye color
		$scope.user.EyeColorFix = colorArr[$scope.user.EyeColor];
		$scope.user.HairColorFix = colorArr[$scope.user.HairColor];
		$scope.eyeColorLabels = labels.eyeColorLabels;
		$scope.hairColorLabels = labels.hairColorLabels;

		$scope.ok = function () {
			$uibModalInstance.close();
		};

		$scope.cancel = function () {
			$uibModalInstance.dismiss('cancel');
		};
  });

})();
