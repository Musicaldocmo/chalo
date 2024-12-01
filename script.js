document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('travelContactForm');
    const submitBtn = form.querySelector('.submit-btn');
    
    // Form validation functions
    const validators = {
        name: (value) => {
            return value.length >= 2 ? '' : 'Name must be at least 2 characters long';
        },
        email: (value) => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(value) ? '' : 'Please enter a valid email address';
        },
        subject: (value) => {
            return value.length >= 5 ? '' : 'Subject must be at least 5 characters long';
        },
        message: (value) => {
            return value.length >= 10 ? '' : 'Message must be at least 10 characters long';
        }
    };

    // Real-time validation
    form.querySelectorAll('input, textarea').forEach(field => {
        field.addEventListener('input', () => {
            validateField(field);
        });
        
        field.addEventListener('blur', () => {
            validateField(field);
        });
    });

    function validateField(field) {
        const errorElement = field.parentElement.querySelector('.error-message');
        const errorMessage = validators[field.id](field.value);
        errorElement.textContent = errorMessage;
        return !errorMessage;
    }

    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate all fields
        let isValid = true;
        form.querySelectorAll('input, textarea').forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        if (!isValid) {
            return;
        }

        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'success-message';
                successMessage.textContent = 'Message sent successfully!';
                form.insertBefore(successMessage, form.firstChild);
                
                // Reset form
                form.reset();
                
                // Remove success message after 5 seconds
                setTimeout(() => {
                    successMessage.remove();
                }, 5000);
            } else {
                throw new Error(result.message || 'Failed to send message');
            }
        } catch (error) {
            alert('Error sending message: ' + error.message);
        } finally {
            // Remove loading state
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }
    });

    // Add floating label behavior
    form.querySelectorAll('.form-group input, .form-group textarea').forEach(field => {
        field.addEventListener('focus', () => {
            field.parentElement.classList.add('focused');
        });
        
        field.addEventListener('blur', () => {
            if (!field.value) {
                field.parentElement.classList.remove('focused');
            }
        });
        
        // Check initial state
        if (field.value) {
            field.parentElement.classList.add('focused');
        }
    });
});