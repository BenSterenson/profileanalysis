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
	  
	  this.addComment = function(photoID, facebookId,comment){
		   return $http.get('../php/api/insertComment/'+photoID+'/'+facebookId+'/'+comment).then(function successCallback(response) {
				return response;
			}, function errorCallback(response) {
				alert("Error on addComment!");
			});
	  }
	  
	  this.getComments = function(photoID){
		   return $http.get('../php/api/getPhotoComments/'+photoID).then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on getComments!");
			});
	  }
	  
		this.login = function(facebookId, firstName,lastName,numOfLikes){
		   return $http.get('../php/api/login/'+facebookId+'/'+firstName+'/'+lastName+'/'+numOfLikes).then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on addComment!");
			});
	  }
	  
	  this.extractAttributes = function(photoId, iteration){
		   return $http.get('../php/api/extractAttributes/'+photoId+'/'+iteration).then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on extractAttributes!");
			});
	  }
	  
	  this.insertAttributes = function(photoId, gender, eyeColor, hairColor, hasBeard, hasGlasses, hasSmile ,age){
		   return $http.get('../php/api/insertAttributes/'+photoId+'/'+gender+'/'+eyeColor+'/'+hairColor+'/'+hasBeard+'/'+hasGlasses+'/'+hasSmile+'/'+age).then(function successCallback(response) {
				return response;
			}, function errorCallback(response) {
				alert("Error on insertAttributes!");
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
	  
      this.getChart = function (name,args) {
          return $http.get('../php/api/'+name+'/'+args.join("/")).then(function successCallback(response) {
				return getKeysAndValuesOfObject(JSON.parse(response.data));
			}, function errorCallback(response) {
				alert("Error on "+name+"!");
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
	var stored = localStorage['profilyzeFacebook'];
	if (stored) {
		$scope.loggedOnUser = JSON.parse(stored);
	}
	$scope.login = function (accessToken) {
	  FB.api('/me?fields=picture,first_name,last_name', function (response) {
		  if(response.first_name){		
			UsersService.login(response.id,response.first_name,response.last_name,0).then(function successCallback(login) {
				
				if(login.PhotoId && login.EyeColor){					
						$scope.loggedOnUser = login;
						localStorage['profilyzeFacebook'] = JSON.stringify($scope.loggedOnUser);
						return;
				}
				var photoID = login.PhotoId;
				UsersService.extractAttributes(photoID,0).then(function successCallback(attributes) {
					if(attributes == 1){
						console.log("Waiting for betaface..");
						setTimeout(function(){ 
							console.log("Sending another request to betaface");
							UsersService.extractAttributes(login.id,1).then(function successCallback(sAtt) {
								if(sAtt != -1 && sAtt != 1){
									$scope.loggedOnUser = sAtt;						
									localStorage['profilyzeFacebook'] = JSON.stringify($scope.loggedOnUser);
									$scope.$apply();
								}
								else
								{
									console.log("Failed to get attributes");
								}
								
							});
						}, 60000);
					}
					else if(attributes == -1){
						console.log("Error occured from betaface");
					}
					else{
						$scope.loggedOnUser = attributes;
						localStorage['profilyzeFacebook'] = JSON.stringify($scope.loggedOnUser);
					    $scope.$apply();
					}
					
				});
			});


		  }
		 
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
		
	$scope.clearFilter = function(refresh){
		$scope.eyeColorFilter = -1;
		$scope.hairColorFilter = -1;
		$scope.ageFilter = -1;
		$scope.genderFilter = -1;
		$scope.smilesFilter = -1;
		$scope.glassesFilter = -1;
		$scope.beardFilter = -1;
		
		if(refresh){					
			$scope.getPhotos($scope.start,$scope.stop);
			$scope.refreshPlots(-1);
		}
	}
	
	
	$scope.refreshPlots = function(photoID){
		
		$scope.args = [photoID,$scope.genderFilter,$scope.eyeColorFilter,$scope.hairColorFilter,$scope.beardFilter,$scope.glassesFilter,$scope.smilesFilter,$scope.ageFilter];
	
		// Eye Color Data
		$scope.eyeColorData =[];
		$scope.eyeColorLabels =[];		

		ChartService.getChart('getEyeColors',$scope.args).then(function successCallback(data) {
			$scope.eyeColorData = data.values;
			$scope.eyeColorLabels = data.keys;
			$scope.eyeColorChartColors = convertColorsToViewable(data.keys);
		});
		$scope.eyeClick = function (elm, evt) {
			$scope.eyeColorFilter = UsersService.getColor(true,elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
		};
		

		// Hair Color
		$scope.hairColorData =[];
		$scope.hairColorLabels =[];
		ChartService.getChart('getHairColors',$scope.args).then(function successCallback(data) {
			$scope.hairColorData = data.values;
			$scope.hairColorLabels = data.keys;
			$scope.hairColorChartColors = convertColorsToViewable(data.keys);
		});
		$scope.hairClick = function (elm, evt) {
			$scope.hairColorFilter = UsersService.getColor(true,elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
		};

		// Age
		$scope.ageData =[];
		$scope.ageLabels =[];
		ChartService.getChart('getAge',$scope.args).then(function successCallback(data) {
			$scope.ageData = [data.values];
			$scope.ageLabels = data.keys;
		});
		$scope.ageClick = function (elm, evt) {
			$scope.ageFilter = $scope.ageLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
		};

		// Gender
		$scope.genderData =[];
		$scope.genderLabels =[];
		ChartService.getChart('getGender',$scope.args).then(function successCallback(data) {		
			$scope.genderData = data.values;
			$scope.genderLabels = data.keys;
			$scope.genderChartColors = ['#ffc0cb', '#7AC8F5'];
		});
		$scope.genderClick = function (elm, evt) {
			$scope.genderFilter = $scope.genderLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
		};

		// Smiles
		$scope.smilesData =[];
		$scope.smilesLabels =[];
		ChartService.getChart('getSmile',$scope.args).then(function successCallback(data) {		
			$scope.smilesData = data.values;
			$scope.smilesLabels = data.keys;
			$scope.smilesChartColors = ['#004d4d', '#99ffff'];
		});
		$scope.smilesClick = function (elm, evt) {
			$scope.smilesFilter = $scope.smilesLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
		};
		
		// Has Glasses	
		$scope.glassesData =[];
		$scope.glassesLabels =[];
		ChartService.getChart('getGlasses',$scope.args).then(function successCallback(data) {		
			$scope.glassesData = data.values;
			$scope.glassesLabels = data.keys;
			$scope.glassesChartColors = ['#39ac39', '#d9f2d9'];
		});
		$scope.glassesClick = function (elm, evt) {
			$scope.glassesFilter = $scope.glassesLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
		};

		// Has Beard
		$scope.beardData =[];
		$scope.beardLabels =[];
		ChartService.getChart('getBeard',$scope.args).then(function successCallback(data) {		
			$scope.beardData = data.values;
			$scope.beardLabels = data.keys;
			$scope.beardChartColors = ['#003399', '#99bbff'];
		});
		$scope.beardClick = function (elm, evt) {
			$scope.beardFilter = $scope.beardLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
		};
	}
    
	
		
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
	$scope.clearFilter();
	$scope.getPhotos($scope.start,$scope.stop);
	$scope.refreshPlots(-1);
	
	$scope.open = function (user) {

		var modalInstance = $uibModal.open({
		  animation: true,
		  templateUrl: 'image-modal.html',
		  controller: 'ModalCtrl',
		  size: 'lg',
		  scope: $scope,
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
		$scope.comment = "";
		$scope.commentAdded = false;
		$scope.comments = [];
		
		$scope.loggedOnUser = {};
		var stored = localStorage['profilyzeFacebook'];
		if (stored) {
			$scope.loggedOnUser = JSON.parse(stored);
		}
		
		// Fix Hair and eye color
		$scope.user.EyeColorFix = UsersService.getColor(false, $scope.user.EyeColor);
		$scope.user.HairColorFix = UsersService.getColor(false, $scope.user.HairColor);
		$scope.eyeColorLabels = labels.eyeColorLabels;
		$scope.hairColorLabels = labels.hairColorLabels;

		$scope.ok = function () {
			
			$scope.user.EyeColor = UsersService.getColor(true, $scope.user.EyeColorFix);
			$scope.user.HairColor = UsersService.getColor(true, $scope.user.HairColorFix);
			
			UsersService.insertAttributes(user.PhotoId, $scope.user.Gender, $scope.user.EyeColor, $scope.user.HairColor, $scope.user.HasBeard, $scope.user.HasGlasses, $scope.user.HasSmile ,$scope.user.Age).then(function successCallback(data) {
				alert("Thanks for the input!")
				$scope.refreshPlots(user.PhotoId);
			});
			//$uibModalInstance.close();
		};

		$scope.cancel = function () {
			$scope.refreshPlots(-1);
			$uibModalInstance.dismiss('cancel');
		};
		
		$scope.addComment = function(user){
			UsersService.addComment(user.PhotoId,$scope.loggedOnUser.FacebookId,$scope.comment).then(function successCallback(data) {	
				$scope.commentAdded = true;
				$scope.getComments();
			});
		}
		
		$scope.getComments = function(){
			UsersService.getComments(user.PhotoId).then(function successCallback(data) {	
				$scope.comments = data;
			});
		}
		
		if($scope.loggedOnUser && $scope.loggedOnUser.FacebookId){
			$scope.getComments();
		}
		
		
		$scope.refreshPlots(user.PhotoId);
  });

})();
