function validatePassword(password) {
    // Password must be at least 8 characters long
    if (password.length < 8) {
        return { success: false, message: 'Password must be at least 8 characters long!' };
    }

    // Password must have at least one uppercase letter
    if (!/[A-Z]/.test(password)) {
        return { success: false, message: 'Password must have at least one uppercase letter!' };
    }

    // Password must have at least one lowercase letter
    if (!/[a-z]/.test(password)) {
        return { success: false, message: 'Password must have at least one lowercase letter!' };
    }

    // Password must have at least one digit
    if (!/\d/.test(password)) {
        return { success: false, message: 'Password must have at least one digit!' };
    }

    // Password must have at least one special character
    if (!/[',@,$,#,!,*,+,\-.,,\,]/.test(password)) {
        return { success: false, message: 'Password must have at least one special character!' };
    }

    return { success: false, message: 'Password passed' };
}
