import React from 'react';
import axios from 'axios';
import classNames from 'classnames';
import EDConfig from 'ed-config';
import Autosuggest from 'react-autosuggest';

class EDInflectionSelect extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            inflections: [],
            selectedInflections: [],
            groupNames: [],
            value: props.value || '',
            suggestions: []
        };
    }

    componentWillMount() {
        axios.get(EDConfig.api('inflection'))
            .then(resp => this.setInflections(resp.data));
    }

    getSuggestions(data) {
        const name = data.value.toLocaleLowerCase();

        let sections = [];
        for (let groupName of this.state.groupNames) {
            const inflections = this.state.inflections[groupName].filter(
                i => i.name.length >= name.length && i.name.substr(0, name.length) === name &&
                    this.state.selectedInflections.indexOf(i) === -1 // isn't selected!
            );

            if (inflections.length > 0) {
                sections.push({
                    inflections,
                    groupName
                });
            }
        }

        return sections;
    }

    getSuggestionValue(suggestion) {
        return suggestion.name;
    }

    getSectionSuggestions(section) {
        return section.inflections;
    }

    renderSuggestion(suggestion) {
        return <span>{suggestion.name}</span>;
    }

    renderSectionTitle(section) {
        return (
            <strong>{section.groupName}</strong>
        );
    }

    setInflections(inflections) {
        const groupNames = Object.keys(inflections);

        groupNames.forEach(groupName => {
            inflections[groupName].forEach(inflection => {
                inflection.name = inflection.name.toLocaleLowerCase();
            });
        });

        this.setState({
          inflections,
          groupNames
        });
    }

    setValue(value) {
        this.setState({
            value
        });
    }

    onSuggestionSelect(ev, data) {
        ev.preventDefault();

        this.setState({
            selectedInflections: [ ...this.state.selectedInflections, data.suggestion ],
            value: ''
        });
    }

    onSuggestionsFetchRequest(value) {
        this.setState({
            suggestions: this.getSuggestions(value)
        });
    }

    onSuggestionsClearRequest() {
        this.setState({
            suggestions: []
        });
    }

    onInflectionChange(ev, data) {
        this.setState({
            value: data.newValue
        });
    }

    onRemoveInflectionClick(ev, inflection) {
        this.setState({
            selectedInflections: this.state.selectedInflections.filter(i => i.id !== inflection.id)
        });
    }

    render() {
        const inputProps = {
            placeholder: 'Search for an inflection',
            value: this.state.value,
            onChange: this.onInflectionChange.bind(this)
        };

        return <div className="ed-inflection-select">
            <div>
                <Autosuggest 
                    alwaysRenderSuggestions={false} // set to _true_ to view all.
                    multiSection={true}
                    suggestions={this.state.suggestions}
                    onSuggestionsFetchRequested={this.onSuggestionsFetchRequest.bind(this)}
                    onSuggestionsClearRequested={this.onSuggestionsClearRequest.bind(this)}
                    onSuggestionSelected={this.onSuggestionSelect.bind(this)}
                    getSuggestionValue={this.getSuggestionValue.bind(this)}
                    renderSuggestion={this.renderSuggestion.bind(this)}
                    renderSectionTitle={this.renderSectionTitle.bind(this)}
                    getSectionSuggestions={this.getSectionSuggestions.bind(this)}
                    inputProps={inputProps} />
            </div>
            <div>
                {this.state.selectedInflections.map(i => 
                    <span key={i.id}>
                        <a className="label label-default selected-inflection" 
                           onClick={e => this.onRemoveInflectionClick(e, i)}
                           title={`Press on the label (${i.name}) to remove it.`}>
                           {i.name}
                        </a>
                        {' '}
                    </span>
                )}
            </div>
        </div>;
    }
}

EDInflectionSelect.defaultProps = {
    componentName: 'inflection',
    componentId: undefined,
    value: 0
};

export default EDInflectionSelect;