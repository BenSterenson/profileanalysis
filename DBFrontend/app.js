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
  app.service('HistoryService', function ($http) {
		this.insertHistory = function (facebookId,attributeName,filterValue,sessionId) {
          return $http.get('../php/api/InsertHistory/'+facebookId+'/'+attributeName+'/'+filterValue+'/'+sessionId).then(function successCallback(response) {
				return response;
			}, function errorCallback(response) {
				alert("Error on insertHistory!");
			});
		}
		
		this.getHistory = function (facebookId) {
          return $http.get('../php/api/getHistory/'+facebookId).then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on getHistory!");
			});
		}
  })
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
	  
	  this.setPhotoRatings = function(photoID, isHot, facebookId){
		   return $http.get('../php/api/setPhotoRatings/'+photoID+'/'+isHot+'/'+facebookId).then(function successCallback(response) {
				return response;
			}, function errorCallback(response) {
				alert("Error on setPhotoRatings!");
			});
	  }
	  
	  
	  this.getPhotoRatings = function(photoID){
		   return $http.get('../php/api/getPhotoRatings/'+photoID).then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on getPhotoRatings!");
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
				alert("Error on login!");
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
    app.service('WallOfFameService', function ($http) {
  
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
	  
	  this.getMostAccurate = function(){
		   return $http.get('../php/api/most_accurate').then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on getMostAccurate!");
			});
	  }
	  
	  this.getMostLiked = function(isMan){
		   return $http.get('../php/api/getMostLiked/10/'+isMan).then(function successCallback(response) {
				return JSON.parse(response.data);
			}, function errorCallback(response) {
				alert("Error on getMostLiked!");
			});
	  }
	  
  })
  
  app.controller('MainCtrl', function ($scope,$http, $uibModal, ChartService,UsersService,HistoryService) {

	// Init
	$scope.size = 12;
	$scope.start = 0;
	$scope.stop = $scope.size;
	
	// Handle Facebook
	$scope.loggedOnUser = {};
	var stored = localStorage['profilyzeFacebook'];
	if (stored && stored != "undefined") {
		$scope.loggedOnUser = JSON.parse(stored);
	}
	$scope.photoID = 0;
	$scope.login = function (accessToken) {
	  FB.api('/me?fields=picture,first_name,last_name', function (response) {
		  if(response.first_name){		
			UsersService.login(response.id,response.first_name,response.last_name,0).then(function successCallback(login) {

				if(login.FacebookId){					
						$scope.loggedOnUser = login;
						localStorage['profilyzeFacebook'] = JSON.stringify($scope.loggedOnUser);
				}
				$scope.photoID = login.Id;
				UsersService.extractAttributes($scope.photoID,0).then(function successCallback(attributes) {
					if(attributes == 1){
						console.log("Waiting for betaface..");
						setTimeout(function(){ 
							console.log("Sending another request to betaface");
							UsersService.extractAttributes($scope.photoID,1).then(function successCallback(sAtt) {
								if(sAtt != -1 && sAtt != 1){
									$scope.loggedOnUser = sAtt;						
									localStorage['profilyzeFacebook'] = JSON.stringify($scope.loggedOnUser);
									//$scope.$apply();
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
					    //$scope.$apply();
					}
					
				});
			});


		  }
		 
	  });
	  
	}

      // Load the data

	function generateUUID(){
		var d = new Date().getTime();
		if(window.performance && typeof window.performance.now === "function"){
			d += performance.now(); //use high-precision timer if available
		}
		var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
			var r = (d + Math.random()*16)%16 | 0;
			d = Math.floor(d/16);
			return (c=='x' ? r : (r&0x3|0x8)).toString(16);
		});
		return uuid;
	}
	
	$scope.sessionId = generateUUID();
	
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
		$scope.sessionId = generateUUID();
		
		if(refresh){		
			$scope.start = 0;
			$scope.stop = $scope.size;
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
			if($scope.loggedOnUser.FacebookId){
				HistoryService.insertHistory($scope.loggedOnUser.FacebookId,"Eye",elm[0].label,$scope.sessionId);
			}
			
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
			if($scope.loggedOnUser.FacebookId){
				HistoryService.insertHistory($scope.loggedOnUser.FacebookId,"Hair",elm[0].label,$scope.sessionId);
			}
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
			if($scope.loggedOnUser.FacebookId){
				HistoryService.insertHistory($scope.loggedOnUser.FacebookId,"Age",elm[0].label,$scope.sessionId);
			}
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
			if($scope.loggedOnUser.FacebookId){
				HistoryService.insertHistory($scope.loggedOnUser.FacebookId,"Gender",elm[0].label,$scope.sessionId);
			}
		};

		// Smiles
		$scope.smilesData =[];
		$scope.smilesLabels =[];
		ChartService.getChart('getSmile',$scope.args).then(function successCallback(data) {		
			$scope.smilesData = data.values;
			$scope.smilesLabels = data.keys;
			$scope.smilesChartColors = ['#d3d3d3', '#007FFF'];
		});
		$scope.smilesClick = function (elm, evt) {
			$scope.smilesFilter = $scope.smilesLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
			if($scope.loggedOnUser.FacebookId){
				HistoryService.insertHistory($scope.loggedOnUser.FacebookId,"Smiles",elm[0].label,$scope.sessionId);
			}
		};
		
		// Has Glasses	
		$scope.glassesData =[];
		$scope.glassesLabels =[];
		ChartService.getChart('getGlasses',$scope.args).then(function successCallback(data) {		
			$scope.glassesData = data.values;
			$scope.glassesLabels = data.keys;
			$scope.glassesChartColors = ['#d3d3d3', '#007FFF'];
		});
		$scope.glassesClick = function (elm, evt) {
			$scope.glassesFilter = $scope.glassesLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
			if($scope.loggedOnUser.FacebookId){
				HistoryService.insertHistory($scope.loggedOnUser.FacebookId,"Glasses",elm[0].label,$scope.sessionId);
			}
		};

		// Has Beard
		$scope.beardData =[];
		$scope.beardLabels =[];
		ChartService.getChart('getBeard',$scope.args).then(function successCallback(data) {		
			$scope.beardData = data.values;
			$scope.beardLabels = data.keys;
			$scope.beardChartColors = ['#d3d3d3', '#007FFF'];
			
		});
		$scope.beardClick = function (elm, evt) {
			$scope.beardFilter = $scope.beardLabels.indexOf(elm[0].label);
			$scope.getPhotos();
			$scope.refreshPlots(-1);
			if($scope.loggedOnUser.FacebookId){
				HistoryService.insertHistory($scope.loggedOnUser.FacebookId,"Beard",elm[0].label,$scope.sessionId);
			}
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
		  controller: 'ImageModalCtrl',
		  size: 'lg',
		  backdrop: 'static',
		  scope: $scope,
		  resolve: {
			user: function () {
			  return user;
			}
		  }
		}).result.then(function(){ $scope.refreshPlots(-1); },function(){ $scope.refreshPlots(-1); });

	  };
	  
	$scope.openHistory = function (user) {

		var modalInstance = $uibModal.open({
		  animation: true,
		  templateUrl: 'history-modal.html',
		  controller: 'HistoryModalCtrl',
		  size: 'lg',
		  backdrop: 'static',
		  scope: $scope,
		  resolve: {
		  }
		}).result.then(function(){ $scope.refreshPlots(-1); },function(){ $scope.refreshPlots(-1); });


	  };

});
  app.controller('ImageModalCtrl', function ($scope,$http, $uibModalInstance, UsersService, user) {
		
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
		
		$scope.ratedSuccess = false;
		
		$scope.rate = function(isHot){
			if(isHot){
				$scope.curHot +=1;
			}
			else
			{
				$scope.curNot +=1;
			}
			UsersService.setPhotoRatings(user.PhotoId,$scope.curHot,$scope.loggedOnUser.FacebookId).then(function successCallback(data) {	
				$scope.ratedSuccess = true;
			});
		}
		
		$scope.curPhotoRatings = function(){
			UsersService.getPhotoRatings(user.PhotoId).then(function successCallback(data) {	
				$scope.curHot = data.Hot;
				$scope.curNot = data.Not;
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
		
		$scope.isAgeEmpty = function(){
			for(var i in $scope.ageData[0]){
				if($scope.ageData[0][i] != "0"){
					return false;
				}
			}
			return true;
		}
		$scope.refreshPlots(user.PhotoId);
		$scope.curPhotoRatings();
  });

   app.controller('HistoryModalCtrl', function ($scope,$http, $uibModalInstance, UsersService,HistoryService) {
				
		$scope.loggedOnUser = {};
		var stored = localStorage['profilyzeFacebook'];
		if (stored != "undefined") {
			$scope.loggedOnUser = JSON.parse(stored);
		}
		
		HistoryService.getHistory($scope.loggedOnUser.FacebookId).then(function successCallback(data){
			
			var colorArr = ['#D9EDF7','white']
			var colorInd = 0;
			var currentSessionId = 0;
			var order = 1;
			for(var i in data){
				if(currentSessionId != data[i].SessionId){
					currentSessionId = data[i].SessionId;
					colorInd = 1 - colorInd;
					order = 1;
				}
				else
				{
					order = order + 1;
				}
				data[i].Order = order;
				data[i].BackgroundColor = { "background-color" : colorArr[colorInd] }
			}
			
			$scope.historyForUser = data;
		});
		
		$scope.ok = function () {
			
			$uibModalInstance.close();
		};

		$scope.cancel = function () {
			$uibModalInstance.dismiss('cancel');
		};
		
  });
  app.controller('WallOfFameCtrl', function ($scope,$http, WallOfFameService) {
	  $scope.currentPage = 'mostAccurate';
	  $scope.mostAccurate = [];
	  $scope.mostLikedWomen = [];
	  $scope.mostLikedMen = [];

	  
	  WallOfFameService.getMostAccurate().then(function successCallback(data){
		  $scope.mostAccurate = data;
	  });
	  
	  WallOfFameService.getMostLiked(1).then(function successCallback(data){
		  $scope.mostLikedMen = data;
	  });
	  
	  WallOfFameService.getMostLiked(0).then(function successCallback(data){
		  $scope.mostLikedWomen = data;
	  });
  });
})();
