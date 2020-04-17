import {
  createAction as middlewareCreateAction,
  getJSON
} from "redux-api-middleware";

export const createAction = (args, actionType) => {
  const request = {
    ...args,
    ...{
      types: [
        "REQUEST",
        {
          type: "SUCCESS",
          payload: (action, state, res) => getJSON(res),
          meta: `${actionType}_SUCCESS`
        },
        {
          type: "FAILURE",
          payload: (action, state, res) =>
            getJSON(res).then(
              json => new Error(res.status, res.statusText, json)
            ),
          meta: `${actionType}_FAILURE`
        }
      ]
    }
  };

  return middlewareCreateAction(request);
};

export const createJsonAction = (args, actionType) => {
  const headers = {
    headers: {
      Accept: "application/json"
    }
  };

  if (args.body) {
    headers.headers["Content-Type"] = "application/json";
  }

  return createAction({ ...args, ...headers }, actionType);
};
