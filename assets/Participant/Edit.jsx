import React, { PureComponent } from "react";
import { Switch, Route, withRouter } from "react-router-dom";
import { connect } from "react-redux";
import { createJsonAction } from "../api-action";
import BaseData from "./Edit/BaseData.jsx";
import Loader from "../Loader.jsx";
import styles from "./Edit.scss";

class Edit extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;

    const { participantId } = this.props.match.params;
    this.participantId = participantId;

    if (!this.props.participantEditSchema || this.props.participantEditSchema.length === 0) {
      this.props.fetchSchema();
    }

    if (participantId && !this.props.participantData) {
      this.props.fetchParticipant(participantId);
    }
  }

  render() {
    return (
      <div className={styles.Participant_Edit}>
        {!this.props.participantEditSchema || (this.participantId && !this.props.participantData)
          ? <Loader />
          : <BaseData />
        }
      </div>
    );
  }
}

const mapStateToProps = state => {
  const { participant: { participantEditSchema, participantData } } = state;

  return { participantEditSchema, participantData };
};
const mapDispatchToProps = dispatch => {
  return {
    fetchSchema: () => dispatch(createJsonAction({
      endpoint: "/api/v1/participant",
      method: "OPTIONS"
    }, "PARTICIPANT_EDIT_SCHEMA")),
    fetchParticipant: participantId => dispatch(createJsonAction({
      endpoint: `/api/v1/participant/${participantId}`,
      method: "GET"
    }, "PARTICIPANT_DATA"))
  };
};

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Edit));
