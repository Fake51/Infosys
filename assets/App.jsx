import React, { PureComponent } from "react";
import { Provider } from "react-redux";
import { BrowserRouter as Router } from "react-router-dom";
import configureStore from "./store";
import Navigation from "./Navigation.jsx";
import Content from "./Content.jsx";

class App extends PureComponent {
  render() {
    return (
      <Provider store={configureStore()}>
        <Router>
          <Navigation />
          <Content />
        </Router>
      </Provider>
    );
  }
}

export default App;
