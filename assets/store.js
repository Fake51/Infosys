import { createStore, applyMiddleware, combineReducers } from "redux";
import { apiMiddleware } from "redux-api-middleware";
import reducers from "./reducers";

const logger = () => next => action => {
  console.log(action);
  return next(action);
};

const transformApiResult = store => next => action => {
  if (
    action &&
    action.type &&
    (action.type === "SUCCESS" || action.type === "FAILURE")
  ) {
    store.dispatch({ ...action, ...{ type: action.meta } });
  }

  return next(action);
};

// const reducer = combineReducers(reducers);
const createStoreWithMiddleware = applyMiddleware(
  apiMiddleware,
  transformApiResult,
  logger
)(createStore);

export default function configureStore(initialState) {
  return createStoreWithMiddleware(combineReducers(reducers), initialState);
}
