const defaultState = {
  searchResult: [],
  searchMeta: []
};
const participant = (state = defaultState, action) => {
  switch (action.type) {
    case "PARTICIPANT_SEARCH_SUCCESS":
      return { ...state, searchResult: action.payload };
    case "PARTICIPANT_SEARCH_META_SUCCESS":
      return { ...state, searchMeta: action.payload };
    default:
      return state;
  }
};

export default { participant };
