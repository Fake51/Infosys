import React, { PureComponent } from "react";
import { withRouter } from "react-router-dom";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import { createJsonAction } from "../api-action";
import BaseData from "./Edit/BaseData.jsx";
import Loader from "../Loader.jsx";
import styles from "./Edit.scss";

class Edit extends PureComponent {
  constructor(props) {
    super(props);
    this.props = props;

    const {
      match,
      participantEditSchema,
      fetchSchema,
      fetchParticipant,
      participantData
    } = this.props;
    const { participantId } = match.params;

    this.participantId = participantId;

    if (!participantEditSchema || participantEditSchema.length === 0) {
      fetchSchema();
    }

    if (participantId && !participantData) {
      fetchParticipant(participantId);
    }
  }

  render() {
    const { participantEditSchema, participantData } = this.props;

    return (
      <div className={styles.Participant_Edit}>
        {!participantEditSchema || (this.participantId && !participantData) ? (
          <Loader />
        ) : (
          <BaseData />
        )}
      </div>
    );
  }
}

Edit.propTypes = {
  participantEditSchema: PropTypes.shape([]),
  participantData: PropTypes.shape({}),
  fetchParticipant: PropTypes.func,
  fetchSchema: PropTypes.func,
  match: PropTypes.shape({ params: {} })
};

const mapStateToProps = state => {
  const {
    participant: { participantEditSchema, participantData }
  } = state;

  return { participantEditSchema, participantData };
};
const mapDispatchToProps = dispatch => {
  return {
    fetchSchema: () =>
      dispatch(
        createJsonAction(
          {
            endpoint: "/api/v1/participant",
            method: "OPTIONS"
          },
          "PARTICIPANT_EDIT_SCHEMA"
        )
      ),
    fetchParticipant: participantId =>
      dispatch(
        createJsonAction(
          {
            endpoint: `/api/v1/participant/${participantId}`,
            method: "GET"
          },
          "PARTICIPANT_DATA"
        )
      )
  };
};

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Edit));
