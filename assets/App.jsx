import React, { PureComponent } from "react";
import { Provider } from "react-redux";
import { BrowserRouter as Router } from "react-router-dom";
import configureStore from "./store";
import HeaderBar from "./HeaderBar.jsx";
import Navigation from "./Navigation.jsx";
import Content from "./Content.jsx";
import Login from "./Login.jsx";
import styles from "./App.scss";

const store = configureStore();

class App extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;

    store.subscribe(this.handleStoreUpdate.bind(this));
  }

  handleStoreUpdate(arg) {
    console.log(arg);
  }

  render() {
    const { user } = store.getState();

    return (
      <Provider store={store}>
        <Router>
          <HeaderBar />
          <div className={styles.appContainer}>
            <Navigation />
            { user ? <Content /> : <Login /> }
          </div>
        </Router>
      </Provider>
    );
  }
}

export default App;
