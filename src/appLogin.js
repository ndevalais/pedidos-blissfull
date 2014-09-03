//Define an angular module for our app
var app = angular.module('myApp', []);

app.controller('loginController', function($scope, $http) {
	
	$scope.titulo = "Pedidos EXPOSED";
	$scope.titulo_login = "Lenceria EXPOSED";
	
	$scope.login = function(login) {
		//alert(login.email);	
		location.href = "index.html";
	}
	/*
	getTask(); // Load all available tasks 
	function getTask(){  
		$http.get("http://www.diemp.com.ar/tareas/getTask.php").success(function(data){
		$scope.tasks = data;
	});
	$scope.addTask = function (task) {
		$http.get("http://www.diemp.com.ar/tareas/addTask.php?task="+task).success(function(data){
		getTask();
		$scope.taskInput = "";
	});
	};
	$scope.deleteTask = function (task) {
		if(confirm("Are you sure to delete this line?")){
		$http.get("http://diemp.com.ar/tareas/deleteTask.php?taskID="+task).success(function(data){
		getTask();
	});
	}
	};
	$scope.toggleStatus = function(item, status, task) {
		if(status=='2'){status='0';}else{status='2';}
		$http.get("http://diemp.com.ar/tareas/updateTask.php?taskID="+item+"&status="+status).success(function(data){
		getTask();
	});
	};*/

});
