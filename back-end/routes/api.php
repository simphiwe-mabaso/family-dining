<?php

use Core\Router;

$router = Router::getInstance();

// Authentication routes
$router->post('/api/v1/auth/register', 'AuthController@register');
$router->post('/api/v1/auth/login', 'AuthController@login');
$router->post('/api/v1/auth/logout', 'AuthController@logout');
$router->post('/api/v1/auth/refresh', 'AuthController@refresh');
$router->post('/api/v1/auth/forgot-password', 'AuthController@forgotPassword');
$router->post('/api/v1/auth/reset-password', 'AuthController@resetPassword');

// User routes (protected)
$router->group(['middleware' => 'auth'], function($router) {
    $router->get('/api/v1/users/profile', 'UserController@profile');
    $router->put('/api/v1/users/profile', 'UserController@updateProfile');
    $router->get('/api/v1/users/family-members', 'UserController@getFamilyMembers');
    $router->post('/api/v1/users/family-members', 'UserController@addFamilyMember');
    $router->put('/api/v1/users/family-members/{id}', 'UserController@updateFamilyMember');
    $router->delete('/api/v1/users/family-members/{id}', 'UserController@deleteFamilyMember');
    
    // Restaurant routes
    $router->get('/api/v1/restaurants', 'RestaurantController@index');
    $router->get('/api/v1/restaurants/search', 'RestaurantController@search');
    $router->get('/api/v1/restaurants/{id}', 'RestaurantController@show');
    $router->get('/api/v1/restaurants/{id}/reviews', 'RestaurantController@reviews');
    $router->post('/api/v1/restaurants/{id}/reviews', 'RestaurantController@addReview');
    $router->get('/api/v1/restaurants/{id}/deals', 'RestaurantController@deals');
    
    // Recipe routes
    $router->get('/api/v1/recipes', 'RecipeController@index');
    $router->get('/api/v1/recipes/search', 'RecipeController@search');
    $router->get('/api/v1/recipes/{id}', 'RecipeController@show');
    $router->post('/api/v1/recipes/ai-recommendations', 'RecipeController@aiRecommendations');
    
    // Meal planning routes
    $router->get('/api/v1/meal-plans', 'MealPlanController@index');
    $router->post('/api/v1/meal-plans', 'MealPlanController@create');
    $router->get('/api/v1/meal-plans/{id}', 'MealPlanController@show');
    $router->put('/api/v1/meal-plans/{id}', 'MealPlanController@update');
    $router->delete('/api/v1/meal-plans/{id}', 'MealPlanController@delete');
    
    // Shopping list routes
    $router->get('/api/v1/shopping/list', 'ShoppingController@list');
    $router->post('/api/v1/shopping/generate', 'ShoppingController@generate');
    $router->post('/api/v1/shopping/checkout', 'ShoppingController@checkout');
});

// GraphQL endpoint
$router->post('/graphql', 'GraphQLController@handle');