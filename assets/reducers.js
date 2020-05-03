const defaultState = {
  searchResult: [],
  searchMeta: [],
  participantEditSchema: [],
  participantData: {}
};
const participant = (state = defaultState, action) => {
  switch (action.type) {
    case "PARTICIPANT_SEARCH_SUCCESS":
      return { ...state, searchResult: action.payload };

    case "PARTICIPANT_SEARCH_META_SUCCESS":
      return { ...state, searchMeta: action.payload };

    case "PARTICIPANT_EDIT_SCHEMA_SUCCESS":
      return { ...state, participantEditSchema: action.payload };

    case "PARTICIPANT_EDIT_DATA_SUCCESS":
      return { ...state, participantData: action.payload };

    default:
      return state;
  }
};

const user = (state = {}, action) => {
  switch (action.type) {
    case "USER_LOGIN_SUCCESS":
      return { ...action.payload };
    default:
      return state;
  }
};

export default { participant, user };
