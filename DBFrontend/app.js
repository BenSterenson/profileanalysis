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
	  
	  this.getColor = function(getVal,val){		  
		var colorArr = ["red","green","yellow","blue","orange","purple","pink","brown","black","gray","white"];
		if(!getVal){ // val is index in this case
			return colorArr[val];
		}
		else
		{ // val is the value and we return the index
			for (var i in colorArr){
				if(colorArr[i] == val){
					return i;
				}
			}
		}
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
	$scope.eyeClick = function (elm, evt) {
		$scope.eyeColorFilter = UsersService.getColor(true,elm[0].label);
		$scope.getPhotos();
	};
	

    // Hair Color
	$scope.hairColorFilter = -1;
	$scope.hairColorData =[];
	$scope.hairColorLabels =[];
	ChartService.getHairColors().then(function successCallback(data) {
		$scope.hairColorData = data.values;
		$scope.hairColorLabels = data.keys;
		$scope.hairColorChartColors = convertColorsToViewable(data.keys);
	});
	$scope.hairClick = function (elm, evt) {
		$scope.hairColorFilter = UsersService.getColor(true,elm[0].label);
		$scope.getPhotos();
	};

    // Age
	$scope.ageFilter = -1;
	$scope.ageData =[];
	$scope.ageLabels =[];
	ChartService.getAge().then(function successCallback(data) {
		$scope.ageData = [data.values];
		$scope.ageLabels = data.keys;
	});
	$scope.ageClick = function (elm, evt) {
		$scope.ageFilter = $scope.ageLabels.indexOf(elm[0].label);
		$scope.getPhotos();
	};

    // Gender
	$scope.genderFilter = -1;
	$scope.genderData =[];
	$scope.genderLabels =[];
	ChartService.getGender().then(function successCallback(data) {		
		$scope.genderData = data.values;
		$scope.genderLabels = data.keys;
		$scope.genderChartColors = ['#ffc0cb', '#7AC8F5'];
	});
    $scope.genderClick = function (elm, evt) {
		$scope.genderFilter = $scope.genderLabels.indexOf(elm[0].label);
		$scope.getPhotos();
	};

    // Smiles
	$scope.smilesFilter = -1;
	$scope.smilesData =[];
	$scope.smilesLabels =[];
	ChartService.getSmile().then(function successCallback(data) {		
		$scope.smilesData = data.values;
		$scope.smilesLabels = data.keys;
		$scope.smilesChartColors = ['#004d4d', '#99ffff'];
	});
	$scope.smilesClick = function (elm, evt) {
		$scope.smilesFilter = $scope.smilesLabels.indexOf(elm[0].label);
		$scope.getPhotos();
	};
	
    // Has Glasses	
	$scope.glassesFilter = -1;
	$scope.glassesData =[];
	$scope.glassesLabels =[];
	ChartService.getGlasses().then(function successCallback(data) {		
		$scope.glassesData = data.values;
		$scope.glassesLabels = data.keys;
		$scope.glassesChartColors = ['#39ac39', '#d9f2d9'];
	});
	$scope.glassesClick = function (elm, evt) {
		$scope.glassesFilter = $scope.glassesLabels.indexOf(elm[0].label);
		$scope.getPhotos();
	};

    // Has Beard
	$scope.beardFilter = -1;
	$scope.beardData =[];
	$scope.beardLabels =[];
	ChartService.getBeard().then(function successCallback(data) {		
		$scope.beardData = data.values;
		$scope.beardLabels = data.keys;
		$scope.beardChartColors = ['#003399', '#99bbff'];
	});
	$scope.beardClick = function (elm, evt) {
		$scope.beardFilter = $scope.beardLabels.indexOf(elm[0].label);
		$scope.getPhotos();
	};
	
		
	// Get Photos
	$scope.users = [];
	$scope.getPhotos = function(start,stop){
		if(start != undefined && stop != undefined){			
			if($scope.start <=0){
				$scope.start = 0;
				$scope.stop = $scope.size;
			}
			// TODO: check maximum right
			$scope.start = start;
			$scope.stop = stop;
		}
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
  app.controller('ModalCtrl', function ($scope,$http, $uibModalInstance, UsersService, user, labels) {
		
		$scope.user = user;
		
		// Fix Hair and eye color
		$scope.user.EyeColorFix = UsersService.getColor(false, $scope.user.EyeColor);
		$scope.user.HairColorFix = UsersService.getColor(false, $scope.user.HairColor);
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
