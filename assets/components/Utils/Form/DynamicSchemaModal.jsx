import React from 'react';
import { Modal, ModalHeader, ModalBody, FormGroup, Label, Input, Button } from 'reactstrap';
import Form from '@rjsf/core';
import validator from '@rjsf/validator-ajv8';

const ReactstrapTextWidget = ({ id, required, readonly, disabled, type, value, onChange, onBlur, onFocus, label, schema }) => {
    const inputType = type || (schema.type === 'integer' || schema.type === 'number' ? 'number' : 'text');

    return (
        <FormGroup>
            {/* <Label htmlFor={id}>{label} {required && <span className="text-danger">*</span>}</Label> */}
            <Input
                id={id}
                type={inputType}
                value={value || ''}
                required={required}
                disabled={disabled}
                readOnly={readonly}
                onChange={(event) => onChange(event.target.value === '' ? undefined : event.target.value)}
                onBlur={onBlur && ((event) => onBlur(id, event.target.value))}
                onFocus={onFocus && ((event) => onFocus(id, event.target.value))}
            />
        </FormGroup>
    );
};

const ReactstrapCheckboxWidget = ({ id, value, required, disabled, readonly, label, onChange }) => {
    return (
        <FormGroup check className="mb-3">
            <Input
                id={id}
                type="checkbox"
                checked={typeof value === "undefined" ? false : value}
                required={required}
                disabled={disabled || readonly}
                onChange={(event) => onChange(event.target.checked)}
            />
            <Label check htmlFor={id}>
                {label} {required && <span className="text-danger">*</span>}
            </Label>
        </FormGroup>
    );
};

// --- NEW ENUM WIDGET ---
const ReactstrapSelectWidget = ({ id, options, value, required, disabled, readonly, multiple, onChange, onBlur, onFocus }) => {
    // RJSF automatically extracts enumOptions from the PHP oneOf array
    const { enumOptions, enumDisabled } = options;

    return (
        <FormGroup>
            <Input
                id={id}
                type="select"
                multiple={multiple}
                value={typeof value === 'undefined' ? (multiple ? [] : '') : value}
                required={required}
                disabled={disabled || readonly}
                onChange={(event) => {
                    if (multiple) {
                        const selectedValues = Array.from(event.target.options)
                            .filter(o => o.selected)
                            .map(o => o.value);
                            
                        // Map DOM strings back to original types (int or string)
                        const typedValues = selectedValues.map(val => {
                            const option = enumOptions.find(o => String(o.value) === String(val));
                            return option ? option.value : val;
                        });
                        onChange(typedValues);
                    } else {
                        const newValue = event.target.value;
                        if (newValue === '') {
                            onChange(undefined);
                        } else {
                            // Map single DOM string back to original type
                            const selectedOption = enumOptions.find(o => String(o.value) === String(newValue));
                            onChange(selectedOption ? selectedOption.value : newValue);
                        }
                    }
                }}
                onBlur={onBlur && ((event) => onBlur(id, event.target.value))}
                onFocus={onFocus && ((event) => onFocus(id, event.target.value))}
            >
                {!multiple && !required && <option value="">Select...</option>}
                
                {enumOptions && enumOptions.map(({ value: optionValue, label: optionLabel }, i) => {
                    const isDisabled = enumDisabled && enumDisabled.indexOf(optionValue) !== -1;
                    return (
                        <option key={i} value={optionValue} disabled={isDisabled}>
                            {optionLabel}
                        </option>
                    );
                })}
            </Input>
        </FormGroup>
    );
};

// Map all widgets to the RJSF engine
const customWidgets = {
    TextWidget: ReactstrapTextWidget,
    CheckboxWidget: ReactstrapCheckboxWidget,
    SelectWidget: ReactstrapSelectWidget, // <-- Link the new dropdown widget here
};

export default function DynamicSchemaModal({ isOpen, toggle, schema, onSubmit }) {
    if (!schema) return null;

    return (
        <Modal isOpen={isOpen} toggle={toggle} centered size="lg">
            <ModalHeader toggle={toggle}>
                {schema.title || 'Create New Entry'}
            </ModalHeader>
            <ModalBody>
                <Form 
                    schema={schema} 
                    validator={validator}
                    widgets={customWidgets}
                    onSubmit={({ formData }) => onSubmit(formData)}
                    showErrorList={false}
                >
                    <div className="d-flex justify-content-end mt-4">
                        <Button color="secondary" outline className="me-2" onClick={toggle} type="button">
                            Cancel
                        </Button>
                        <Button color="primary" type="submit">
                            Save {schema.title}
                        </Button>
                    </div>
                </Form>
            </ModalBody>
        </Modal>
    );
}