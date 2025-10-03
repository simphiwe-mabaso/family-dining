import { configureStore } from '@reduxjs/toolkit';
import authReducer from './reducers/authReducer';
import userReducer from './reducers/userReducer';
import restaurantReducer from './reducers/restaurantReducer';
import recipeReducer from './reducers/recipeReducer';

const store = configureStore({
  reducer: {
    auth: authReducer,
    user: userReducer,
    restaurants: restaurantReducer,
    recipes: recipeReducer,
  },
  devTools: process.env.NODE_ENV !== 'production',
});

export default store;