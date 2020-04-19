import React, { Component } from "react";
import { Provider } from "react-redux";
import { BrowserRouter as Router } from "react-router-dom";
import configureStore from "./store";
import HeaderBar from "./HeaderBar";
import Navigation from "./Navigation";
import ContentWrapper from "./ContentWrapper";
import Login from "./Login";
import styles from "./App.scss";
import { setApiToken } from "./api-action";

const store = configureStore();

class App extends Component {
  constructor(props) {
    super(props);
    this.props = props;

    const { user } = store.getState();
    this.user = user;

    store.subscribe(this.handleStoreUpdate.bind(this, store));
  }

  handleStoreUpdate(store) {
    const { user } = store.getState();

    if (user !== this.user) {
      setApiToken(user && user.token);
      this.user = user;
    }
  }

  render() {
    const { user } = store.getState();

    return (
      <Provider store={store}>
        <Router>
          <HeaderBar />
          <div className={styles.appContainer}>
            <Navigation />
            <ContentWrapper />
          </div>
        </Router>
      </Provider>
    );
  }
}

export default App;
