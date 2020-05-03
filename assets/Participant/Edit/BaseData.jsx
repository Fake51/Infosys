import React, { PureComponent } from "react";
import { connect } from "react-redux";
import PropTypes from "prop-types";
import Input from "../../Input";
import Loader from "../../Loader";

class BaseData extends PureComponent {
  render() {
    const { participantData, participantEditSchema } = this.props;
    const base = participantEditSchema.filter(
      group => group.name === "baseData"
    );

    if (base.length === 0) {
      return <Loader />;
    }

    const schema = base[0];

    return (
      <div>
        {schema.fields.map(field => (
          <Input
            key={field.name}
            readOnly={field.readOnly}
            name={field.name}
            label={field.label}
            value={participantData ? participantData[field.name] : ""}
            size={field.size}
          />
        ))}
      </div>
    );
  }
}

BaseData.propTypes = {
  participantData: PropTypes.shape({ id: PropTypes.number }),
  participantEditSchema: PropTypes.shape([])
};

const mapStateToProps = state => {
  const {
    participant: { participantEditSchema, participantData }
  } = state;

  return { participantData, participantEditSchema };
};

export default connect(mapStateToProps)(BaseData);
