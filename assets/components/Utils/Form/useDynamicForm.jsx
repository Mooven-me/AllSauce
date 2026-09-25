import React, { useState, useCallback } from 'react';
import DynamicSchemaModal from './DynamicSchemaModal';

export function useDynamicForm(path, onSuccess = null) {
    const [schema, setSchema] = useState(null);
    const [isOpen, setIsOpen] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const openForm = useCallback(() => {
        setIsOpen(true);
        if (!schema) {
            fetch(`/api/form/${path}`)
                .then(res => res.json())
                .then(data => setSchema(data))
                .catch(err => console.error("Failed to load schema", err));
        }
    }, [path, schema]);

    const closeForm = () => setIsOpen(false);

    const handleSubmit = async (formData) => {
        setIsSubmitting(true);
        try {
            const response = await fetch(`/api/form/${path}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                closeForm();
                if (onSuccess) onSuccess(await response.json());
            } else {
                console.error("Validation or server error", await response.text());
            }
        } catch (error) {
            console.error("Network error during submission", error);
        } finally {
            setIsSubmitting(false);
        }
    };

    const FormModal = () => (
        <DynamicSchemaModal 
            isOpen={isOpen}
            toggle={closeForm}
            schema={schema}
            onSubmit={handleSubmit}
        />
    );

    return { FormModal, openForm, closeForm, isSubmitting };
}